<?php

class Publicada_Search_IndexedPage extends Zend_Search_Lucene_Document
{
	public function __construct($page)
	{
		Zend_Search_Lucene_Analysis_Analyzer::setDefault(new Zend_Search_Lucene_Analysis_Analyzer_Common_Utf8());
	
		$this->addField(Zend_Search_Lucene_Field::Keyword('page_id', $page->id, 'utf-8'));
		
		$this->addField(Zend_Search_Lucene_Field::Keyword('code', $page->code, 'utf-8'));
		
		$this->addField(Zend_Search_Lucene_Field::Keyword('status', $page->status, 'utf-8'));
		
		$this->addField(Zend_Search_Lucene_Field::Text('title', $page->title, 'utf-8'));
		
		$this->addField(Zend_Search_Lucene_Field::UnStored('title_to_lower', mb_strtolower($page->title, mb_detect_encoding($page->title)), 'utf-8'));
		
		$this->addField(Zend_Search_Lucene_Field::Text('description', $page->description, 'utf-8'));
		
		$this->addField(Zend_Search_Lucene_Field::UnStored('description_to_lower', mb_strtolower($page->description, mb_detect_encoding($page->description)), 'utf-8'));
		
		$this->addField(Zend_Search_Lucene_Field::UnStored('body', $page->body, 'utf-8'));
		
		$this->addField(Zend_Search_Lucene_Field::UnStored('body_to_lower', mb_strtolower($page->body, mb_detect_encoding($page->body)), 'utf-8'));
		
		$this->addField(Zend_Search_Lucene_Field::UnStored('keywords', $page->keywords, 'utf-8'));

		$this->addField(Zend_Search_Lucene_Field::UnStored('keywords_to_lower', mb_strtolower($page->keywords, mb_detect_encoding($page->keywords)), 'utf-8'));
		
		$this->addField(Zend_Search_Lucene_Field::Keyword('language', $page->language));
		
		$this->addField(Zend_Search_Lucene_Field::Keyword('content_type', 'page'));
	}
}