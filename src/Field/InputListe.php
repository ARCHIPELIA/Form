<?php
namespace Atoll\Form\Field;

use Atoll\Form\AbstractFieldList;

class InputListe extends AbstractFieldList
{
  protected $field 			= null;
  protected $field_sel	= null;

  public function __construct($id, $title = '', $accessKey = '', $values = array(), $default = array())
  {
    global $_PAGE;
    $values_sel = array();
    foreach ($default as $selected) {
      if (array_key_exists($selected, $values)) {
        $values_sel[$selected] = $values[$selected];
        unset($values[$selected]);
      }
    }
    $this->field = new Select($id, $title, $accessKey, $values);
    $this->field->setSize(10);
    $this->field->setMultiple();
    $this->field_sel = new Select(sprintf('%s_sel', $id), $title, $accessKey, $values_sel);
    $this->field_sel->setSize(10);
    $this->field_sel->setMultiple();
    $_PAGE->addJavaScriptFiles("selectbox.js");
    parent::__construct($id, $title, $accessKey, $default);
  }

  public function getOnSubmitEventActions()
  {
    return 'selectAllOptions( getEle(\'' . $this->field_sel->getId() . '\') )';
  }

  /* parseVal */
  public function parseVal($value){
    return ($value == true);
  }

  public function showField($options = '')
  {
    return '
      <table cellpadding="0" cellspacing="2">
        <tr>
          <td> <b>' . __('Non associé') . '</b> <br>
          ' . $this->field->showField('ondblclick="copySelectedOptions(getEle(\'' . $this->field->getId() . '\'), getEle(\'' . $this->field_sel->getId() . '\'), false);" style="width: 180px;"') . '
          </td>
          <td>
            <table cellpadding="0" cellspacing="2">
              <tr>
                <td align="center"> '. showButton('button', 'small', '', '', 'onclick="copySelectedOptions(getEle(\'' . $this->field->getId() . '\'), getEle(\'' . $this->field_sel->getId() . '\'), false); return false;"', '', 'but_right.gif', '', 0) .' </td>
              </tr>
              <tr>
                <td align="center"> '. showButton('button', 'small', '', '', 'onclick="moveSelectedOptions( getEle(\'' . $this->field_sel->getId() . '\'), getEle(\'' . $this->field->getId() . '\'), false, \'^('. implode('|', $this->field_sel->getVals()) .')$\' );"', '', 'but_left.gif', '', 0) .' </td>
              </tr>
            </table>
          </td>
          <td> <b>' . __('Associé') . '</b> <br>
          ' . $this->field_sel->showField('ondblclick="moveSelectedOptions( getEle(\'' . $this->field_sel->getId() . '\'), getEle(\'' . $this->field->getId() . '\'), false, \'^('. implode('|', $this->field_sel->getVals()) .')$\' );" style="width: 180px;"') . '
          </td>
          <td width="30">
            <table cellpadding="0" cellspacing="2">
              <tr>
                <td align="center"> '. showButton('button', 'small', '', '', 'onclick="moveOptionUp(getEle(\'' . $this->field_sel->getId() . '\'), getEle(\'' . $this->field_sel->getId() . '\'), false); return false;"', '', 'but_up.gif', '', 0) .' </td>
              </tr>
              <tr>
                <td align="center"> '. showButton('button', 'small', '', '', 'onclick="moveOptionDown( getEle(\'' . $this->field_sel->getId() . '\') ); return false;"', '', 'but_down.gif', '', 0) .' </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>';
  }

  public function getValues()
  {
    return $this->field_sel->getVals();
  }

  public function displayVal()
  {
    $return = array();
    foreach ($this->field_sel->getValues() as $value) {
      $return[] = $value;
    }
    return implode(', ', $return);
  }
}
