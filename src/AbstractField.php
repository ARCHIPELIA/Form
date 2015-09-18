<?php

namespace Atoll\Form;

abstract class AbstractField
{
  protected $id;
  protected $name;
  protected $title;
  protected $accessKey;

  protected $options = '';
  protected $value;
  protected $valid = true;
  protected $disabled = false;
  protected $readOnly = false;
  protected $highlight = true;
  protected $comment = '';


  /** Constructeur **/
  public function __construct($id, $title, $accessKey, $default, $name = '')
  {
    $this->id = $id;
    $this->name = ($name == '' ? $id : $name);
    $this->title = $title;

    if ($accessKey != '')
      $this->accessKey = ($title != '' && stripos($title, $accessKey) === false) ? $title[0] : $accessKey;

    $this->setDefault($default);
  }

  /** setDefault **/
  public function setDefault($default = '')
  {
    if (isset($_POST[$this->name]) && !is_array($_POST[$this->name])) {
      $this->value = $this->parseVal($_POST[$this->name]);
    } else
      $this->value = $this->parseVal($default);
  }

  /** parseVal : traitement du champ après réception du post **/
  public function parseVal($value)
  {
    return trim($value);
  }

  protected function getFieldValue()
  {
    return $this->value;
  }

  public function getId()
  {
    return $this->id;
  }

  public function getName()
  {
    return $this->name;
  }

  public function getTitle()
  {
    return $this->title;
  }

  public function getVal($html = false)
  {
    return ($html ? str_replace(array("\r\n", "\n", "\r"), "<br>", $this->value) : $this->value);
  }

  public function isSubmited()
  {
    return isset($_POST[$this->name]);
  }

  public function isDisabled()
  {
    return $this->disabled;
  }

  public function isNull()
  {
    return ($this->value == '');
  }

  public function setVal($value)
  {
    $this->value = $this->parseVal($value);
  }

  public function setTitle($title)
  {
    $this->title = $title;
  }

  public function setValid($valid)
  {
    return $this->valid = $valid;
  }

  public function isValid()
  {
    return $this->valid;
  }

  public function setOptions($options = '')
  {
    $this->options = $options;
  }

  public function addOptions($options = '')
  {
    $this->options = $options . ($this->options == '' ? '' : ";$this->options");
  }

  public function setDisabled($disabled = true)
  {
    $this->disabled = $disabled;
  }

  public function setReadOnly($readOnly = true)
  {
    $this->readOnly = $readOnly;
  }

  public function setHighlight($highlight = true)
  {
    $this->highlight = $highlight;
  }

  public function setComment($comment)
  {
    $this->comment = $comment;
  }


  /** initialize **/
  public function initialize()
  {
    if (isset($_POST[$this->id]))
      unset($_POST[$this->id]);
  }

  /** Affiche le label du champs **/
  public function showLabel($options = '')
  {
    return '<label for="' . $this->getId() . '"' .
    ($this->accessKey != '' ? ' accesskey="' . $this->accessKey . '"' : '') .
    ($this->isValid() ? '' : ' class="error"') . ($options != '' ? ' ' . $options : '') . ($this->comment != '' ? ' title="' . $this->comment . '"' : '') . '>' .
    (strlen($this->accessKey) == 1 ? preg_replace("/([^$this->accessKey]*)($this->accessKey)(.*)/i", "\\1<u>\\2</u>\\3", $this->title) : $this->title) .
    '</label>';
  }

  /** showHiddenField **/
  public function showHiddenField()
  {
    return '<input type="hidden" name="' . $this->getName() . '" id="' . $this->getId() . '" value="' . $this->getFieldValue() . '"/>';
  }

  /** showField **/
  abstract public function showField($options = '');


  /** validate : Validation du champs formulaire **/
  public function validate($type, $requis, $taille_max, &$msg)
  {
    return $this->setValid(validate($this->value, $type, $requis, $taille_max, $msg));
  }

  /** setUpperCase **/
  public function setUpperCase($type = '')
  {
    $val = $this->value;

    if ($type == 'ALNUM') {
      $val = remove_accent($val);
      $val = preg_replace('/[^a-zA-Z0-9\.]/', ' ', $val);
      $val = preg_replace('/[ ]+/', ' ', $val);
      $val = trim($val);
    }

    $this->setVal(mb_strtoupper($val));
  }

  /** setLowerCase **/
  public function setLowerCase()
  {
    $this->setVal(strtolower($this->value));
  }

  /** toArray **/
  public function toArray($type = 'VAL')
  {
    if ($type == 'VAL')
      return array($this->getName() => $this->getFieldValue());

    return array();
  }

  /****************************************************************************/
  /** Valeur utilisable par Oracle                                           **/

  public function oraText($like = false, $html = false)
  {
    return oraText($like ? str_replace("*", "%", $this->value) : $this->value, $html);
  }

  public function oraFloat($keep_null = false)
  {
    return oraFloat($this->value, $keep_null);
  }

  public function oraInt($keep_null = false)
  {
    return oraInt($this->value, $keep_null);
  }

  public function phpFloat()
  {
    return phpFloat($this->value);
  }
}
