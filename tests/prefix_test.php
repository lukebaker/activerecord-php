<?php

require_once '../models/Post.php';
require_once '../models/Author.php';

class TestPrefix extends BaseTest {
  function testPrefixRelationship() {
    $p = Post::find(1);
    $this->AssertEqual($p->author->name, 'Luke');
  }
}

?>
