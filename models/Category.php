<?php
require_once 'active_record' .DIRECTORY_SEPARATOR. 'generated_models' .DIRECTORY_SEPARATOR. 'CategoryBase.php';
class Category extends CategoryBase {

  protected $has_many = array('categorizations', array('posts' => array('through' => 'categorizations')));
}
?>
