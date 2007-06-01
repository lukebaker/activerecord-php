<?php
require_once dirname(__FILE__) .DIRECTORY_SEPARATOR. '..' .DIRECTORY_SEPARATOR. 'activerecord' .DIRECTORY_SEPARATOR. 'ActiveRecord.php';

class SlugBase extends ActiveRecord {

  protected $columns = array('id', 'slug', 'post_id');
  protected $table_name = 'slugs';
  protected $table_vanity_name = 'slugs';
  protected $primary_key = 'id';

  static function find($id, $options=null) {
    return parent::find(__CLASS__, $id, $options);
  }
}
?>
