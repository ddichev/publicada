<?php

class PageController extends Publicada_Controller_Action
{
  public function showAction()
  {
    $code = $this->_getParam('code');
    $pageModel = Publicada_Model_Pages::getInstance();
    $page = $pageModel->getByCode($code);
    $this->view->page = $page;

    if ($page === null) {
      throw new Zend_Exception("Page not found with code '{$code}'");
    }

    $this->setViewMetaData($page);

    if ($page->renderer == 'blog') {
      $page_at = $this->_getParam('page');
      $this->view->description = $this->view->description . ' ' . $this->view->translate('site.page') . ' ' . $page_at;
      $postModel = Publicada_Model_Posts::getInstance();
      $posts = $postModel->getPosts(8, $page_at);
      $this->view->posts = $posts;

      $postCategoryModel = Publicada_Model_PostCategories::getInstance();
      $categories = $postCategoryModel->getCategories('category1');
      $this->view->categories = $categories;
    }

    $this->_helper->layout->setLayout('layouts/general/' . $page->renderer);

    $mainPage = $pageModel->getByCode(Publicada_Model_Page::DEFAULT_ROOT_PAGE_CODE);
    $this->view->mainPages = $mainPage->getPublishedChildren();

    $this->view->subPages = $page->getPublishedChildren();
  }
}
