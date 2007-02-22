<?php

require_once dirname(__FILE__) .DIRECTORY_SEPARATOR. 'activerecord' .DIRECTORY_SEPARATOR. 'generated_models' .DIRECTORY_SEPARATOR. 'SlugBase.php';
class Slug extends SlugBase {

  protected $belongs_to = array('post');
}

?>
