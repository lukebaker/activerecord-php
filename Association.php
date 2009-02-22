<?php
class Association {
  protected $dest_class;
  protected $source_class;
  protected $value;
  protected $options;
  
  function __construct($source, $dest, $options=null) {
    $this->source_class = get_class($source);
    $this->dest_class = ActiveRecordInflector::classify($dest);
    $this->options = $options;
  }

  function needs_saving() {
    if (!$this->value instanceof $this->dest_class)
      return false;
    else
      return $this->value->is_new_record() || $this->value->is_modified();
  }

  function destroy(&$source) {
    if (isset($this->options['dependent']) && $this->options['dependent'] == 'destroy') {
      $this->get($source);
      if (is_array($this->value)) {
        foreach ($this->value as $val)
          $val->destroy();
      }
      else {
        $this->value->destroy();
      }
    }
  }

}

?>
