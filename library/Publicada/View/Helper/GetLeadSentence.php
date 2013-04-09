<?php
class Publicada_View_Helper_GetLeadSentence extends Zend_View_Helper_Abstract
{
	public function getLeadSentence($length = null, $text = '')
	{
	    $explode = explode(' ',$text);
	   
	    $string  = '';
	
	    $dots = '...';
	    if(count($explode) <= $length){
	        $dots = '';
	    }
	    for($i = 0; $i < $length; $i++) {
	    	if ($i + 1 > count($explode)) {
	    		break;
	    	}
			else {
		        $string .= $explode[$i] . " ";
			}
	    }
	
	    return $string . $dots;
	}
}