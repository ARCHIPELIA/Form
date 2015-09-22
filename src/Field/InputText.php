<?php

namespace Atoll\Form\Field;

use Atoll\Form\AbstractFieldList;

class InputText extends AbstractFieldList
{
  protected $size;
  protected $maxlength;


  /** Constructeur **/
  public function __construct($id, $title = '', $accessKey = '', $size = 25, $maxlength = 100, $default = '')
  {
    parent::__construct($id, $title, $accessKey, $default);

    $this->size = $size;
    $this->maxlength = $maxlength;
  }

  /** parseVal **/
  public function parseVal($value)
  {
    return str_replace(array('<', '>', '"'), '', trim($value));
  }

  /** setSize **/
  public function setSize($size)
  {
    $this->size = $size;
  }

  /** Affiche le champs de saisie texte **/
  public function showField($options = '')
  {
    if (!$this->disabled)
      $this->submited[] = $this->index;

    return '<input type="text" class="input text' . ($this->isValid() ? '' : ' error') . '" id="' . $this->getId() . '" name="' . $this->getName() . '" size="' . $this->size . '" maxlength="' . $this->maxlength . '" value="' . $this->getFieldValue() . '"' . ($this->highlight ? ' onblur="unhighlight(this);" onfocus="highlight(this);"' : '') .
    ($this->readOnly ? ' readonly="true"' : '') . ($this->disabled ? ' disabled="true"' : '') .
    ($this->options != '' ? ' ' . $this->options : '') . ($options != '' ? ' ' . $options : '') . ($this->comment != '' ? ' title="' . $this->comment . '"' : '') . '/>';
  }

  public function displayVal()
  {
    return $this->oraText();
  }

}
