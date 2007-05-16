<?php
require_once dirname(__FILE__) .DIRECTORY_SEPARATOR. 'generated_models' .DIRECTORY_SEPARATOR. 'CategorizationBase.php';
class Categorization extends CategorizationBase {

  protected $belongs_to = array('post', 'category');
}
?>
