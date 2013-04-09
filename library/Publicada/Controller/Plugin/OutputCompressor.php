<?php

class Publicada_Controller_Plugin_OutputCompressor extends Zend_Controller_Plugin_Abstract
{
    public function dispatchLoopShutdown()
    {
		if (preg_match("/gzip/", $this->getRequest()->getHeader('Accept-Encoding')) &&
			!ini_get('zlib.output_compression')) {
			$this->getResponse()->setHeader("Content-Encoding", "gzip");
			$body = gzencode( $this->getResponse()->getBody() );
	        $this->getResponse()->setBody( $body );
		}
		elseif (preg_match("/deflate/", $this->getRequest()->getHeader('Accept-Encoding')) &&
			!ini_get('zlib.output_compression')) {
			$this->getResponse()->setHeader("Content-Encoding", "gzip");
			$body = gzdeflate( $this->getResponse()->getBody() );
	        $this->getResponse()->setBody( $body );
		}
    }
}