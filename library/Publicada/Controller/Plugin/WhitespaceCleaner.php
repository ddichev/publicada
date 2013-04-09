<?php

class Publicada_Controller_Plugin_WhitespaceCleaner extends Zend_Controller_Plugin_Abstract
{
    public function dispatchLoopShutdown()
    {
        $response = $this->getResponse();
		$body = preg_replace( "/(?:(?<=\>)|(?<=\/\>))(\s+)(?=\<\/?)/", "", $response->getBody() );
        $response->setBody( $body );
    }
}