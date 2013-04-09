<?php
class Publicada_View_Helper_ParseTextVars extends Zend_View_Helper_Abstract
{
	public function parseTextVars($text = '') {
		if ($text == '')
		{
			return '';
		}
		
		$new_text = str_replace('*site*', Bootstrap::$view->baseUrl(), $text);
		
		return $new_text;
	}
}