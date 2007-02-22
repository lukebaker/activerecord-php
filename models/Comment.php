<?php
require_once 'activerecord' .DIRECTORY_SEPARATOR. 'generated_models' .DIRECTORY_SEPARATOR. 'CommentBase.php';
class Comment extends CommentBase {

  protected $belongs_to = array('post');
}
?>
