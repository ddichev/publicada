<?php

class Publicada_Filter_Keywords implements Zend_Filter_Interface
{
    public function filter($value)
    {
		if (!empty($value)) {
			$value = trim($value);
			$newLineRegexp = '/[\s\,]*(\r\n?|\n)[\s\,]*/i';
			if (preg_match($newLineRegexp, $value)) {
				$lines = preg_split($newLineRegexp, $value);
				$valueFiltered = implode(', ', $lines);
				$valueFiltered = preg_replace('/\s\s+/', ' ', $valueFiltered);
				return $valueFiltered;
			}
			$value = preg_replace('/\s\s+/', ' ', $value);
		    return $value;
		}
		return $value;
    }
}