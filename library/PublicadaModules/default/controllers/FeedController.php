<?php

class FeedController extends Publicada_Controller_Action
{
  public function indexAction()
  {
    $this->_helper->viewRenderer->setNoRender(); // Disable the viewscript
    $this->_helper->layout->disableLayout(); // Disable the layout

    $optionModel = Publicada_Model_Options::getInstance();
    $siteName = $optionModel->getByCode('site_name')->option_value;

    $array = array(
      'title' => $siteName, // required
      'link' => 'http://dichev.net', // required
      'lastUpdate' => time(), // optional
      'published' => time(), // optional
      'charset' => 'utf8', // required
      'description' => 'dichev.net', // optional
      'author' => 'Jordan Dichev', // optional
      'email' => 'jordan@dichev.net', // optional
      'webmaster' => 'jordan@dichev.net',
      'copyright' => 'All rights reserved Jordan Dichev', // optional
      // 'image'       => 'http://www.example.com/logo.gif',		// optional
      'generator' => 'Publicada', // optional
      'ttl' => '60'
    );

    $postModel = Publicada_Model_Posts::getInstance();
    $posts = $postModel->getPosts();

    foreach ($posts as $post) {
      $array['entries'][] = array(
        'title' => $post['title'], //required
        'link' => 'http://' . $_SERVER['SERVER_NAME'] . $this->view->url(array('lang' => $this->_getParam('lang'), 'controller' => 'post', 'id' => $post['id']), 'post'),
        'description' => $post['description'],
        'content' => $post['body']
      );
    }

    $rssFeedFromArray = Zend_Feed::importArray($array, $this->_getParam('type'));
    $rssFeedFromArray->send();
  }
}