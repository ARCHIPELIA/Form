<?php
namespace Atoll\Form\Field;

use Atoll\Form\AbstractFieldList;
/******************************************************************************/
/*                           Class InputCheckboxGroup                         */
/******************************************************************************/
class InputCheckboxGroup extends AbstractFieldList
{
  private $fields = array();
  public function __construct($id, $title = '', $accessKey = '', $options = array(), $default = array())
  {
    if (count($options) == 1) {
      return new InputCheckbox($id, $title, $accessKey, $default);
    }
    $this->id     =  $id;
    $this->name   =  $id;
    $this->title  =  $title;
    foreach ($options as $key => $opt) {
      $this->fields[] = new InputCheckbox($key, $opt, $accessKey, (in_array($key, $default) === true));
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
  public function getValues()
  {
    $values = array();
    foreach ($this->fields as $field) {
      if ($field->isChecked() === true) {
        $values[] = $field->getId();
      }
    }
    return $values;
  }
  public function displayVal()
  {
    $return = array();
    foreach ($this->fields as $radio) {
      if ($radio->isChecked() === true) {
        $return[] = $radio->getTitle();
      }
    }
    return implode(', ', $return);
  }
}
