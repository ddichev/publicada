<?php

class Publicada_PostController extends Publicada_Controller_Management
{
  public function listAction()
  {
    if ($this->_hasParam('page')) {
      $page = (int)$this->_getParam('page');
    } else {
      $page = 1;
    }

    $postsModel = Publicada_Model_Posts::getInstance();
    $posts = $postsModel->getAll(array('paginate' => true, 'lang' => Zend_Registry::get('content_locale'), 'page_at' => $page));
    $this->view->posts = $posts;
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
        $postsModel = new Publicada_Model_Posts();
        $post = $postsModel->createRow();
        $post->setFromArray($form->getValues());
        $post->save();

        $this->_redirect($this->view->url(array('action' => 'list', 'id' => null, 'cl' => Zend_Registry::get('content_locale'))), array('prependBase' => false));
      }
    }
  }

  public function editAction()
  {
    $form = $this->_getActionForm();

    $postId = $this->_getParam('id');

    if ($this->_request->isGet()) {
      $postsModel = new Publicada_Model_Posts();
      $post = $postsModel->getById($postId);

      $form->populate($post->toArray());
      $this->view->form = $form;
    } elseif ($this->_request->isPost()) {
      if (!$form->isValid($this->_getAllParams())) {
        $this->view->form = $form;
      } else {
        $postsModel = new Publicada_Model_Posts();
        $post = $postsModel->getById($postId);
        $post->setFromArray($form->getValues());
        $post->save();

        $this->_redirect($this->view->url(array('action' => 'list', 'id' => null, 'cl' => Zend_Registry::get('content_locale'))), array('prependBase' => false));
      }
    }
  }

  public function reindexAction()
  {
    $postModel = Publicada_Model_Posts::getInstance();
    $postModel->reIndex();

    $this->_redirect($this->view->url(array('action' => 'list', 'id' => null, 'cl' => Zend_Registry::get('content_locale'))), array('prependBase' => false));
  }

  public function deleteAction()
  {
    $postId = (int)$this->_getParam('id');
    $postModel = Publicada_Model_Posts::getInstance();
    $post = $postModel->getById($postId);
    if ($post) {
      $post->delete();
    } else {
      throw new Zend_Application_Exception('Trying to delete an object that does not exist!');
    }

    $this->_redirect($this->view->url(array('action' => 'list', 'id' => null, 'cl' => Zend_Registry::get('content_locale'))), array('prependBase' => false));
  }

  public function filesAction()
  {
    $id = $this->_getParam('id');
    $postsModel = Publicada_Model_Posts::getInstance();
    $post = $postsModel->getById((int)$id);
    $this->view->post = $post;

    $images = $post->getImages();
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
      ->setDestination($configuration['static_file_storage'][$type]);

    $postsModel = Publicada_Model_Posts::getInstance();
    $post = $postsModel->getById((int)$this->_getParam('id'));
    $this->view->post = $post;

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

        $p2fModel = new Publicada_Model_Posts2Files();
        $p2f = $p2fModel->createRow();
        $p2f->post_id = $post->id;
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

    $postsModel = Publicada_Model_Posts::getInstance();
    $post = $postsModel->getById((int)$this->_getParam('id'));
    $this->view->post = $post;

    $filesModel = Publicada_Model_Files::getInstance();

    if ($this->_request->isGet()) {
      $this->view->files = $filesModel->getAll();
    } elseif ($this->_request->isPost()) {
      $file = $filesModel->getById((int)$this->_getParam('fid'));

      if (!empty($file)) {
        $p2fModel = new Publicada_Model_Posts2Files();
        $p2fRow = $p2fModel->fetchRow($p2fModel->select()->where('post_id = ?', $page->id)->where('file_id = ?', $file->id));

        if (null === $p2fRow) {
          $p2f = $p2fModel->createRow();
          $p2f->post_id = $post->id;
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

    $configuration = Zend_Registry::get('configuration');
    $typePath = $configuration['svetnime_static_file_storage'][$file->type];
    $mainFile = $typePath . DIRECTORY_SEPARATOR . $file->name;

    // we don't delete files for now
    // unlink($mainFile);

    $postsModel = Publicada_Model_Posts::getInstance();
    $post = $postsModel->getById((int)$this->_getParam('id'));

    $p2fModel = new Publicada_Model_Posts2Files();
    $p2fRow = $p2fModel->fetchRow($p2fModel->select()->where('post_id = ?', $post->id)->where('file_id = ?', $file->id));
    $p2fRow->delete();

    // we also do not delete the file object, this will be possible from file model
    // $file->delete();

    $this->_redirect($this->view->url(array('action' => 'files', 'id' => $this->_getParam('id'))), array('prependBase' => false));
  }

  protected function _getActionForm()
  {
    $config = new Zend_Config_Ini($this->_getControllerFormPath(), $this->getActionName());

    $form = new Zend_Form($config->{$this->getControllerName()}->{$this->getActionName()});
    $form->setAction($this->_getActionUrl());

    $postCategoriesModel = new Publicada_Model_PostCategories();
    $postCategories = $postCategoriesModel->getAll(array('lang' => $this->_getParam('cl')));

    foreach ($postCategories as $postCategory) {
      $form->getElement('categories')
        ->addMultiOption($postCategory->id, $postCategory->name);
    }

    // $linkCategoriesModel = Publicada_Model_LinkCategories::getInstance();
    // $linkCategories = $linkCategoriesModel->getAll(array('lang' => $this->_getParam('cl')));

    if ($form->getElement('language') != null) {
      $configuration = Zend_Registry::get('configuration');
      foreach ($configuration['languages'] as $langKey => $langValue) {
        $form->getElement('language')->addMultiOption($langValue, $langValue);
      }
    }

    return $form;
  }
}
