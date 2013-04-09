<?php

class Publicada_Application_Resource_Siteconfig extends Zend_Application_Resource_ResourceAbstract
{
    public function init()
    {
        return $this->getOptions();
    }
}