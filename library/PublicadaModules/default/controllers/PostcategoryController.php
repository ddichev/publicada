<?php

class PostcategoryController extends Publicada_Controller_Action
{
  public function postsAction()
  {
    $this->_helper->layout->setLayout('layouts/general/blog');

    $pageModel = Publicada_Model_Pages::getInstance();
    $mainPage = $pageModel->getByCode(Publicada_Model_Page::DEFAULT_ROOT_PAGE_CODE);
    $this->view->mainPages = $mainPage->getPublishedChildren();

    $postCategoryCode = $this->_getParam('code');
    $page_at = (int)$this->_getParam('page');

    $postCategoryModel = Publicada_Model_PostCategories::getInstance();
    $postCategory = $postCategoryModel->getByCode($postCategoryCode);
    $this->view->category = $postCategory->name;

    $posts = $postCategory->getPosts(16, $page_at);

    $this->setViewMetaData($postCategory);

    $this->view->posts = $posts;

    $categories = $postCategoryModel->getCategories('');
    $this->view->categories = $categories;
  }
}