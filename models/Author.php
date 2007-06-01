<?php

require_once dirname(__FILE__) .DIRECTORY_SEPARATOR. 'generated_models' .DIRECTORY_SEPARATOR. 'AuthorBase.php';
class Author extends AuthorBase {
  protected $has_many = array('posts');
}

?>
