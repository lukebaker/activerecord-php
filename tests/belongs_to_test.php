<?php

require_once '../models/Post.php';
require_once '../models/Comment.php';

class TestBelongsTo extends BaseTest {
  function test_SetWithNeitherSaved() {
    $query_count = Post::get_query_count();
    $p = new Post( array('id' => 2) );
    $c = new Comment( array('post_id' => 1) );
    $this->AssertEqual($c->post_id, 1);
    $c->post = $p;
    $this->AssertEqual($c->post_id, null);
    $this->AssertEqual(Post::get_query_count(), $query_count + 0);
  }

  function test_SetWithChildSaved() {
    $query_count = Post::get_query_count();
    $p = Post::find(1);
    $c = new Comment(array('body' => 'Some comment'));
    $c->post = $p;
    $this->AssertEqual($c->post_id, 1);
    $this->AssertEqual(Post::get_query_count(), $query_count + 1);
  }

  function test_SetWithParentSaved() {
    $query_count = Post::get_query_count();
    $c = Comment::find(1);
    $p = new Post(array('body' => 'Some post'));
    $c->post = $p;
    $this->AssertEqual($c->post_id, null);
    $this->AssertEqual(Post::get_query_count(), $query_count + 1);
  }

  function testAssocEqualBadType() {
    $c = new Comment( array('post_id' => 1) );
    try {
      $c->post = 2;
      $this->fail();
    }
    catch (Exception $e) {
      $this->AssertEqual($e->getCode(), ActiveRecordException::UnexpectedClass);
      $this->pass();
    }
  }

  function testAssocGetFromCache() {
    $query_count = Post::get_query_count();
    $p = new Post( array('id' => 2) );
    $c = new Comment( array('post_id' => 1) );
    $c->post = $p;
    $this->AssertEqual($p, $c->post);
    $this->AssertEqual(Post::get_query_count(), $query_count);
  }

  function testAssocGetFromDB() {
    $query_count = Post::get_query_count();
    $p = Post::find(1);
    $this->AssertEqual(Post::get_query_count(), $query_count + 1);
    $c = Comment::find(1);
    $this->AssertEqual(Post::get_query_count(), $query_count + 2);
    $this->AssertTrue($c->post instanceof Post);
    $this->AssertEqual(Post::get_query_count(), $query_count + 3);
    $this->AssertEqual($c->post, $p);
    $this->AssertEqual(Post::get_query_count(), $query_count + 3);
  }

  function testFindWithJoin() {
    $query_count = Post::get_query_count();
    $c = Comment::find(1, array('include' => 'post'));
    $this->AssertEqual(Post::get_query_count(), $query_count + 1);
    
    $this->AssertEqual($c->post->id, 1);
    $this->AssertEqual($c->post->title, "First Post");
    $this->AssertEqual($c->post->body, "Hi, this is my first post. Enjoy!");
    $this->AssertEqual(Post::get_query_count(), $query_count + 1);
  }

  function test_NeedsSaving() {
    $c = Comment::find(1, array('include' => 'post'));
    $this->AssertFalse($c->post_needs_saving());
    $c->post->title = "Foo bar";
    $this->AssertTrue($c->post_needs_saving());
    $c = Comment::find(1, array('include' => 'post'));
    $this->AssertFalse($c->post_needs_saving());
    $c->post = new Post(array('body' => 'some post'));
    $this->AssertTrue($c->post_needs_saving());
  }

  function test_CascadingSaveNew() {
    $c = Comment::find(1, array('include' => 'post'));
    $orig_id = $c->post_id;
    $c->post = new Post(array('body' => 'some post'));
    $c->save();
    $this->AssertEqual($c->post->id, $c->post_id);
    $this->AssertEqual($c->post->body, 'some post');
    $this->AssertNotEqual($c->post_id, $orig_id);
    $c = Comment::find(1, array('include' => 'post'));
    $this->AssertEqual($c->post->id, $c->post_id);
    $this->AssertEqual($c->post->body, 'some post');
    $this->AssertNotEqual($c->post_id, $orig_id);
  }

  function test_CascadingSaveExisting() {
    $c = Comment::find(1, array('include' => 'post'));
    $c->post->body = 'some post';
    $c->save();
    $this->AssertEqual($c->post->id, $c->post_id);
    $this->AssertEqual($c->post->body, 'some post');
    $c = Comment::find(1, array('include' => 'post'));
    $this->AssertEqual($c->post->id, $c->post_id);
    $this->AssertEqual($c->post->body, 'some post');
  }

}

?>
