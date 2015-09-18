<?php

namespace Atoll\Form\Field;

use Atoll\Form\AbstractFieldList;

class Select extends AbstractFieldList
{
  /** Attributs **/
  protected $size = 1;
  protected $values = array();
  protected $multiple = false;


  /** Constructeur **/
  public function __construct($id, $title = '', $accessKey = '', $values = array(), $default = '')
  {
    $this->values = $values;

    parent::__construct($id, $title, $accessKey, $default);
  }

  /** parseVal **/
  public function parseVal($value)
  {
    return $value;
  }

  /** setDefault **/
  public function setDefault($default)
  {
    if (isset($_POST[$this->id]) /*&& !is_array($_POST[$this->id])*/) {
      $this->value = $this->parseVal($_POST[$this->id]);
    } else if ($default != '') {
      $this->value = $this->parseVal($default);
    } else if (count($this->values) > 0) {
      // On choisit la 1er valeur de la liste
      $this->value = current(array_keys($this->values));
    } else {
      $this->value = $this->parseVal('');
    }
  }

  public function setValues($values = array())
  {
    $this->values = $values;
  }

  public function getValues()
  {
    return $this->values;
  }

  public function setSize($size)
  {
    $this->size = $size;
  }

  public function setMultiple($multiple = true, $size = '')
  {
    $this->multiple = $multiple;
    if ($size != '')
      $this->size = $size;
  }

  /** showLabel **/
  public function showLabel($options = '')
  {
    return parent::showLabel('onclick="setFocus(this.htmlFor); return false;"' . ($options != '' ? ' ' . $options : ''));
  }

  /** Affiche le champs de saisie texte **/
  public function showField($options = '')
  {
    if (!$this->disabled)
      $this->submited[] = $this->index;

    $result = '<select class="input select' . ($this->isValid() ? '' : ' error') . '" id="' . $this->getId() . '" name="' . $this->getName() . ($this->multiple ? '[]' : '') . '" size="' . $this->size . '"' . ($this->highlight ? ' onblur="unhighlight(this);" onfocus="highlight(this);"' : '') .
      ($this->multiple ? ' multiple="yes"' : '') . ($this->readOnly || $this->disabled ? ' disabled="true"' : '') .
      ($this->options != '' ? ' ' . $this->options : '') . ($options != '' ? ' ' . $options : '') . '>';

    foreach ($this->values as $key => $value) {
      $selected = '';
      if ($this->multiple && is_array($this->value)) {
        if (in_array($key, $this->value)) {
          $selected = ' selected="selected"';
        }
      } else if ("$key" == "$this->value") {
        $selected = ' selected="selected"';
      }
      $result .= '<option value="' . $key . '"' . $selected . '>' . $value . '</option>';
    }

    $result .= '</select>';

    return $result;
  }


  public function oraText()
  {
    if (is_array($this->value)) {
      $value = array();

      foreach ($this->value as $key => $val)
        $value[$key] = oraText($val, true);

      return $value;
    } else
      return oraText($this->value, true);
  }


  public function oraFloat($keep_null = false)
  {
    if (is_array($this->value)) {
      $value = array();

      foreach ($this->value as $key => $val)
        $value[$key] = oraFloat($val, $keep_null);

      return $value;
    } else
      return oraFloat($this->value, $keep_null);
  }

  public function oraInt()
  {
    if (is_array($this->value)) {
      $value = array();

      foreach ($this->value as $key => $val)
        $value[$key] = oraInt($val);

      return $value;
    } else
      return oraInt($this->value);
  }

  public function phpFloat()
  {
    if (is_array($this->value)) {
      $value = array();

      foreach ($this->value as $key => $val)
        $value[$key] = phpFloat($val);

      return $value;
    } else
      return phpFloat($this->value);
  }
}
