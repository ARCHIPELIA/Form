<?php

namespace Atoll\Form\Field;

use Atoll\Form\AbstractField;

class InputCalendar extends \Atoll\Form\AbstractField
{
  const POS_BOT_RIGHT		= 1;
  const POS_TOP_RIGHT		= 2;
  const POS_BOT_LEFT		= 3;
  const POS_TOP_LEFT		= 4;
  const POS_AUTO				= 5;
  const FMT_FULL_DATE		= 1;
  const FMT_WEEK_YEAR		= 2;
  const FMT_PERIOD_YEAR	= 3;
  private $field 					= null;
  private $positionOpt		= self::POS_AUTO;
  private $dateFormatOpt	= self::FMT_FULL_DATE;
  private $validPosition = array(
      self::POS_BOT_RIGHT,
      self::POS_TOP_RIGHT,
      self::POS_BOT_LEFT,
      self::POS_TOP_LEFT,
      self::POS_AUTO,
  );
  private $validDateFormat = array(
      self::FMT_FULL_DATE,
      self::FMT_WEEK_YEAR,
      self::FMT_PERIOD_YEAR
  );
  public function __construct($id, $title = '', $accessKey = '', $size = 25, $maxlength = 100, $default = array())
  {
    $this->field = new InputText($id, $title, $accessKey, $size, $maxlength, $default);
    parent::__construct($id, $title, $accessKey, $default);
  }
  public function setPositionOpt($position)
  {
    if (!in_array($position, $this->validPosition)) {
      throw new \InvalidArgumentException;
    }
    $this->positionOpt = $position;
  }
  public function setDateFormatOpt($dateFormat)
  {
    if (!in_array($dateFormat, $this->validDateFormat)) {
      throw new \InvalidArgumentException;
    }
    $this->dateFormatOpt = $dateFormat;
  }
  public function showField($options = '')
  {
    $fieldsTab = array();
    $this->field->setValid($this->isValid());
    $fieldsTab[] = $this->field->showField($options);
    $fieldsTab[] = showico('ico_calendar.gif', '', __("Calendrier"), 3, 'border="0" onClick="popUpCalendar(this, getEle(\'' . $this->field->getId() . '\'), ' . $this->positionOpt . ', ' . $this->dateFormatOpt . ')"');
    return implode(' ', $fieldsTab);
  }
  public function displayVal()
  {
    return $this->oraText();
  }
}
