<?php

class Publicada_View_Helper_GetDate extends Zend_View_Helper_Abstract
{
	protected $_format;

	public function getDate($dateString, $format = null)
	{
		if (null === $format) {
			$this->_format = 'd.M.YYYY';
		}
		else {
			$this->_format = $format;
		}

		$newDate = new Zend_Date($dateString, Zend_Date::ISO_8601);
		
		$newDate->setLocale(new Zend_Locale('bg_BG'));
		
		$date = $newDate->toString($this->_format);
		
		return $date;
	}
}