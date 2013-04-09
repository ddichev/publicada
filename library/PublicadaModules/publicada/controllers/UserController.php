<?php

class Publicada_UserController extends Publicada_Controller_Management
{
  public function newAction()
  {
    $form = $this->_getActionForm();

    if ($this->_request->isGet()) {
      $this->view->registerForm = $form;
    } elseif ($this->_request->isPost()) {
      if ($form->isValid($this->_getAllParams())) {
        $usersModel = new Publicada_Model_Users();
        $user = $usersModel->createRow($form->getValues());
        $user->password = md5($user->password);
        $user->save();

        $namespace = new Zend_Session_Namespace();
        $namespace->justRegistered = 'yes';
        $namespace->userRegistered = $user->username;
      } else {
        $this->view->registerForm = $form;
      }
    }
  }

  public function loginAction()
  {
    $this->_helper->layout->setLayout('layouts/publicada/login');
    $form = $this->_getActionForm();

    if ($this->_request->isGet()) {
      $this->view->form = $form;
    } elseif ($this->_request->isPost()) {
      if (!$form->isValid($this->_getAllParams())) {
        $this->view->form = $form;
      } else {
        if (Publicada_Model_User::doesAuthenticate($form->getValue('username'), $form->getValue('password'))) {
          $this->_helper->redirector('index', 'index');
        } else {
          $this->view->form = $form;
        }
      }
    }
  }

  public function logoutAction()
  {
    $this->_helper->layout->setLayout('layouts/publicada/login');
    Zend_Auth::getInstance()->clearIdentity();
    $form = $this->_getActionForm();
    $this->view->form = $form;

    $this->_helper->redirector('login');
  }

  public function editAction()
  {
  }

  public function changepasswordAction()
  {
    $form = $this->_getActionForm();

    if ($this->_request->isGet()) {
      $usersModel = new Publicada_Model_Users();
      $user = $usersModel->getByName(Zend_Auth::getInstance()->getIdentity());

      $this->view->passwordForm = $form;
    } elseif ($this->getRequest()->isPost()) {
      if ($form->isValid($this->_getAllParams())) {
        $usersModel = Publicada_Model_Users::getInstance();
        $user = $usersModel->getByName(Zend_Auth::getInstance()->getIdentity());
        $user->changePassword($form->getElement('newpassword')->getValue());

        $this->_helper->redirector('edit');
      } else {
        $this->view->passwordForm = $form;
      }
    }
  }

  public function editdetailsAction()
  {
    $form = $this->_getActionForm();

    if ($this->_request->isGet()) {
      $usersModel = new Publicada_Model_Users();
      $user = $usersModel->getByName(Zend_Auth::getInstance()->getIdentity());

      $form->populate($user->toArray());
      $this->view->editDetailsForm = $form;
    } elseif ($this->_request->isPost()) {
      if ($form->isValid($this->_getAllParams())) {
        $usersModel = new Publicada_Model_Users();
        $user = $usersModel->getByName(Zend_Auth::getInstance()->getIdentity());
        $user->setFromArray($form->getValues());
        $user->save();

        $this->_helper->redirector('edit');
      }
    }
  }
}