<?php

require_once '../models/Post.php';
require_once '../models/Comment.php';
require_once '../models/Slug.php';

class TestHasOne extends BaseTest {

  function testGetFromDB() {
    $query_count = Post::get_query_count();
    $p = Post::find(1);
    $this->AssertEqual(Post::get_query_count(), $query_count + 1);
    $this->AssertEqual($p->slug->id, 1);
    $this->AssertEqual(Post::get_query_count(), $query_count + 2);
    $this->AssertEqual($p->slug->slug, 'first-post');
    $this->AssertEqual($p->slug->post_id, 1);
    $this->AssertEqual(Post::get_query_count(), $query_count + 2);
  }
  function testGetViaJoin() {
    $query_count = Post::get_query_count();
    $p = Post::find(1, array('include' => 'slug'));
    $this->AssertEqual(Post::get_query_count(), $query_count + 1);
    $this->AssertEqual($p->slug->id, 1);
    $this->AssertEqual($p->slug->slug, 'first-post');
    $this->AssertEqual($p->slug->post_id, 1);
    $this->AssertEqual(Post::get_query_count(), $query_count + 1);
  }

  function testGetViaJoinWithNoResults() {
    $query_count = Post::get_query_count();
    $p = Post::find(2, array('include' => 'slug'));
    $this->AssertEqual(Post::get_query_count(), $query_count + 1);
    $this->AssertNull($p->slug);
  }

  function testSetWithParentSaveButNotChild() {
    $query_count = Post::get_query_count();
    $p = Post::find(1);
    $this->AssertEqual(Post::get_query_count(), $query_count + 1);
    $s = new Slug(array('slug' => 'first-post-edited'));
    $this->AssertEqual($s->slug, 'first-post-edited');
    $this->AssertNotEqual($s->post_id, 1);
    $p->slug = $s;
    $this->AssertEqual(Post::get_query_count(), $query_count + 2);
    $this->AssertEqual($p->slug->post_id, 1);
    $this->AssertEqual($p->slug->slug, 'first-post-edited');
    $this->AssertTrue(is_numeric($p->slug->id));
    $this->AssertEqual(Post::get_query_count(), $query_count + 2);
  }

  function testSetWithNeitherSaved() {
    $query_count = Post::get_query_count();
    $p = new Post(array('title' => 'Some post'));
    $s = new Slug(array('slug' => 'first-post-edited'));
    $p->slug = $s;
    $this->AssertEqual($p->slug->post_id, null);
    $this->AssertEqual($p->slug->slug, 'first-post-edited');
    $this->AssertEqual(Post::get_query_count(), $query_count + 0);
  }
  
  function test_SetWithOnlyChildSaved() {
    $query_count = Post::get_query_count();
    $s = Slug::find('first');
    $p = new Post(array('title' => 'Some post'));
    $p->slug = $s;
    $this->AssertTrue($p->slug->is_modified());
    $this->AssertEqual(Post::get_query_count(), $query_count + 1);
  }

  function test_GetWhenParentIsUnsaved() {
    $query_count = Post::get_query_count();
    $p = new Post(array('title' => 'Some post'));
    $this->AssertEqual($p->slug, null);
    $this->AssertEqual(Post::get_query_count(), $query_count + 0);
  }

  function test_CascadingSaveNewRecords() {
    $query_count = Post::get_query_count();
    $p = new Post(array('title' => 'some post'));
    $s = new Slug(array('slug'  => 'some-post'));
    $p->slug = $s;
    $p->save();
    $this->AssertEqual(Post::get_query_count(), $query_count + 2);
    $this->AssertEqual($p->slug->post_id, $p->id);
    $s1 = Slug::find($p->slug->id);
    $this->AssertEqual($s1, $p->slug);
  }

  function test_CascadingSaveUpdate() {
    $query_count = Post::get_query_count();
    $p = Post::find(1, array('include' => 'slug'));
    $this->AssertEqual(Post::get_query_count(), $query_count + 1);
    $p->slug->slug = 'some-post';
    $p->save();
    $this->AssertEqual(Post::get_query_count(), $query_count + 2);
    $s1 = Slug::find($p->slug->id);
    $this->AssertEqual($s1, $p->slug);
  }

}

?>
