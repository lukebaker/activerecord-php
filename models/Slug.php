<?php

require_once dirname(__FILE__) .DIRECTORY_SEPARATOR. 'generated_models' .DIRECTORY_SEPARATOR. 'SlugBase.php';
class Slug extends SlugBase {

  protected $belongs_to = array('post');

  function before_save() {
    if ($this->slug == __FUNCTION__)
      $this->slug = $this->slug . '-success';
  }

  function before_create() {
    if ($this->slug == __FUNCTION__)
      $this->slug = $this->slug . '-success';
  }

  function before_update() {
    if ($this->slug == __FUNCTION__)
      $this->slug = $this->slug . '-success';
  }

  function after_save() {
    if ($this->slug == __FUNCTION__)
      $this->slug = $this->slug . '-success';
  }

  function after_create() {
    if ($this->slug == __FUNCTION__)
      $this->slug = $this->slug . '-success';
  }

  function after_update() {
    if ($this->slug == __FUNCTION__)
      $this->slug = $this->slug . '-success';
  }

  function before_destroy() {
    if ($this->slug == __FUNCTION__)
      $this->slug = $this->slug . '-success';
  }

  function after_destroy() {
    if ($this->slug == __FUNCTION__)
      throw new Exception("after_destroy");
  }

}

?>
