<?php

namespace Atoll\Form\Field;

use Atoll\Form\AbstractFieldList;

class InputTextPopup extends AbstractFieldList
{
  protected $size;
  protected $maxlength;
  protected $fieldReadOnly = false;
  protected $fieldDisabled = false;
  protected $isMultiple    = false;

  protected $pl_popup;
  protected $popup_url;
  protected $popup_name;
  protected $popup_w;
  protected $popup_h;
  protected $popup_img_off = 'ico_idea_off.gif';
  protected $popup_img_on = 'ico_idea_on.gif';
  protected $popup_show_onchange = false;
  protected $modal = false;


  /** Constructeur **/
  public function __construct($id, $title = '', $accessKey = '', $size = 25, $maxlength = 100, $default = '')
  {
    parent::__construct($id, $title, $accessKey, $default);

    $this->size = $size;
    $this->maxlength = $maxlength;
  }

  public function setMultiple($isMultiple = true)
  {
    $this->isMultiple = $isMultiple;
  }

  /** parseVal **/
  public function parseVal($value)
  {
    if(is_array($value)) {
      return $value;
    }
    return str_replace(array('<', '>', '"'), '', trim($value));
  }

  /** setSize **/
  public function setSize($size)
  {
    $this->size = $size;
  }

  /** setPopupLink **/
  function setPopupLink($pl_popup, $args, $popup_w, $popup_h, $autocomplete = false, $colvalue = null, $modal = true, $auto_args = array(), $fill_fields = array(), $minLength = 1, $popup_show_onchange = false, $popup_img_off = 'ico_idea_off.gif', $popup_img_on = 'ico_idea_on.gif')
  {
    global $_PAGE;
    $BdD = new \SqlBdD();
    $this->pl_popup = $pl_popup;
    $stmt = $BdD->SqlQuery("select mtb_reference, mtb_path, mtb_table, mtb_rech_popup, mtb_col_id, mtb_params from ref_mod_table where mtb_table = upper('" . oraText($pl_popup) . "')", '');

    if (($mtb = $BdD->SqlFetchRow($stmt)) == false)
      throw new \RuntimeException(__("Popup <b>%s</b> inexistante !", $pl_popup));

    $pl_args = array();
    $al_args = array();

    if ($mtb["mtb_params"] != '')
      $pl_args[] = "'" . $mtb["mtb_params"];

    if (is_array($args)) {
      foreach ($args as $k => $v)
        $pl_args[] = "$k=$v";
    } else if ($args != '')
      $pl_args[] = $args;

    if (!is_array($auto_args))
      parse_str($auto_args, $al_args);

    $this->modal = $modal;

    if ($colvalue == null)
      $colvalue = $mtb["mtb_col_id"];

    if ($mtb["mtb_rech_popup"] != '')
      $this->setPopup("'" . PATH_ROOT_WEB . "/sysgestion/" . $mtb["mtb_path"] . "/" . $mtb["mtb_rech_popup"] . "'" . (count($pl_args) > 0 ? "+'?'+" . implode('&', $pl_args) : ''), $this->id . '_' . $mtb["mtb_rech_popup"], $popup_w, $popup_h, $popup_show_onchange, $popup_img_off, $popup_img_on);

    if ($autocomplete && $_PAGE instanceof \Page)
      $this->setAutoComplete($mtb["mtb_table"], $colvalue, $al_args, $minLength, $fill_fields);
  }

  /** setPopup **/
  function setPopup($popup_url, $popup_name, $popup_w, $popup_h, $popup_show_onchange = false, $popup_img_off = 'ico_idea_off.gif', $popup_img_on = 'ico_idea_on.gif')
  {
    $this->popup_url = $popup_url;
    $this->popup_name = $popup_name;
    $this->popup_w = $popup_w;
    $this->popup_h = $popup_h;
    $this->popup_show_onchange = $popup_show_onchange;
    $this->popup_img_off = $popup_img_off;
    $this->popup_img_on = $popup_img_on;
  }

  /** setModal **/
  public function setModal($modal = true)
  {
    $this->modal = $modal;
  }

  public function setAutoComplete($type, $colvalue = null, $args = array(), $minLength = 1, $fill_fields = array())
  {
    global $_PAGE;

    if (!$_PAGE instanceof \Page)
      throw new \RuntimeException(__("Aucune page définie !"));

    $data = "type:'" . htmlentities($type) . "'" . ($colvalue != '' ? ",colvalue:'" . htmlentities($colvalue) . "'" : "");
    foreach ($args as $k => $v)
      $data .= ',' . htmlentities($k) . ":'" . htmlentities($v) . "'";

    $_PAGE->addJSOnLoad("inputAutoComplete('" . $this->getId() . "', {" . $data . "}, '" . PATH_INC_WEB . "/autocomplete.php', $minLength, " . json_encode($fill_fields) . ");");
  }

  /** setFieldReadOnly **/
  public function setFieldReadOnly($fieldReadOnly = true)
  {
    $this->fieldReadOnly = $fieldReadOnly;
  }

  /** setFieldReadOnly **/
  public function setFieldDisabled($fieldDisabled = true)
  {
    $this->fieldDisabled = $fieldDisabled;
  }

  public function getPopupLien()
  {
    $is_modal = $this->modal ? '1' : '0';
    return "openPopup($this->popup_url, '$this->popup_name', '$this->popup_w', '$this->popup_h',$is_modal)";
  }

  public function getPopupId()
  {
    return $this->getId() . '_popup';
  }

  public function getImgId()
  {
    return $this->getId() . '_img';
  }

  /** Affiche le champs de saisie texte **/
  public function showField($options = '')
  {
    if (!$this->disabled)
      $this->submited[] = $this->index;

    $popup_lien = ($this->popup_url != '' ? 'openPopup(' . $this->popup_url . ', \'' . $this->popup_name . '\', \'' . $this->popup_w . '\', \'' . $this->popup_h . '\', \'' . ($this->modal ? '1' : '0') . '\')' : '');

    if ($this->readOnly || $this->disabled)
      return '<input type="text" class="input text' . ($this->isValid() ? '' : ' error') . '" id="' . $this->getId() . '" name="' . $this->getName() . '" size="' . $this->size . '" maxlength="' . $this->maxlength . '" value="' . $this->getFieldValue() . '"' . ($this->highlight ? ' onblur="unhighlight(this);" onfocus="highlight(this);"' : '') .
      ($this->readOnly ? ' readonly="true"' : '') . ($this->disabled ? ' disabled="true"' : '') . ($this->options != '' ? ' ' . $this->options : '') . ($this->comment != '' ? ' title="' . $this->comment . '"' : '') . ($options != '' ? ' ' . $options : '') . '/>';
    else
      return '<input type="text" class="input text' . ($this->isValid() ? '' : ' error') . '" id="' . $this->getId() . '" name="' . $this->getName() . '" size="' . $this->size . '" maxlength="' . $this->maxlength . '" value="' . $this->getFieldValue() . '"' . ($this->highlight ? ' onblur="unhighlight(this);" onfocus="highlight(this);"' : '') . ($this->popup_url != '' ? ' onkeydown="if (event.keyCode == 113) ' . $popup_lien . ';"' . ($this->popup_show_onchange ? ' onchange="' . $popup_lien . '; return false;"' : '') : '') .
      ($this->fieldReadOnly ? ' readonly="true"' : '') . ($this->fieldDisabled ? ' disabled="true"' : '') . ($this->options != '' ? ' ' . $this->options : '') . ($options != '' ? ' ' . $options : '') . ($this->comment != '' ? ' title="' . $this->comment . '"' : '') . '/>' .
      ($this->popup_img_off != '' ?
          ' <a id="' . $this->getId() . '_popup" href="#"' . ($this->popup_url != '' ? ' onclick="' . $popup_lien . '; return false;"' : 'style="display: none"') .
          ($this->popup_img_on != '' ? ' onmouseover="rub_ico_over(\'' . $this->getId() . '_img\');" onmouseout="rub_ico_out(\'' . $this->getId() . '_img\');" onfocus="rub_ico_over(\'' . $this->getId() . '_img\');" onblur="rub_ico_out(\'' . $this->getId() . '_img\');"' : '') .
          '>' . showIco($this->popup_img_off, '', __("Raccourci F2"), 3, 'id="' . $this->getId() . '_img"') . '</a>'
          : '');
  }

  public function displayVal()
  {
    $BdD   =  new \SqlBdD();

    $stmt  =  $BdD->SqlQuery("select mtb_reference, mtb_path, mtb_table, mtb_detail_popup, mtb_col_id, mtb_col_ref, mtb_params from ref_mod_table where mtb_table = upper('". oraText($this->pl_popup) ."')", '');
    if (($mtb = $BdD->SqlFetchRow($stmt)) == false)
      throw new \RuntimeException( __("Popup <b>%s</b> inexistante !", $this->pl_popup));
    $idMap = (is_array($this->getVal())) ? $this->getVal() : array("'" . $this->getVal() . "'");
    if ($this->isMultiple === true) {
      $idMap = array_map(function($val) { return "'" . $val . "'"; }, $this->getValues());
    }
    $stmt = $BdD->SqlQuery(sprintf('SELECT %s, %s FROM %s WHERE %s IN (%s)', $mtb['mtb_col_id'], $mtb['mtb_col_ref'], $mtb['mtb_table'], $mtb['mtb_col_id'], implode(', ', $idMap)));
    $valeurs = array();
    while ($row = $BdD->SqlFetchRow($stmt)) {
      $valeurs[$row[$mtb['mtb_col_id']]] = $row[$mtb['mtb_col_ref']];
    }
    $url 	= PATH_ROOT_WEB . "/sysgestion/" . $mtb["mtb_path"] . "/" . $mtb["mtb_detail_popup"];
    $args = array('rm_item' => 'FICHE');
    $return = '<dl>';
    foreach ($valeurs as $id => $ref) {
      $args[$mtb['mtb_col_id']] = $id;
      $return .= '<dt>' . $ref . ' ' . showPopupLink($url, $args, showico('ico_view.gif', '', __("Voir le détail")), '') . '</dt>';
    }
    $return .= '</dl>';
    return $return;
  }
  public function getValues()
  {
    if ($this->isMultiple === false) {
      return $this->getVal();
    }
    return explode(';', $this->getVal());
  }



}
