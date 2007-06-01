<?php
require_once dirname(__FILE__) .DIRECTORY_SEPARATOR. '..' .DIRECTORY_SEPARATOR. '{$ar_dir}' .DIRECTORY_SEPARATOR. 'ActiveRecord.php';

class {$class_name}Base extends ActiveRecord {

  protected $columns = array({$columns});
  protected $table_name = '{$table_name}';
  protected $table_vanity_name = '{$table_vanity_name}';
  protected $primary_key = '{$primary_key}';

  static function find($id, $options=null) {
    return parent::find(__CLASS__, $id, $options);
  }
}
?>
