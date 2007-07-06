<?php

require_once '../models/Post.php';
require_once '../models/Comment.php';
require_once '../models/Categorization.php';
require_once '../models/Category.php';
class TestHasMany extends BaseTest {
  function testGetHasMany() {
    $query_count = Post::get_query_count();
    $p = Post::find(1);
    $this->AssertEqual(Post::get_query_count(), $query_count + 1);
    $this->AssertEqual($p->comments[0]->id, 1);
    $this->AssertEqual(Post::get_query_count(), $query_count + 2);
    $this->AssertEqual($p->comments[0]->author, 'anon');
    $this->AssertEqual($p->comments[0]->body, 'I enjoyed your post.');
    $this->AssertEqual($p->comments[0]->post_id, 1);
    $this->AssertEqual(Post::get_query_count(), $query_count + 2);

    $this->AssertEqual($p->comments[1]->id, 2);
    $this->AssertEqual($p->comments[1]->author, 'anon');
    $this->AssertEqual($p->comments[1]->body, 'I hated your post.');
    $this->AssertEqual($p->comments[1]->post_id, 1);
    $this->AssertEqual(Post::get_query_count(), $query_count + 2);
    
    $this->AssertEqual(count($p->comments), 2);
    $this->AssertEqual(Post::get_query_count(), $query_count + 2);
  }

  function testJoin() {
    $query_count = Post::get_query_count();
    $p = Post::find(1, '{"include" : "comments"}');
    $this->AssertEqual(Post::get_query_count(), $query_count + 1);

    $this->AssertEqual($p->comments[0]->id, 1);
    $this->AssertEqual($p->comments[0]->author, 'anon');
    $this->AssertEqual($p->comments[0]->body, 'I enjoyed your post.');
    $this->AssertEqual($p->comments[0]->post_id, 1);
    $this->AssertEqual(Post::get_query_count(), $query_count + 1);

    $this->AssertEqual($p->comments[1]->id, 2);
    $this->AssertEqual($p->comments[1]->author, 'anon');
    $this->AssertEqual($p->comments[1]->body, 'I hated your post.');
    $this->AssertEqual($p->comments[1]->post_id, 1);
    $this->AssertEqual(Post::get_query_count(), $query_count + 1);
    
    $this->AssertEqual(count($p->comments), 2);
    $this->AssertEqual(Post::get_query_count(), $query_count + 1);
  }

  function testJoinWithNoResults() {
    $query_count = Post::get_query_count();
    $p = Post::find(2, array('include' => 'comments'));
    $this->AssertEqual(Post::get_query_count(), $query_count + 1);
    $this->AssertEqual(count($p->comments), 0);
  }

  function testThroughGet() {
    $query_count = Post::get_query_count();
    $p = Post::find(1);
    $this->AssertEqual(Post::get_query_count(), $query_count + 1);

    $c = Category::find(1);
    $this->AssertEqual(Post::get_query_count(), $query_count + 2);

    $this->AssertEqual($p->categories[0]->name, 'General');
    $this->AssertEqual(Post::get_query_count(), $query_count + 3);

    $this->AssertEqual($c->posts[0]->id, 1);
    $this->AssertEqual(Post::get_query_count(), $query_count + 4);

    $this->AssertEqual($c->posts[0]->title, 'First Post');
    $this->AssertEqual($c->posts[0]->body, 'Hi, this is my first post. Enjoy!');
    $this->AssertEqual(Post::get_query_count(), $query_count + 4);
  }

  function testThroughWithInclude() {
    $query_count = Post::get_query_count();
    $p = Post::find(1, array('include' => 'categories'));
    $this->AssertEqual(Post::get_query_count(), $query_count + 1);

    $this->AssertEqual($p->categories[0]->name, 'General');
    $this->AssertEqual(Post::get_query_count(), $query_count + 1);
  }

  function testIncludeMultiple() {
    $query_count = Post::get_query_count();
    $p = Post::find(1, array('include' => 'categories, comments'));
    $this->AssertEqual(Post::get_query_count(), $query_count + 1);
    $this->AssertEqual($p->comments[0]->id, 1);
    $this->AssertEqual(Post::get_query_count(), $query_count + 1);
    $this->AssertEqual($p->categories[0]->name, 'General');
    $this->AssertEqual(Post::get_query_count(), $query_count + 1);
  }

  /* set tests for has_many without through */
  function test_SetWithNeitherSaved() {
    $query_count = Post::get_query_count();
    $p = new Post(array('title' => 'post title'));
    $c = new Comment(array('body' => 'some comment'));
    $p->comments_push($c);
    $this->AssertEqual(count($p->comments), 1);
    $this->AssertEqual($p->comments[0], $c);
    $this->AssertEqual(Post::get_query_count(), $query_count + 0);
  }
  function test_SetWithParentSaved() {
    $query_count = Post::get_query_count();
    $p = Post::find(1);
    $this->AssertEqual(Post::get_query_count(), $query_count + 1);

    $p2 = Post::find(1, array('include' => 'comments'));
    $this->AssertEqual(Post::get_query_count(), $query_count + 2);
    $c = new Comment(array('body' => "some comment"));

    $p->comments_push($c);
    $this->AssertEqual(Post::get_query_count(), $query_count + 4);
    $this->AssertEqual($p->comments[count($p->comments) - 1]->post_id, 1);
    $this->AssertEqual(count($p->comments), count($p2->comments) + 1);
    $this->AssertEqual($p->comments[count($p->comments) - 1]->body, "some comment");
    $this->AssertEqual($p->comments[count($p->comments) - 1], $c);
    $this->AssertEqual(Post::get_query_count(), $query_count + 4);
  }

  function test_SetWithChildSaved() {
    $p = new Post(array('body' => 'new post'));
    $c = Comment::find(1);
    $this->AssertFalse($c->is_modified());
    $p->comments_push($c);
    $this->AssertEqual(count($p->comments), 1);
    $this->AssertTrue($p->comments[0]->is_modified());
  }

  function test_SetWithBothSaved() {
    $query_count = Post::get_query_count();
    $p = Post::find(1);
    $this->AssertEqual(Post::get_query_count(), $query_count + 1);
    $p2 = Post::find(1, array('include' => 'comments'));
    $this->AssertEqual(Post::get_query_count(), $query_count + 2);
    $c = Comment::find(self::$fd['comments']['comment_with_no_post']['id']);
    $this->AssertEqual(Post::get_query_count(), $query_count + 3);
    $p->comments_push($c);
    $this->AssertEqual(Post::get_query_count(), $query_count + 5);
    $this->AssertEqual(count($p->comments), count($p2->comments) + 1);
    $this->AssertEqual($c->post_id, 1);
    $this->AssertEqual($p->comments[count($p2->comments)], $c);
    $this->AssertEqual(Comment::find(self::$fd['comments']['comment_with_no_post']['id']), $c);
    $this->AssertEqual(Post::get_query_count(), $query_count + 6);
  }

  /* set tests with through associations */
  function test_SetWithNeitherSavedThrough() {
    $p = new Post(array('title' => 'post title'));
    $c = new Category(array('name' => 'Family'));
    try {
      $p->categories_push($c);
      $this->fail();
    } catch (Exception $e) {
      $this->AssertEqual($e->getCode(), ActiveRecordException::HasManyThroughCantAssociateNewRecords);
      $this->pass();
    }
  }

  function test_SetWithParentSavedThrough() {
    $p = Post::find(1);
    $c = new Category(array('name' => 'Family'));
    try {
      $p->categories_push($c);
      $this->fail();
    } catch (Exception $e) {
      $this->AssertEqual($e->getCode(), ActiveRecordException::HasManyThroughCantAssociateNewRecords);
      $this->pass();
    }
  }

  function test_SetWithChildSavedThrough() {
    $p = new Post(array('title' => 'post title'));
    $c = Category::find(1);
    try {
      $p->categories_push($c);
      $this->fail();
    } catch (Exception $e) {
      $this->AssertEqual($e->getCode(), ActiveRecordException::HasManyThroughCantAssociateNewRecords);
      $this->pass();
    }
  }

  function test_SetWithBothSavedThrough() {
    $j = Categorization::find('all', array('conditions' => "post_id = 2"));
    $this->AssertEqual(count($j), 0);
    $query_count = Post::get_query_count();
    $p = Post::find(2);
    $this->AssertEqual(Post::get_query_count(), $query_count + 1);
    $c = Category::find(1);
    $this->AssertEqual(Post::get_query_count(), $query_count + 2);
    $p->categories_push($c);
    $this->AssertEqual(Post::get_query_count(), $query_count + 4);
    $this->AssertEqual($p->categories[0], $c);
    $j = Categorization::find('all', array('conditions' => "post_id = 2"));
    $this->AssertEqual($j[0]->post_id, 2);
    $this->AssertEqual($j[0]->category_id, 1);
  }

  /* save tests */
  function test_CascadingSave() {
    $query_count = Post::get_query_count();
    $p = Post::find(1, array('include' => 'comments'));
    $this->AssertEqual(Post::get_query_count(), $query_count + 1);
    $p->comments[0]->body = 'foo-bar';
    $p->save();
    $this->AssertEqual(Post::get_query_count(), $query_count + 2);
    $p2 = Post::find(1, array('include' => 'comments'));
    $this->AssertEqual(Post::get_query_count(), $query_count + 3);
    $this->AssertEqual($p2->comments[0], $p->comments[0]);
    $this->AssertEqual(Post::get_query_count(), $query_count + 3);
  }

  function test_CascadingSaveThrough() {
    $query_count = Post::get_query_count();
    $p = Post::find(1, array('include' => 'categories'));
    $this->AssertEqual(Post::get_query_count(), $query_count + 1);
    $p->categories[0]->name = "foo-bar";
    $p->save();
    $this->AssertEqual(Post::get_query_count(), $query_count + 2);
    $p2 = Post::find(1, array('include' => 'categories'));
    $this->AssertEqual($p2->categories[0], $p->categories[0]);
    $this->AssertEqual(Post::get_query_count(), $query_count + 3);
  }

  function test_CascadingSaveNew() {
    $p = new Post(array('title' => 'some post'));
    $c = new Comment(array('body' => 'some comment'));
    $p->comments_push($c);
    $p->save();
    $p2 = Post::find($p->id, array('include' => 'comments'));
    $this->AssertEqual($p->title, $p2->title);
    $this->AssertEqual($p2->comments[0]->body, $p->comments[0]->body);
  }

  function test_GetArrayOfIds() {
    $p = Post::find(1, array('include' => 'comments'));
    $comment_ids = array();
    foreach ($p->comments as $comment)
      $comment_ids[] = $comment->id;
    $this->AssertEqual($p->comment_ids, $comment_ids);
  }

}

?>
