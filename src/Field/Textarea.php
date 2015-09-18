<?php

namespace Atoll\Form\Field;

use Atoll\Form\AbstractFieldList;

class Textarea extends AbstractFieldList
{
  protected $cols;
  protected $rows;
  protected $html;

  /** Constructeur **/
  function __construct($id, $title = '', $accessKey = '', $cols = 80, $rows = 8, $default = '', $html = false)
  {
    $this->cols = $cols;
    $this->rows = $rows;
    $this->html = $html;

    parent::__construct($id, $title, $accessKey, $default);
  }

  /** parseVal **/
  public function parseVal($value)
  {
    return $this->html ? trim($value) : str_replace(array('<', '"'), '', trim($value));
  }

  public function addVal($value = '')
  {
    $this->value .= $this->parseVal($value);
  }

  /** Affiche le champs de saisie textarea **/
  public function showField($options = '')
  {
    if (!$this->disabled)
      $this->submited[] = $this->index;

    return '<textarea class="input textarea' . ($this->isValid() ? '' : ' error') . '" id="' . $this->getId() . '" name="' . $this->getName() . '" cols="' . $this->cols . '" rows="' . $this->rows . '"' . ($this->highlight ? ' onblur="unhighlight(this);" onfocus="highlight(this);"' : '') .
    ($this->readOnly ? ' readonly="true"' : '') . ($this->disabled ? ' disabled="true"' : '') . ($this->options != '' ? ' ' . $this->options : '') . ($options != '' ? ' ' . $options : '') . '>' . $this->getFieldValue() .
    '</textarea>';
  }

  /** oraText **/
  public function oraText($html = false)
  {
    return oraText(!$html && !$this->html ? str_replace(array('<', '"'), '', $this->value) : $this->value, $html || $this->html);
  }
}
