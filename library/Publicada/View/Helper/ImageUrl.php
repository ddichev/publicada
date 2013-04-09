<?php

class Publicada_View_Helper_ImageUrl extends Zend_View_Helper_Abstract
{
	protected $_fileType = 'image';
	
	public function imageUrl($imageName, $fileType = null, $absolute = false, $transform = array())
	{
		$configuration = Zend_Registry::get('configuration');
		$url = '';
		$transformParams = array();
		$addTransform = false;
		
		if (array_key_exists('width', $transform) && array_key_exists('height', $transform)) {
			$url = $this->view->url(array('src' => $imageName, 'w' => $transform['width'], 'h' => $transform['height']), $this->_fileType . '-wh');
		}
		elseif (array_key_exists('width', $transform) && !array_key_exists('height', $transform)) {
			$url = $this->view->url(array('src' => $imageName, 'w' => $transform['width']), $this->_fileType . '-w');
		}
		elseif (array_key_exists('height', $transform) && !array_key_exists('width', $transform)) {
			$url = $this->view->url(array('src' => $imageName, 'h' => $transform['height']), $this->_fileType . '-h');
		}
		else {
			$url = $this->view->url(array('src' => $imageName), 'home-image');
		}

		return $url;
	}
}