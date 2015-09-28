<?php
namespace Atoll\Form\Field;

use \Atoll\Form\AbstractFieldList;
/******************************************************************************/
/*                           Class InputRadioGroup                              */
/******************************************************************************/
class InputRadioGroup extends AbstractFieldList
{
  private $fields = array();

  public function __construct($id, $title, $accessKey, $options = array(), $value = '')
  {
    if (is_array($value)) {
      $value = $value[0];
    }

    if (count($options) == 1) {
      return new InputRadio($id, $title, $accessKey, $id, ($id === $value), $value);
    }

    $this->id     =  $id;
    $this->name   =  $id;
    $this->title  =  $title;
    foreach ($options as $key => $opt) {
      $this->fields[] = new InputRadio($key, $opt, $accessKey, $id, ($key == $value), $key);
    }

  }
  public function showField($options = '')
  {
    $fieldsTab = array();
    foreach ($this->fields as $field) {
      $fieldsTab[] = $field->showField($options);
      $fieldsTab[] = $field->showLabel();
    }
    return implode(' ', $fieldsTab);
  }

  public function oraText()
  {
    foreach ($this->fields as $field) {
      if ($field->isChecked() === true) {
        return $field->getTitle();
      }
    }
  }

  // Radio group have only one value
  public function getValues()
  {
    return null;
  }

  public function getVal()
  {
    foreach ($this->fields as $radio) {
      if ($radio->isChecked() === true) {
        return $radio->getVal();
      }
    }
    return null;
  }

  public function displayVal()
  {
    foreach ($this->fields as $radio) {
      if ($radio->isChecked() === true) {
        return $radio->getTitle();
      }
    }
    return null;
  }
}
