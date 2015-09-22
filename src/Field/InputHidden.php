<?php
namespace Atoll\Form\Field;

use Atoll\Form\AbstractField;
/******************************************************************************/
/*                           Class InputText                                  */
/******************************************************************************/
class InputHidden extends \InputText {
  /** Constructeur **/
  public function __construct($id, $title = '', $accessKey = '', $default = ''){
    parent::__construct($id, $title, $accessKey, $default);
  }
  /** parseVal **/
  public function parseVal($value){
    return str_replace(array('<', '>', '"'), '', trim($value));
  }
  /** Affiche le champs de saisie texte **/
  public function showField($options = ''){
    if (!$this->disabled)
      $this->submited[]  =  $this->index;
    return '<input type="hidden" class="input text'. ($this->isValid() ? '' : ' error') .'" id="'. $this->getId() .'" name="'. $this->getName() .'" value="'. $this->getFieldValue() .'"' .
    ($this->readOnly ? ' readonly="true"' : '') . ($this->disabled ? ' disabled="true"' : '') .
    ($this->options != '' ? ' '. $this->options : '') . ($options != '' ? ' '. $options : '') . ($this->comment != '' ? ' title="'. $this->comment .'"' : '') .'/>';
  }
  public function displayVal()
  {
    return $this->oraText();
  }
}
