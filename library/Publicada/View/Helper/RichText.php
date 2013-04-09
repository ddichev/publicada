<?php
class Publicada_View_Helper_RichText extends Zend_View_Helper_Abstract
{
    const MARKUP_TEXTILE = 'textile';
    const MARKUP_MARKDOWN = 'markdown';

	public function richText($text = '', $markup = null)
    {
    	$text = preg_replace("/\x{2022}\s+|\-\s+/mu", "* ", $text); // http://stackoverflow.com/questions/3140734/unicode-preg-replace-problem-in-php and http://stackoverflow.com/questions/2728070/how-do-i-replace-characters-not-in-range-0x5e10-0x7f35-with-in-php/2728372#2728372
    	
    	$text = preg_replace("/[0-9]+\)\s+/mu", "p. ", $text);
    	
    	$text = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n\n", $text);
    	
        if (is_null($markup)) {
            $markup = self::MARKUP_TEXTILE;
        }

        if ($markup == self::MARKUP_TEXTILE) {
            require_once 'Markup/classTextile.php';
            $textile = new Textile();
            return $textile->TextileThis($text);
        }
        elseif ($markup == self::MARKUP_MARKDOWN) {
            require_once 'Markup/markdown.php';
            $body = Markdown($text);
            return $body;
        }
	}
}