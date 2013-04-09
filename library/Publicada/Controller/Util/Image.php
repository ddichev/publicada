<?php

class Publicada_Controller_Util_Image extends Publicada_Controller_Action
{
	protected $_dirName = 'images';
	
	public function urlAction()
	{
	    $file = $this->_getParam('src');
	    $fileDir = APPLICATION_PATH . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'static' . DIRECTORY_SEPARATOR . $this->_dirName . DIRECTORY_SEPARATOR;
	
	    if (file_exists($fileDir . $file))
	    {
	    	$this->getResponse()->setHeader('Content-Type', 'image/jpeg');
			$configuration = Zend_Registry::get('configuration');
			$simpleImage = new Publicada_Image_SimpleImage();
			$image = $fileDir . $file;
			$simpleImage->load($image);
			
			if ($this->_getParam('w') != null && $this->_getParam('h') != null) {
				$simpleImage->resize( (int) $this->_getParam('w'), (int) $this->_getParam('h') );
			}
			elseif ($this->_getParam('w') != null && $this->_getParam('h') == null) {
				$simpleImage->resizeToWidth( (int) $this->_getParam('w'));
			}
			elseif ($this->_getParam('h') != null && $this->_getParam('w') == null) {
				$simpleImage->resizeToHeight( (int) $this->_getParam('h'));
			}

			$contents = $simpleImage->output();
			
			$fileName = basename($_SERVER['REQUEST_URI']);
			if ($this->_dirName == 'posters') {
				$simpleImage->save($configuration['static_poster_storage'] . DIRECTORY_SEPARATOR . $fileName);	
			}
			else {
				$simpleImage->save($configuration['static_image_storage'] . DIRECTORY_SEPARATOR . $fileName);	
			}

	        echo $contents;
	    }
		else {
			$this->getResponse()->setRawHeader('HTTP/1.1 404 Not Found');
	    	$this->getResponse()->setHeader('Code', 'text/plain');
			$this->getResponse()->setHeader('Content-Type', 'text/plain; charset=utf-8');
			echo $this->view->translate('image not found.');
		}
	}
	
	public function loadExternalAction()
	{
	    $this->getResponse()->setHeader('Content-Type', 'image/jpeg');
		$url = $this->_getParam('url');
		$rawUrl = $url;
		$url = base64_decode($url);
		
		$base64Safe = strtr($rawUrl, '+/', '-_');
		$base64Safe = preg_replace("/\=/", '', $base64Safe);
		$md5FileName = md5($base64Safe);
		$fileName = APPLICATION_PATH . '/../temp/' . $md5FileName . '.jpg';
		if (!file_exists($fileName)) {
			$fileContents = file_get_contents($url);
			file_put_contents($fileName, $fileContents);
		}
		$simpleImage = new Publicada_Image_SimpleImage();
		$simpleImage->load($fileName);
		echo $simpleImage->output();
	}
	
	public function preDispatch() {
		$this->_helper->viewRenderer->setNoRender(); // Disable the viewscript
		$this->_helper->layout->disableLayout();  // Disable the layout
	}
}