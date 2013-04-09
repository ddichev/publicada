<?php

require_once 'Zend/Search/Lucene/MultiSearcher.php';

class SearchController extends Publicada_Controller_Action
{
  public function searchAction()
  {
    $this->_helper->layout->setLayout('layouts/general/search');

    if ($this->getRequest()->getParam('q') == null) {
      $this->_helper->redirector('index', 'index');
    }

    $pageModel = Publicada_Model_Pages::getInstance();
    $mainPage = $pageModel->getByCode(Publicada_Model_Page::DEFAULT_ROOT_PAGE_CODE);
    $this->setViewMetaData($mainPage);

    $optionModel = Publicada_Model_Options::getInstance();
    $this->view->site_name = $optionModel->getByCode('site_name')->option_value;

    $this->view->mainPages = $mainPage->getPublishedChildren();

    $configuration = Zend_Registry::get('configuration');
    $combinedIndexPath = $configuration['combined_index_path'];
//		$pageIndexPath = $configuration['page_index_path'];
//		$postIndexPath = $configuration['post_index_path'];

    $index = Zend_Search_Lucene::open($combinedIndexPath);
//		$index = new Zend_Search_Lucene_Interface_MultiSearcher();
//		$index->addIndex(Zend_Search_Lucene::open($pageIndexPath));
//		$index->addIndex(Zend_Search_Lucene::open($postIndexPath));

    Zend_Search_Lucene_Analysis_Analyzer::setDefault(new Zend_Search_Lucene_Analysis_Analyzer_Common_Utf8());

    $queryParam = $this->_getParam('q');
    $language = $this->_getParam('lang');

    $userQuery = Zend_Search_Lucene_Search_QueryParser::parse(mb_strtolower($queryParam, mb_detect_encoding($queryParam)), 'utf-8');

    $publishedQuery = new Zend_Search_Lucene_Search_Query_Term(new Zend_Search_Lucene_Index_Term('2', 'status'));

    $languageQuery = new Zend_Search_Lucene_Search_Query_Term(new Zend_Search_Lucene_Index_Term($language, 'language'));

    $query = new Zend_Search_Lucene_Search_Query_Boolean();
    $query->addSubquery($userQuery, true);
    $query->addSubquery($publishedQuery, true);
    $query->addSubquery($languageQuery, true);

    $hits = $index->find($query);

    $this->view->query = $queryParam;
    $this->view->hits = Zend_Paginator::factory($hits);
    $this->view->hits->setCurrentPageNumber($this->_getParam('page'));
    $this->view->hits->setItemCountPerPage(10);

    $this->view->hitsCount = count($hits);
  }
}