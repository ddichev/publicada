<?php

class Publicada_Validate_UsernameUsed extends Zend_Validate_Abstract
{
    const USERNAME_USED = 'codeUsed';

    protected $_messageTemplates = array(
        self::USERNAME_USED => 'Username already used. Choose another one.'
    );

    public function isValid($value, $context = null)
    {
        $value = (string) $value;
        $this->_setValue($value);

	    if (is_array($context)) {

    		$usersModel = Publicada_Model_Users::getInstance();
			$user = $usersModel->fetchRow($usersModel->select()->where('username = ?', $value));

			// if page is found using the code
			if ($user instanceof Publicada_Model_User) {
				// if editing the page that was found is the one being edited
				// then we're checking against the same page so no problem
				if (isset($context['id']) && $context['id'] == $user->id) {
					return true;
				}
				else {
			        $this->_error(self::USERNAME_USED);
			        return false;
				}
			}
			else {
				return true;
			}
        }

        $this->_error(self::USERNAME_USED);
        return false;
    }
}