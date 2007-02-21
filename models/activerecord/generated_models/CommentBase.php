<?php
require_once dirname(__FILE__) .DIRECTORY_SEPARATOR. '..' .DIRECTORY_SEPARATOR. 'ActiveRecord.php';

class CommentBase extends ActiveRecord {

  protected $columns = array('id', 'author', 'body', 'post_id');
  protected $table_name = 'comments';
  protected $primary_key = 'id';

  static function find($id, $options=null) {
    return parent::find(__CLASS__, $id, $options);
  }
}
?>
