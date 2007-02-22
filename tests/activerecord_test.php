<?php

require_once '../models/Post.php';
require_once '../models/Comment.php';
require_once '../models/Category.php';
require_once '../models/Categorization.php';
require_once '../models/Slug.php';

class TestActiveRecord extends BaseTest {
  function test_ConstructorGood() {
    $p = new Post( array('id' => 1) );
    $this->assertEqual($p->id, 1);
  }

  function test_ConstructorBad() {
    try {
      $p = new Post( array('this_does_not_exist' => 1) );
      $this->fail();
    } catch (Exception $e) {
      $this->AssertEqual($e->getCode(), ActiveRecordException::AttributeNotFound);
      $this->pass();
    }
  }

  function test_NoMemoryLeak() {
    for ($i = 0; $i < 5; $i++)
      new Comment();
    $start = memory_get_usage();
    for ($i = 0; $i < 400; $i++)
      new Comment();
    $end = memory_get_usage();
    $this->AssertWithinMargin($start, $end, 15000);
  }

  function test_Constructors() {
    $start_time = microtime(true);
    for ($i = 0; $i < 1000; $i++)
      new Post();
    $end_time = microtime(true);
    # must be able to do at least 500 constructors / second
    $this->AssertTrue((1000 / ($end_time - $start_time)) > 500);
  }

  function test_FindEnsureIsModifiedEqualsFalse() {
    $p = Post::find('first');
    $this->AssertFalse($p->is_modified());
  }

  function test_SimpleInsertViaSave() {
    $p = new Post(array('title' => "Some Title", 'body' => "Lot o' text"));
    $this->AssertTrue($p->is_new_record());
    $p->save();
    $this->AssertFalse($p->is_new_record());
    $p2 = Post::find($p->id);
    $this->AssertEqual($p->id, $p2->id);
    $this->AssertEqual($p->body, $p2->body);
    $this->AssertEqual($p->title, $p2->title);
    $p->destroy();
  }

  function test_SimpleInsertViaSaveWithNull() {
    $p = new Post(array('body' => "Lot o' text"));
    $this->AssertTrue($p->is_new_record());
    $p->save();
    $this->AssertFalse($p->is_new_record());

    $p2 = Post::find($p->id);
    $this->AssertEqual($p->id, $p2->id);
    $this->AssertEqual($p->body, $p2->body);
    $this->AssertEqual($p->title, $p2->title);

    $p2 = Post::find($p->id, array('conditions' => "title is null"));
    $this->AssertEqual($p->id, $p2->id);
    $this->AssertEqual($p->body, $p2->body);
    $this->AssertEqual($p->title, $p2->title);
    $p->destroy();
  }

  function test_FrozenObjectAfterDestroy() {
    $p = new Post(array('body' => "Lot o' text"));
    $p->save();
    $this->AssertFalse($p->is_frozen());
    $p->destroy();
    $this->AssertTrue($p->is_frozen());
    try {
      $p->title = 'Foo bar';
      $this->fail();
    } catch (ActiveRecordException $e) {
      $this->AssertEqual($e->getCode(), ActiveRecordException::ObjectFrozen);
    }
  }
  function test_CascadingDestroy() {
    $p = Post::find(1);
    $p->destroy();
    try {
      Post::find(1);
      $this->fail();
    }
    catch (Exception $e) {
      $this->AssertEqual($e->getCode(), ActiveRecordException::RecordNotFound);
    }
    $c = Comment::find('all', array('conditions' => 'post_id = 1'));
    $this->AssertEqual($c, array());
  }

  function test_before_save() {
    $s = new Slug(array('slug' => 'before_save', 'post_id' => 1));
    $s->save();
    $this->AssertEqual($s->slug, 'before_save-success');
  }

  function test_before_create() {
    $s = new Slug(array('slug' => 'before_create', 'post_id' => 1));
    $s->save();
    $this->AssertEqual($s->slug, 'before_create-success');
  }

  function test_after_create() {
    $s = new Slug(array('slug' => 'after_create', 'post_id' => 1));
    $s->save();
    $this->AssertEqual($s->slug, 'after_create-success');
    $this->AssertEqual(Slug::find($s->id)->slug, 'after_create');
  }

  function test_before_update() {
    $s = Slug::find('first');
    $s->slug = "before_update";
    $s->save();
    $this->AssertEqual($s->slug, 'before_update-success');
  }

  function test_after_update() {
    $s = Slug::find('first');
    $s->slug = "after_update";
    $s->save();
    $this->AssertEqual($s->slug, 'after_update-success');
    $this->AssertEqual(Slug::find('first')->slug, "after_update");
  }

  function test_after_save() {
    $s = new Slug(array('slug' => 'after_save', 'post_id' => 1));
    $s->save();
    $this->AssertEqual($s->slug, 'after_save-success');
    $this->AssertEqual(Slug::find($s->id)->slug, 'after_save');
  }

  function test_before_destroy() {
    $s = Slug::find("first");
    $s->slug = "before_destroy";
    $s->destroy();
    $this->AssertEqual($s->slug, 'before_destroy-success');
  }

  function test_after_destroy() {
    $s = Slug::find("first");
    $s->slug = "after_destroy";
    try {
      $s->destroy();
      $this->fail();
    }
    catch (Exception $e) {
      $this->AssertEqual($e->getMessage(), 'after_destroy');
    }
  }
}

?>
