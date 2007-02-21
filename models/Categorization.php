<?php
require_once 'active_record' .DIRECTORY_SEPARATOR. 'generated_models' .DIRECTORY_SEPARATOR. 'CategorizationBase.php';
class Categorization extends CategorizationBase {

  protected $belongs_to = array('post', 'category');
}
?>
