<?php

namespace Atoll\Form\Field;

use Atoll\Form\AbstractField;

class InputCheckbox extends AbstractField
{
  /** Constructeur **/
  public function __construct($id, $title = '', $accessKey = '', $default = false)
  {
    parent::__construct($id, $title, $accessKey, $default);
  }

  /* parseVal */
  public function parseVal($value)
  {
    return ($value == true);
  }

  /* getFieldValue */
  protected function getFieldValue()
  {
    return ($this->value ? '1' : '0');
  }

  /* setDefault */
  public function setDefault($default = false)
  {
    if (isset($_POST["chk_$this->name"])) {
      $this->value = isset($_POST[$this->name]) ? ($_POST[$this->name] == '1') : false;
    } else
      $this->value = $this->parseVal($default);
  }

  /* isSubmited */
  public function isSubmited()
  {
    return isset($_POST["chk_$this->name"]);
  }


  /** Affiche le champs de saisie texte **/
  public function showField($options = '')
  {
    return '<input type="checkbox" class="input checkbox' . ($this->readOnly ? ' check_readonly' : '') . '" id="' . $this->getId() . '" name="' . $this->getName() . '" value="1"' .
    ($this->value ? ' checked="true"' : '') . ($this->readOnly ? ' onclick="this.checked = ' . ($this->value ? 'true' : 'false') . ';"' : '') .
    ($this->disabled ? ' disabled="true"' : '') . ($this->options != '' ? ' ' . $this->options : '') . ($options != '' ? ' ' . $options : '') . '/>
            <input type="hidden" id="chk_' . $this->getId() . '" name="chk_' . $this->getName() . '" value="1"/>';
  }

  public function showHiddenField()
  {
    return '<input type="hidden" id="' . $this->getId() . '" name="' . $this->getName() . '" value="' . $this->getFieldValue() . '"/>
            <input type="hidden" id="chk_' . $this->getId() . '" name="chk_' . $this->getName() . '" value="1"/>';
  }

  public function toArray($type = 'VAL')
  {
    if ($type == 'VAL')
      return array($this->getName() => ($this->value ? '1' : '0'));
    else if ($type == 'KEY')
      return array('chk_' . $this->getName() => '1');

    return array();
  }

  public function isChecked()
  {
    return $this->value;
  }

  /** Valeur utilisable par Oracle **/
  public function oraBool()
  {
    return ($this->value ? "1" : "0");
  }

  /** Valeur utilisable par PHP **/
  public function phpBool()
  {
    return $this->value;
  }

  public function validate($type, $requis, $taille_max, &$msg)
  {
    return $this->setValid(true);
  }
}
