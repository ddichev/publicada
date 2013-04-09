<?php

class Publicada_FileController extends Publicada_Controller_Management
{
  public function listAction()
  {
    if ($this->_hasParam('page')) {
      $page = (int)$this->_getParam('page');
    } else {
      $page = 1;
    }

    $filesModel = new Publicada_Model_Files();
    $files = $filesModel->getAll(self::DEFAULT_PAGE_SIZE, $page);
    $this->view->files = $files;
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
        $filesModel = new Publicada_Model_Files();
        $file = $filesModel->createRow();
        $file->name = $form->getElement('file_upload')->getValue();

        $date = new Zend_Date();
        $now = $date->toString('YYYY-MM-dd HH:mm:ss');
        $file->created_at = $now;

        $file->save();

        $this->_redirect($this->view->url(array('action' => 'list', 'id' => null, 'cl' => Zend_Registry::get('content_locale'))), array('prependBase' => false));
      }
    }
  }

  public function editAction()
  {
    $form = $this->_getActionForm();

    $filesModel = new Publicada_Model_Files();
    $file = $filesModel->getById((int)$this->_getParam('id'));

    $this->view->file = $file;

    $form->populate($file->toArray());

    if ($this->_request->isGet()) {
      $this->view->form = $form;
    } elseif ($this->_request->isPost()) {
      if (!$form->isValid($this->_getAllParams())) {
        $this->view->form = $form;
      } else {
        $file->setFromArray($this->_getAllParams());
        $file->save();

        $this->_redirect($this->view->url(array('action' => 'list', 'id' => null, 'cl' => Zend_Registry::get('content_locale'))), array('prependBase' => false));
      }
    }
  }

  protected function _getActionForm()
  {
    $configuration = Zend_Registry::get('configuration');
    $form = parent::_getActionForm();

    if ($this->getActionName() == 'create') {
      $form->getElement('file_upload')->addValidator('Exists', null, array($configuration['static_file_storage']));
      $form->getElement('file_upload')
        ->setDestination($configuration['static_file_storage']);
    }

    return $form;
  }

  public function deleteAction()
  {
    $fileId = $this->_getParam('id');
    $filesModel = new Publicada_Model_Files();
    $file = $filesModel->getById($fileId);
    if ($file) {
      $file->delete();
    } else {
      throw new Zend_Application_Exception('Trying to delete an object that does not exist!');
    }

    $this->_redirect($this->view->url(array('action' => 'list', 'id' => null, 'cl' => Zend_Registry::get('content_locale'))), array('prependBase' => false));
  }
}