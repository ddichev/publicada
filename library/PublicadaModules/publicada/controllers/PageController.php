<?php

class Publicada_PageController extends Publicada_Controller_Management
{
  public function listAction()
  {
    if ($this->_hasParam('page')) {
      $page = (int)$this->_getParam('page');
    } else {
      $page = 1;
    }

    $pagesModel = new Publicada_Model_Pages();
    $pages = $pagesModel->getAll(array('paginate' => true, 'lang' => Zend_Registry::get('content_locale'), 'page_at' => $page));
    $this->view->pages = $pages;
  }

  public function createAction()
  {
    $form = $this->_getActionForm();

    if ($this->_request->isGet()) {
      $this->view->form = $form;
    } elseif ($this->_request->isPost()) {
      if (!$form->isValid($this->_request->getParams())) {
        $this->view->form = $form;
      } else {
        $pagesModel = new Publicada_Model_Pages();
        $page = $pagesModel->createRow();
        $page->setFromArray($form->getValues());
        $page->save();

        $this->_redirect($this->view->url(array('action' => 'list', 'id' => null, 'cl' => Zend_Registry::get('content_locale'))), array('prependBase' => false));
      }
    }
  }

  public function editAction()
  {
    $pageId = (int)$this->_getParam('id');

    $form = $this->_getActionForm();

    if ($this->_request->isGet()) {
      $pagesModel = new Publicada_Model_Pages();
      $page = $pagesModel->getById($pageId);
      $form->populate($page->toArray());
      $this->view->form = $form;
    } elseif ($this->_request->isPost()) {
      if (!$form->isValid($this->_getAllParams())) {
        $this->view->form = $form;
      } else {
        $pagesModel = new Publicada_Model_Pages();
        $page = $pagesModel->getById($pageId);
        $page->setFromArray($form->getValues());
        $page->save();

        $this->_redirect($this->view->url(array('action' => 'list', 'id' => null, 'cl' => Zend_Registry::get('content_locale'))), array('prependBase' => false));
      }
    }
  }

  public function deleteAction()
  {
    if ($this->_hasParam('id')) {
      $pageId = (int)$this->_getParam('id');
      $pagesModel = new Publicada_Model_Pages();
      $page = $pagesModel->getById($pageId);
      if ($page) {
        $page->delete();
      } else {
        throw new Zend_Application_Exception('Trying to delete an object that does not exist!');
      }
    }

    $this->_redirect($this->view->url(array('action' => 'list', 'id' => null, 'cl' => Zend_Registry::get('content_locale'))), array('prependBase' => false));
  }

  public function reindexAction()
  {
    $pagesModel = new Publicada_Model_Pages();
    $pagesModel->reIndex();

    $this->_redirect($this->view->url(array('action' => 'list', 'id' => null, 'cl' => Zend_Registry::get('content_locale'))), array('prependBase' => false));
  }


  protected function _getActionForm()
  {
    $form = parent::_getActionForm();

    $pagesModel = new Publicada_Model_Pages();
    $pages = $pagesModel->getAll(array('lang' => Zend_Registry::get('content_locale')));

    $form->getElement('parent_code')->addMultiOption('', '');
    foreach ($pages as $page) {
      $form->getElement('parent_code')->addMultiOption($page->code, $page->title);
    }

    $configuration = Zend_Registry::get('configuration');
    $renderers = $configuration['layout']['renderers'];
    foreach ($renderers as $rendererKey => $rendererVal) {
      $form->getElement('renderer')->addMultiOption($rendererKey, $rendererVal);
    }

    $linkCategoriesModel = Publicada_Model_Links::getInstance();
    $linkCategories = $linkCategoriesModel->getAll();
    foreach ($linkCategories as $linkCategory) {
      $form->getElement('links_code')->addMultiOption($linkCategory->code, $linkCategory->name);
    }

    return $form;
  }

  public function filesAction()
  {
    $id = $this->_getParam('id');
    $pagesModel = Publicada_Model_Pages::getInstance();
    $page = $pagesModel->getById((int)$id);
    $this->view->page = $page;

    $images = $page->getImages();
    $this->view->images = $images;
  }

  public function addfileAction()
  {
    $type = (int)$this->_getParam('type');

    $configuration = Zend_Registry::get('configuration');
    $config = new Zend_Config_Ini($this->getModuleDirectory() . '/forms/file.ini', 'create');
    $form = new Zend_Form($config->file->create);
    $form->setAction($this->_getActionUrl());
    $form->getElement('file_upload')
      ->setDestination($configuration['static_file_storage']);

    $pagesModel = Publicada_Model_Pages::getInstance();
    $page = $pagesModel->getById((int)$this->_getParam('id'));
    $this->view->page = $page;

    if ($this->_request->isGet()) {
      $this->view->form = $form;
    } elseif ($this->_request->isPost()) {
      if (!$form->isValid($this->_getAllParams())) {
        $this->view->form = $form;
      } else {
        $fileName = $form->getElement('file_upload')->getValue();

        $filesModel = Publicada_Model_Files::getInstance();
        $file = $filesModel->createRow();
        $file->name = $fileName;
        $file->type = $type;
        $date = new Zend_Date();
        $now = $date->toString('YYYY-MM-dd HH:mm:ss');
        $file->created_at = $now;
        $file->save();

        $p2fModel = new Publicada_Model_Pages2Files();
        $p2f = $p2fModel->createRow();
        $p2f->page_id = $page->id;
        $p2f->file_id = $file->id;
        $p2f->save();

        $this->_redirect($this->view->url(array('action' => 'files')), array('prependBase' => false));
      }
    }
  }

  public function addexistingfileAction()
  {
    $type = (int)$this->_getParam('type');
    $configuration = Zend_Registry::get('configuration');

    $pagesModel = Publicada_Model_Pages::getInstance();
    $page = $pagesModel->getById((int)$this->_getParam('id'));
    $this->view->page = $page;

    $filesModel = Publicada_Model_Files::getInstance();

    $pageAt = (int)$this->_getParam('page');
    if ($this->_request->isGet()) {
      $this->view->files = $filesModel->getAll(16, $pageAt);
    } elseif ($this->_request->isPost()) {
      $file = $filesModel->getById((int)$this->_getParam('fid'));

      if (!empty($file)) {
        $p2fModel = new Publicada_Model_Pages2Files();
        $p2fRow = $p2fModel->fetchRow($p2fModel->select()->where('page_id = ?', $page->id)->where('file_id = ?', $file->id));

        if (null === $p2fRow) {
          $p2f = $p2fModel->createRow();
          $p2f->page_id = $page->id;
          $p2f->file_id = $file->id;
          $p2f->save();
        }
      }

      $this->_redirect($this->view->url(array('action' => 'files')), array('prependBase' => false));
    }
  }

  public function removefileAction()
  {
    $filesModel = Publicada_Model_Files::getInstance();
    $file = $filesModel->getById((int)$this->_getParam('fid'));

    $pagesModel = Publicada_Model_Pages::getInstance();
    $page = $pagesModel->getById((int)$this->_getParam('id'));

    $configuration = Zend_Registry::get('configuration');
    $typePath = $configuration['static_file_storage'];
    $mainFile = $typePath . DIRECTORY_SEPARATOR . $file->name;

    $p2fModel = new Publicada_Model_Pages2Files();
    $p2fRow = $p2fModel->fetchRow($p2fModel->select()->where('page_id = ?', $page->id)->where('file_id = ?', $file->id));
    $p2fRow->delete();

    // we also do not delete the file object, this will be possible from file model
    // !!! here should be added logic to check if file has any pages attached to it
    // $file->delete();
    // unlink($mainFile);

    $this->_redirect($this->view->url(array('action' => 'files', 'id' => $this->_getParam('id'))), array('prependBase' => false));
  }
}