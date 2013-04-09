<?php

class IndexController extends Publicada_Controller_Action
{
  public function indexAction()
  {
    $code = $this->_getParam('code');
    $this->_helper->layout->setLayout('layouts/general/homepage');

    $pageModel = Publicada_Model_Pages::getInstance();
    $mainPage = $pageModel->getByCode(Publicada_Model_Page::DEFAULT_ROOT_PAGE_CODE);
    $this->view->mainPages = $mainPage->getPublishedChildren();

    $this->view->page = $mainPage;
    $this->setViewMetaData($page);

    if ($this->view->page['parent_code'] == Publicada_Model_Page::DEFAULT_ROOT_PAGE_CODE) {
      $this->view->subPages = $page->getPublishedChildren();
    }

    $postModel = Publicada_Model_Posts::getInstance();
    $posts = $postModel->getPosts(16);
    $this->view->posts = $posts;

    $postCategoryModel = Publicada_Model_PostCategories::getInstance();
    $categories = $postCategoryModel->getAll();
    $this->view->categories = $categories;
  }
}