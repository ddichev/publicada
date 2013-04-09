<?php

class PostController extends Publicada_Controller_Action
{
  public function showAction()
  {
    $postId = (int)$this->_getParam('id');

    $this->_helper->layout->setLayout('layouts/general/post');

    $postModel = Publicada_Model_Posts::getInstance();
    $post = $postModel->getById($postId);
    if (null === $post) {
      throw new Zend_Exception('Post was not found');
    }

    $this->view->post = $post;

    $this->setViewMetaData($post);

    $pageModel = Publicada_Model_Pages::getInstance();
    $mainPage = $pageModel->getByCode(Publicada_Model_Page::DEFAULT_ROOT_PAGE_CODE);
    $this->view->mainPages = $mainPage->getPublishedChildren();

    $postCategoryModel = Publicada_Model_PostCategories::getInstance();
    $categories = $postCategoryModel->getCategories('');
    $this->view->categories = $categories;

    $this->view->postImageIndex = (int)$this->_getParam('pic');
  }
}