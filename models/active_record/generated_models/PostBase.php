<?php
require_once dirname(__FILE__) .DIRECTORY_SEPARATOR. '..' .DIRECTORY_SEPARATOR. 'ActiveRecord.php';

class PostBase extends ActiveRecord {

  protected $columns = array('id', 'title', 'body');
  protected $table_name = 'posts';
  protected $primary_key = 'id';

  static function find($id, $options=null) {
    return parent::find(__CLASS__, $id, $options);
  }
}
?>
