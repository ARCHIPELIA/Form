<?php

namespace Atoll\Form\Field;

use Atoll\Form\AbstractField;

class InputRadio extends AbstractField
{
  /** Attributs **/
  protected $checked;

  /** Constructeur **/
  function __construct($id, $title, $accessKey, $name, $checked = false, $value = '')
  {
    $this->value = ($value == '' ? $id : $value);

    parent::__construct($id, $title, $accessKey, $checked, $name);
  }

  /** setDefault **/
  public function setDefault($checked = false)
  {
    if (isset($_POST[$this->name])) {
      $this->checked = ($this->value == $_POST[$this->name]);
    } else
      $this->checked = $checked;
  }


  /** Affiche le champs de saisie texte **/
  public function showField($options = '')
  {
    return '<input type="radio" class="input radio" id="' . $this->getId() . '" name="' . $this->getName() . '" value="' . $this->getFieldValue() . '"' .
    ($this->checked ? ' checked="true"' : '') . ($this->readOnly || $this->disabled ? ' disabled="true"' : "") .
    ($this->options != '' ? ' ' . $this->options : '') . ($options != '' ? ' ' . $options : '') . '/>';
  }

  public function setChecked($checked = true)
  {
    $this->checked = $checked;
  }

  public function toArray($type = 'VAL')
  {
    if ($type == 'VAL' && $this->isChecked())
      return array($this->getName() => $this->value);
    else if ($type == 'KEY')
      return array($this->getName());

    return array();
  }

  public function isChecked()
  {
    return $this->checked;
  }

  public function validate($type, $requis, $taille_max, &$msg)
  {
    return $this->setValid(true);
  }

  public function oraBool()
  {
    return ($this->checked ? "1" : "0");
  }
}
