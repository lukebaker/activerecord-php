<?php

class BaseTest extends UnitTestCase {
  
  static $fd = array();

   function __construct() {
       require_once dirname(__FILE__). '/../models/activerecord/ActiveRecord.php';
       require_once '../vendors/spyc/spyc.php';
   }
    
   function setUp() {
       $this->reloadTables();
   }
   function loadFixtures() {
     if (count(self::$fd) > 0) return;
     $fixtures = glob('fixtures/*.yml');
     foreach ($fixtures as $fixture) {
       $rows = Spyc::YAMLLoad($fixture);
       $table_name = preg_replace('/^fixtures\/(.*)\.yml$/', '$1', $fixture);
       self::$fd[$table_name] = $rows;
     }
   }

  function reloadTables() {
    self::loadFixtures();

    /* reset auto_increment; INSERT data from yaml files */
    foreach (self::$fd as $table_name => $rows) {
      self::query("DELETE FROM $table_name");
      self::query("ALTER TABLE $table_name AUTO_INCREMENT = 1");
      foreach ($rows as $row) {
        $columns = array();
        $values = array();
        foreach ($row as $column => $value) {
          $columns[] = '`' . $column . '`';
          $values[] = self::quote($value);
        }
        $column_s = implode(", ", $columns);
        $value_s  = implode(", ", $values);
        $query = "INSERT INTO {$table_name} ($column_s) VALUES ($value_s)";
        self::query($query);
      }
    }
  }

  function query($query) { return ActiveRecord::query($query); }
  function quote($value) { return ActiveRecord::quote($value); }
}
?>
