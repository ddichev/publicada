<?php

class Publicada_OptionController extends Publicada_Controller_Management
{
  /**
   * List options
   */
  public function listAction()
  {
    if ($this->_hasParam('page')) {
      $page = (int)$this->_getParam('page');
    } else {
      $page = 1;
    }
    $optionsModel = new Publicada_Model_Options();
    $options = $optionsModel->getOptions(self::DEFAULT_PAGE_SIZE, $page);
    $this->view->options = $options;
  }

  /**
   * Edit an option
   */
  public function editAction()
  {
    $optionId = (int)$this->_getParam('id');
    $optionsModel = new Publicada_Model_Options();
    $option = $optionsModel->getById($optionId);
    $form = $this->_getActionForm();

    if ($this->_request->isGet()) {
      $form->populate($option->toArray());
      $this->view->form = $form;
    } elseif ($this->_request->isPost()) {
      if (!$form->isValid($this->_getAllParams())) {
        $this->view->form = $form;
      } else {
        $option->setFromArray($form->getValues());
        $option->save();

        $this->_redirect($this->view->url(array('action' => 'list', 'id' => null, 'cl' => Zend_Registry::get('content_locale'))), array('prependBase' => false));
      }
    }
  }

  /**
   * Create an option
   */
  public function createAction()
  {
    $form = $this->_getActionForm();

    if ($this->_request->isGet()) {
      $this->view->form = $form;
    } elseif ($this->_request->isPost()) {
      if (false === $form->isValid($this->_getAllParams())) {
        $this->view->form = $form;
      } else {
        $optionsModel = new Publicada_Model_Options();

        $option = $optionsModel->createRow();
        $option->setFromArray($form->getValues());
        $option->save();

        $this->_redirect($this->view->url(array('action' => 'list', 'id' => null, 'cl' => Zend_Registry::get('content_locale'))), array('prependBase' => false));
      }
    }
  }

  /**
   * Delete an option
   */
  public function deleteAction()
  {
    $optionId = (int)$this->_getParam('id');

    $optionsModel = new Publicada_Model_Options();
    $option = $optionsModel->getById($optionId);
    $option->delete();

    $this->_redirect($this->view->url(array('action' => 'list', 'id' => null, 'cl' => Zend_Registry::get('content_locale'))), array('prependBase' => false));
  }
}