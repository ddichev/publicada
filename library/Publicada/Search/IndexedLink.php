<?php
class Publicada_Search_IndexedLink extends Zend_Search_Lucene_Document
{
	public function __construct($link)
	{
		Zend_Search_Lucene_Analysis_Analyzer::setDefault(new Zend_Search_Lucene_Analysis_Analyzer_Common_Utf8());
	
		$this->addField(Zend_Search_Lucene_Field::Keyword('link_id', $link->id, 'utf-8'));
	
		$this->addField(Zend_Search_Lucene_Field::Keyword('link_code', $link->code, 'utf-8'));
		
		$this->addField(Zend_Search_Lucene_Field::Text('title', $link->title, 'utf-8'));
		
		$this->addField(Zend_Search_Lucene_Field::UnStored('title_to_lower', mb_strtolower($link->title, mb_detect_encoding($this->title)), 'utf-8'));
		
		$this->addField(Zend_Search_Lucene_Field::Text('description', $link->description, 'utf-8'));
		
		$this->addField(Zend_Search_Lucene_Field::Text('description_to_lower', mb_strtolower($link->description, mb_detect_encoding($this->description)), 'utf-8'));
		
		$this->addField(Zend_Search_Lucene_Field::Text('keywords', $link->keywords, 'utf-8'));

		$this->addField(Zend_Search_Lucene_Field::UnStored('keywords_to_lower', mb_strtolower($link->keywords, mb_detect_encoding($this->keywords)), 'utf-8'));
		
		$this->addField(Zend_Search_Lucene_Field::Keyword('language', $link->language));
		
		$this->addField(Zend_Search_Lucene_Field::Keyword('content_type', 'link'));
	}
}