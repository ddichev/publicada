<?php

class Publicada_Search_IndexedPost extends Zend_Search_Lucene_Document
{
	public function __construct($post)
	{
		Zend_Search_Lucene_Analysis_Analyzer::setDefault(new Zend_Search_Lucene_Analysis_Analyzer_Common_Utf8());
	
		$this->addField(Zend_Search_Lucene_Field::Keyword('post_id', $post->id, 'utf-8'));
		
		$this->addField(Zend_Search_Lucene_Field::Keyword('status', $post->status, 'utf-8'));
		
		$this->addField(Zend_Search_Lucene_Field::Text('title', $post->title, 'utf-8'));
		
		$this->addField(Zend_Search_Lucene_Field::UnStored('title_to_lower', mb_strtolower($post->title, mb_detect_encoding($post->title)), 'utf-8'));
		
		$this->addField(Zend_Search_Lucene_Field::Text('description', $post->description, 'utf-8'));
		
		$this->addField(Zend_Search_Lucene_Field::UnStored('body', $post->body, 'utf-8'));
		
		$this->addField(Zend_Search_Lucene_Field::UnStored('body_to_lower', mb_strtolower($post->body, mb_detect_encoding($post->body)), 'utf-8'));
		
		$this->addField(Zend_Search_Lucene_Field::Text('keywords', $post->keywords, 'utf-8'));

		$this->addField(Zend_Search_Lucene_Field::UnStored('keywords_to_lower', mb_strtolower($post->keywords, mb_detect_encoding($post->keywords)), 'utf-8'));
		
		$this->addField(Zend_Search_Lucene_Field::Keyword('language', $post->language));
		
		$this->addField(Zend_Search_Lucene_Field::Keyword('content_type', 'post'));
	}
}