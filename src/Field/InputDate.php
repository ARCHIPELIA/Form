<?php

namespace Atoll\Form\Field;

class InputDate extends InputText
{
  public function __construct($id, $title = '', $accessKey = '', $size = 25, $maxlength = 100, $default = '')
  {
    parent::__construct($id, $title, $accessKey, $size, $maxlength, $default);

    $this->setDefault($default);
  }

  /** setDefault **/
  public function setDefault($default = '', $format = 'd/m/Y')
  {
    $date = new \DateTime($default);
    $default = $date->format($format);

    parent::setDefault($default);
  }
}
