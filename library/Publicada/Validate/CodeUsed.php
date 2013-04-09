<?php

class Publicada_Validate_CodeUsed extends Zend_Validate_Abstract
{
    const CODE_USED = 'codeUsed';

    protected $_messageTemplates = array(
        self::CODE_USED => 'Code already used. Choose another code!'
    );

    public function isValid($value, $context = null)
    {
        $value = (string) $value;
        $this->_setValue($value);

	    if (is_array($context)) {

	    	// if a page code is being validated
	    	if ($context['datatype'] == 'page') {
        		$pagesModel = Publicada_Model_Pages::getInstance();
				$page = $pagesModel->fetchRow($pagesModel->select()->where('code = ?', $value));

				// if page is found using the code
				if ($page instanceof Publicada_Model_Page) {
					if ($page->language == $context['language']) {
						
						// if editing the page that was found is the one being edited
						// then we're checking against the same page so no problem
						if (isset($context['id']) && $context['id'] == $page->id) {
							return true;
						}
						else {
					        $this->_error(self::CODE_USED);
					        return false;
						}
					}
					else {
						return true;
					}
				}
				else {
					return true;
				}
        	}
        }

        $this->_error(self::CODE_USED);
        return false;
    }
}