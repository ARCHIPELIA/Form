<?php

namespace Atoll\Form;

abstract class AbstractFieldList extends AbstractField
{
  protected $index = null;
  protected $upperCase = false;
  protected $upperType = '';
  protected $lowerCase = false;

  protected $saved = array();   // Sauvegarde de toutes les valeurs
  protected $submited = array();   // Sauvegarde des valeurs à submiter
  protected $valids = array();   // Sauvegarde des valeurs valide ou non valide


  /** Constructeur **/
  public function __construct($id, $title, $accessKey, $default)
  {
    // ATTENTION -> id = name
    parent::__construct($id, $title, $accessKey, $default, $id);

    // Résultats sauvegardés précédement
    if (isset($_POST["saved_$this->id"]) && isset($_POST["submited_$this->id"])) {
      $this->saved = JSON::json_decode($_POST["saved_$this->id"], false, true, false);
      $this->submited = JSON::json_decode($_POST["submited_$this->id"], false, true, false);

      // Nouveaux résultat sauvegardés (ATTENTION : $_POST[$this->id][$index] est null dans le cas d'une checkbox non cochée !)
      foreach ($this->submited as $index)
        $this->saved[$index] = $this->parseVal(isset($_POST[$this->id][$index]) ? $_POST[$this->id][$index] : '');

    } else if (isset($_POST[$this->id]) && is_array($_POST[$this->id])) {
      // Si showSavedFields() non utilisé (1 page maxi !)
      foreach ($_POST[$this->id] as $index => $val)
        $this->saved[$index] = $this->parseVal($val);
    }

    $this->submited = array();
  }

  /** setIndex **/
  public function setIndex($index, $default = '')
  {
    $this->index = $index;

    if ($index === null)
      $this->value = $default;
    else {
      if (isset($this->saved[$this->index]))
        $this->value = $this->saved[$this->index];
      else
        $this->saved[$this->index] = $this->value = $this->parseVal($default);
    }

    // Upper/Lower case
    if ($this->upperCase) parent::setUpperCase($this->upperType);
    if ($this->lowerCase) parent::setLowerCase();
  }

  /** setValid **/
  public function setValid($valid)
  {
    return $this->index === null ? parent::setValid($valid) : $this->valids[$this->index] = $valid;
  }

  /** isValid **/
  public function isValid()
  {
    return $this->index === null ? parent::isValid() : (array_key_exists($this->index, $this->valids) ? $this->valids[$this->index] : true);
  }

  /** initialize **/
  public function initialize()
  {
    parent::initialize();

    $this->saved = array();
    $this->submited = array();
  }

  /** getId **/
  public function getId($full = true)
  {
    return ($this->index !== null && $full) ? $this->id . "_" . str_replace('/', '_', $this->index) : $this->id;
  }

  /** getName **/
  public function getName()
  {
    return ($this->index !== null) ? $this->id . "[" . $this->index . "]" : $this->id;
  }

  /** setVal **/
  public function setVal($value)
  {
    $this->saved[$this->index] = $this->value = $this->parseVal($value);
  }

  /** setVal **/
  public function setSize($size)
  {
    $this->saved[$this->index] = $this->size = $this->setSize($size);
  }

  /** delIndex **/
  public function delIndex($tableIndex)
  {
    if (isset($this->saved[$tableIndex]))
      unset($this->saved[$tableIndex]);

    if ($this->index == $tableIndex)
      $this->index = null;
  }

  /** setTable @deprecated */
  function setTable($tableIndex, $default = '')
  {
    $this->setIndex($tableIndex, $default);
  }

  /** getVals **/
  public function getVals()
  {
    return $this->saved;
  }

  /** setVals **/
  public function setVals($vals)
  {
    $this->saved = $vals;
  }

  /** setUpperCase **/
  public function setUpperCase($type = '')
  {
    $this->upperCase = true;
    $this->upperType = $type;
    parent::setUpperCase($type);
  }

  /** setLowerCase **/
  public function setLowerCase()
  {
    $this->lowerCase = true;
    parent::setLowerCase();
  }

  /** getSubmited **/
  public function getSubmited()
  {
    return $this->saved;
  }

  /** showHiddenField **/
  public function showHiddenField()
  {
    if (!$this->disabled)
      $this->submited[] = $this->index;

    return parent::showHiddenField();
  }

  /** showSavedFields **/
  public function showSavedFields()
  {
    return '<input type="hidden" id="saved_' . $this->id . '" name="saved_' . $this->id . '" value=\'' . JSON::json_encode($this->saved) . '\'/>' . "\n" .
    '<input type="hidden" id="submited_' . $this->id . '" name="submited_' . $this->id . '" value=\'' . JSON::json_encode($this->submited) . '\'/>' . "\n";
  }
}
