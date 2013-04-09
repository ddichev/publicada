<?php
/**
* A Zend Controller Plugin dedicated to undoing the damage of magic_quotes_gpc
* in systems where it is on.
*
* @author  Shahar Evron
*/

class Publicada_Plugin_StripMagicQuotes extends Zend_Controller_Plugin_Abstract
{
    /**
     * Called before the action loop is started. Will internally strip all
     * slashes off $request parameters
     *
     * @param Zend_Controller_Request_Abstract $request
     */
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        $params = $request->getParams();

        array_walk_recursive($params, array($this, 'stripSlashes'));
        $request->setParams($params);
    }
    
    /**
     * Strip the slashes off an item in the Params array
     *
     * @param string $value
     * @param string $key
     */
    protected function stripSlashes(&$value, $key)
    {
        $value = stripslashes($value);
    }
}
