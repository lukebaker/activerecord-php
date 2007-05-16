<?php
require_once dirname(__FILE__) .DIRECTORY_SEPARATOR. 'generated_models' .DIRECTORY_SEPARATOR. 'CategoryBase.php';
class Category extends CategoryBase {

  protected $has_many = array('categorizations', array('posts' => array('through' => 'categorizations')));
}
?>
