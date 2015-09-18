<?php

namespace Atoll\Form;

use Atoll\Form\Field\InputRadio;

class Form
{
  const MAX_FORM_SAVE = 5;
  protected $_PAGE = null;

  protected $fields = array();

  protected $is_load = false;
  protected $frm_id;
  protected $user_id;
  protected $frm_reference;
  protected $frm_titre;
  protected $frm_designation;
  protected $frm_tri;
  protected $frm_tri_ordre;
  protected $frm_date_creation;


  /* Constructeur : __construct($id, [formField1], [formField1], ...) */
  public function __construct($frm_reference)
  {
    global $_PAGE;

    if ($_PAGE instanceof \Page) $this->_PAGE = $_PAGE;

    $this->frm_reference = $frm_reference;

    $args = array_slice(func_get_args(), 1);
    call_user_func_array(array($this, 'addFields'), $args);

    // Auto submition du formulaire
    if ($this->is_load && $this->_PAGE instanceof \Page) {
      $_PAGE->addJavascript("
        function frm_load(){
          var form_submit  =  getEle('$this->frm_reference');

          if (form_submit != null){
            var oObj  =  document.createElement('INPUT');
            oObj.setAttribute('type', 'hidden');
            oObj.setAttribute('id', 'frm_" . $this->frm_reference . "_frm_id');
            oObj.setAttribute('name', 'frm_" . $this->frm_reference . "_frm_id');
            oObj.setAttribute('value', $this->frm_id);

            form_submit.appendChild(oObj);
            formSubmit('$this->frm_reference');
            form_submit.removeChild(oObj);
          }
        }");

      $this->_PAGE->addJSOnLoad("frm_load();");
    }
  }

  /* getFields */
  public function getFields()
  {
    return $this->fields;
  }

  /* setSaveFields */
  public function setSaveFields()
  {
    if (formIsSubmit($this->frm_reference)) {
      $this->save();
    } else {
      $this->load();
    }
  }

  /* getLoadLink */
  public function getLoadLstLink($form_submit = '')
  {
    if ($form_submit == '')
      $form_submit = $this->frm_reference;

    return showLink('#', '', 'onclick="openPopup(\'../include_web/liste_form_load_popup.php?frm_reference=' . $this->frm_reference . '&form_submit=' . $this->frm_reference . '\', \'liste_form_load_popup\', \'650\', \'300\', \'1\'); return false;"', 'c', 'ico_save.gif', __("Charger une recherche"), 4);
  }

  /* toArray */
  public function toArray()
  {
    $arrs = array('VAL' => array(), 'KEY' => array());

    foreach ($this->fields as $field) {
      $arrs['VAL'] += $field->toArray('VAL');
      $arrs['KEY'] += $field->toArray('KEY');
    }

    return $arrs;
  }

  public function isLoad()
  {
    return $this->is_load;
  }

  public function getFrmId()
  {
    return $this->frm_id;
  }

  public function getUserId()
  {
    return $this->user_id;
  }

  public function getFrmReference()
  {
    return $this->frm_reference;
  }

  public function getFrmTitre()
  {
    return $this->frm_titre;
  }

  public function getFrmDesignation()
  {
    return $this->frm_designation;
  }

  public function getFrmTri()
  {
    return $this->frm_tri;
  }

  public function getFrmTriOrdre()
  {
    return $this->frm_tri_ordre;
  }

  public function getFrmDateCreation()
  {
    return $this->frm_date_creation;
  }


  /******************************************************************************/

  /* addFields([formField1], [formField1], ...) */
  private function addFields()
  {
    $this->fields = array();

    foreach (func_get_args() as $arg) {
      if (!($arg instanceof AbstractField))
        throw new \RuntimeException(__("Argument de type FormField requis ! '<b>%s</b>' trouvé.", get_class($arg)));

      $this->fields[$arg->getId()] = $arg;
    }

    if (post('frm_' . $this->frm_reference . '_frm_id') != '') {
      $BdD = new \SqlBdD();
      $this->frm_id = postInt('frm_' . $this->frm_reference . '_frm_id');

      // Infos formulaire
      $stmt = $BdD->SqlQuery("select frm_usr_id, frm_reference, frm_titre, frm_designation, frm_tri, frm_tri_ordre, frm_date_creation from prt_formulaire where frm_id = '$this->frm_id'");
      if ($frm = $BdD->SqlFetchRow($stmt)) {
        $this->usr_id = $frm['frm_usr_id'];
        $this->frm_reference = $frm['frm_reference'];
        $this->frm_titre = $frm['frm_titre'];
        $this->frm_designation = $frm['frm_designation'];
        $this->frm_tri = $frm['frm_tri'];
        $this->frm_tri_ordre = $frm['frm_tri_ordre'];
        $this->frm_date_creation = $frm['frm_date_creation'];
      }

      // Infos champs formulaire
      $stmt = $BdD->SqlQuery("select frc_id, frc_valeur from prt_formulaire_champ where frc_frm_id = '$this->frm_id'");
      while ($frc = $BdD->SqlFetchRow($stmt)) {
        foreach ($this->fields as $field) if ($field->getId() == $frc['frc_id']) {
          $val = json_decode($frc['frc_valeur']);
          $field->setVal($val != '' ? $val : $frc['frc_valeur']);
        }
      }

      $this->is_load = true;
    }
  }

  /******************************************************************************/

  /* save : Sauvegarde de la liste des champs en session */
  private function save()
  {
    $this->clear();

    foreach ($this->getFields() as $formField)
      if ($formField instanceof InputRadio)
        $_SESSION['_FORM'][$this->frm_reference][$formField->getId()] = $formField->isChecked();
      else if ($formField instanceof AbstractField)
        $_SESSION['_FORM'][$this->frm_reference][$formField->getId()] = $formField->getVal();

    $_SESSION['_FORM'][$this->frm_reference]['date_save'] = time();
  }

  /* load : Chargement de la liste des champs en session */
  private function load()
  {
    if (isset($_SESSION['_FORM'][$this->frm_reference]))
      foreach ($this->getFields() as $formField)
        if ($formField instanceof InputRadio)
          $formField->setDefault(isset($_SESSION['_FORM'][$this->frm_reference][$formField->getId()]) ? $_SESSION['_FORM'][$this->frm_reference][$formField->getId()] : '');
        else if ($formField instanceof AbstractField)
          $formField->setVal(isset($_SESSION['_FORM'][$this->frm_reference][$formField->getId()]) ? $_SESSION['_FORM'][$this->frm_reference][$formField->getId()] : '');
  }

  /* clear : Suppression  de la liste des champs en session */
  private function clear()
  {
    unset($_SESSION['_FORM'][$this->frm_reference]);

    if (isset($_SESSION['_FORM'])) {
      while (count($_SESSION['_FORM']) > Form::MAX_FORM_SAVE) {
        $date = time();
        $frm_id = null;
        foreach ($_SESSION['_FORM'] as $id => $frm) if (isset($frm['date_save'])) {
          if ($frm_id == null || $frm['date_save'] < $date) {
            $frm_id = $id;
            $date = $frm['date_save'];
          }
        }

        if ($frm_id != null)
          unset($_SESSION['listes'][$frm_id]);
        else
          break;
      }
    }
  }
}
