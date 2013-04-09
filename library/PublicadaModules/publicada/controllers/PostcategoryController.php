<?php

class Publicada_PostcategoryController extends Publicada_Controller_Management
{
  public function listAction()
  {
    if ($this->_hasParam('page')) {
      $page = (int)$this->_getParam('page');
    } else {
      $page = 1;
    }

    $code = $this->_getParam('ccode') ? $this->_getParam('ccode') : '';

    $postCategoriesModel = new Publicada_Model_PostCategories();
    $postCategories = $postCategoriesModel->getAll(array('parent_code' => $code, 'page' => $page, 'lang' => $this->_getParam('cl')));
    $this->view->postCategories = $postCategories;

    if ($code != '') {
      $postCategory = $postCategoriesModel->getByCode($code);
      $this->view->postCategory = $postCategory;
    }
  }

  public function createAction()
  {
    $form = $this->_getActionForm();

    if ($this->_request->isGet()) {
      $this->view->form = $form;
    } elseif ($this->_request->isPost()) {
      if (!$form->isValid($this->_getAllParams())) {
        $this->view->form = $form;
      } else {
        $postCategoriesModel = new Publicada_Model_PostCategories();
        $postCategory = $postCategoriesModel->createRow();
        $postCategory->setFromArray($form->getValues());
        $postCategory->save();

        $this->_redirect($this->view->url(array('action' => 'list', 'id' => null, 'cl' => Zend_Registry::get('content_locale'))), array('prependBase' => false));
      }
    }
  }

  public function editAction()
  {
    $form = $this->_getActionForm();

    if ($this->_request->isGet()) {
      $postCategoriesModel = new Publicada_Model_PostCategories();
      $postCategory = $postCategoriesModel->getById($this->_getParam('id'));

      $form->populate($postCategory->toArray());
      $this->view->form = $form;
    } elseif ($this->_request->isPost()) {
      if (!$form->isValid($this->_getAllParams())) {
        $this->view->form = $form;
      } else {
        $postCategoriesModel = new Publicada_Model_PostCategories();
        $postCategory = $postCategoriesModel->getById($this->_getParam('id'));
        $postCategory->setFromArray($form->getValues());
        $postCategory->save();
        $this->_redirect($this->view->url(array('action' => 'list', 'id' => null, 'cl' => Zend_Registry::get('content_locale'))), array('prependBase' => false));
      }
    }
  }

  public function deleteAction()
  {
    $postCategoriesModel = new Publicada_Model_PostCategories();
    $postCategory = $postCategoriesModel->getById($this->_getParam('id'));

    if ($postCategory) {
      $postCategory->delete();
    } else {
      throw new Zend_Application_Exception('Trying to delete an object that does not exist!');
    }

    $this->_redirect($this->view->url(array('action' => 'list', 'id' => null, 'cl' => Zend_Registry::get('content_locale'))), array('prependBase' => false));
  }

  public function findlinksAction()
  {
    $categoriesModel = new Publicada_Model_PostCategories();
    $category = $categoriesModel->getById((int)$this->_getParam('id'));

//		$queryElements = preg_split("/\,\s/i", $category->keywords);
//		$query = preg_replace("/\,\s/i", '" "', $category->keywords);
    $query = $category->keywords . ' AND content_type:link';

    $configuration = Zend_Registry::get('configuration');
    $combinedIndexPath = $configuration['combined_index_path'];

    Zend_Search_Lucene_Analysis_Analyzer::setDefault(
      new Zend_Search_Lucene_Analysis_Analyzer_Common_Utf8_CaseInsensitive ());

    $index = Zend_Search_Lucene::open($combinedIndexPath);

    Zend_Search_Lucene_Search_QueryParser::setDefaultEncoding('utf-8');
    $hits = $index->find(Zend_Search_Lucene_Search_QueryParser::parse($query, 'utf-8'));

    $this->view->foundlinks = Zend_Paginator::factory($hits);
    $this->view->foundlinks->setCurrentPageNumber((int)$this->_getParam('page'));
    $this->view->foundlinks->setItemCountPerPage(10);

    $this->view->query = $query;
  }
}