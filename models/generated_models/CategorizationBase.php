<?php
require_once dirname(__FILE__) .DIRECTORY_SEPARATOR. '..' .DIRECTORY_SEPARATOR. 'activerecord' .DIRECTORY_SEPARATOR. 'ActiveRecord.php';

class CategorizationBase extends ActiveRecord {

  protected $columns = array('id', 'post_id', 'category_id');
  protected $table_name = 'categorizations';
  protected $table_vanity_name = 'categorizations';
  protected $primary_key = 'id';

  static function find($id, $options=null) {
    return parent::find(__CLASS__, $id, $options);
  }
}
?>
