<?php

class Publicada_Controller_Management extends Publicada_Controller_Action
{
	public function preDispatch()
	{		
		$this->_helper->layout->setLayout('layouts/publicada/default');

		if (!Zend_Auth::getInstance()->hasIdentity())
		{
			if ('login' != $this->getRequest()->getActionName())
			{
				$this->_helper->redirector('login', 'user', 'publicada');
			}
		}

		parent::preDispatch();
		
		if ($this->_getParam('cl')) {
			Zend_Registry::set('content_locale', $this->_getParam('cl'));
		}
	}
	
	public function searchAction()
	{
		if ($this->getRequest()->getParam('q') == null) {
			$this->_helper->redirector('list', $this->getControllerName());
		}
		
		$this->view->controllerName = $this->getControllerName();
		
		$configuration = Zend_Registry::get('configuration');
		$combinedIndexPath = $configuration['index_path'];
		
		$index = Zend_Search_Lucene::open($combinedIndexPath);

		Zend_Search_Lucene_Analysis_Analyzer::setDefault(new Zend_Search_Lucene_Analysis_Analyzer_Common_Utf8());
		
		$queryParam = $this->_getParam('q');
		$language = $this->_getParam('cl');
		
		$userQuery = Zend_Search_Lucene_Search_QueryParser::parse(mb_strtolower($queryParam, mb_detect_encoding($queryParam)), 'utf-8');
		
		$languageQuery = new Zend_Search_Lucene_Search_Query_Term(new Zend_Search_Lucene_Index_Term($language, 'language'));
		
		$languageQuery = new Zend_Search_Lucene_Search_Query_Term(new Zend_Search_Lucene_Index_Term($this->getControllerName(), 'content_type'));
		
		$query = new Zend_Search_Lucene_Search_Query_Boolean();
		$query->addSubquery($userQuery, true);
		$query->addSubquery($languageQuery, true);
		
		$hits = $index->find($query);
		
		$this->view->query = $queryParam;
		$this->view->hits = Zend_Paginator::factory($hits);
		$this->view->hits->setCurrentPageNumber($this->_getParam('page'));
		$this->view->hits->setItemCountPerPage(10);
		
		$this->view->hitsCount = count($hits);

		// Set view metadata
		$viewMetaData = new stdClass;
		$viewMetaData->title = $this->view->query . ':';
		if ($this->view->hitsCount < 1) {
			$viewMetaData->title .= ' ' . $this->view->translate('search.noresultsfound');
		}
		elseif ($this->view->hitsCount == 1) {
			$viewMetaData->title .= ' ' . $this->view->hitsCount . ' ' . $this->view->translate('search.resultfound');
		}
		elseif ($this->view->hitsCount > 1) {
			$viewMetaData->title .= ' ' . $this->view->hitsCount . ' ' . $this->view->translate('search.resultsfound');
		}
		$this->view->heading = $viewMetaData->title;
		$viewMetaData->keywords = $this->view->query . ', ' . $this->view->translate('site.search');
		$viewMetaData->description = $viewMetaData->title;
		$this->setViewMetaData($viewMetaData);
		
		$this->renderScript('search/search.phtml');
	}
}