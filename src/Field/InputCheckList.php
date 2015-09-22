<?php

namespace Atoll\Form\Field;

use Atoll\Form\AbstractFieldList;

class InputCheckList extends AbstractFieldList
{
  protected $chkValue = '1';

  /* Constructeur */
  public function __construct($id, $title = '', $accessKey = '', $default = false)
  {
    parent::__construct($id, $title, $accessKey, $default);
  }

  /* parseVal */
  public function parseVal($value)
  {
    return ($value == true);
  }

  /** setIndex **/
  public function setIndex($index, $default = '', $chkValue = '1')
  {
    if ($chkValue == false)
      throw new \RuntimeException(__("Le paramètre <b>\$chkValue</b> doit être différent de <b>false</b> !"));

    $this->chkValue = $chkValue;

    parent::setIndex($index, $default);
  }


  /* getFieldValue */
  protected function getFieldValue()
  {
    return ($this->value ? '1' : '0');
  }

  /** getValsSubmited() :
   *   retourne un tableau de tous les index séléctionnés ou non (et submité ou indexé au moins une fois !)
   *   array(index_1 => true, index_2 => false, ...)
   */
  public function getValsSubmited()
  {
    return parent::getVals();
  }


  /** getVals() :
   *   retourne un tableau des index séléctionnés uniquement
   *   array(index_1, index_3, ...)
   */
  public function getVals()
  {
    $vals = array();

    foreach ($this->saved as $index => $val)
      if ($val) $vals[] = $index;

    return $vals;
  }


  /* Affiche le champs de saisie texte */
  public function showField($options = '')
  {
    if (!$this->disabled) {
      $this->submited[] = $this->index;

      return '<input type="checkbox" class="input checkbox' . ($this->readOnly ? ' check_readonly' : '') . '" id="' . $this->getId() . '" name="' . $this->getName() . '" value="' . $this->chkValue . '"' .
      ($this->readOnly ? ' onclick="this.checked = ' . ($this->value ? 'true' : 'false') . ';"' : '') .
      ($this->value ? ' checked="true"' : '') . ($this->options != '' ? ' ' . $this->options : '') . ($options != '' ? ' ' . $options : '') . '/>';
    } else
      return '<input type="checkbox" class="input checkbox" id="' . $this->getId() . '" disabled="true"' . ($this->value ? ' checked="true"' : '') . ($options != '' ? ' ' . $options : '') . '/>';
  }

  public function isChecked()
  {
    return $this->value;
  }

  public function validate($type, $requis, $taille_max, &$msg)
  {
    return $this->setValid(true);
  }

  public function oraBool()
  {
    return ($this->value ? "1" : "0");
  }

  /** Valeur utilisable par PHP **/
  public function phpBool()
  {
    return $this->value;
  }

  public function displayVal()
  {

  }
}
