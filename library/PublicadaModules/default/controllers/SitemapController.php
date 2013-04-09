<?php

class SitemapController extends Publicada_Controller_Action
{
  public function pagesAction()
  {
    $this->_helper->viewRenderer->setNoRender(); // Disable the viewscript
    $this->_helper->layout->disableLayout(); // Disable the layout
    $this->getResponse()->setHeader('Content-Type', 'application/xml');

    $pageModel = Publicada_Model_Pages::getInstance();
    $pages = $pageModel->getAll(array('status' => Publicada_Model_Pages::STATUS_PUBLISHED, 'lang' => $this->_getParam('lang')));

    $dom = new DOMDocument();

    $urlset = $dom->createElement('urlset');
    $dom->appendChild($urlset);
    $dom->createAttributeNS('http://www.sitemaps.org/schemas/sitemap/0.9', 'xmlns');
    $dom->encoding = 'UTF-8';

    /**
     * Add the posts to the sitemap
     */
    foreach ($pages as $page) {
      /**
       * Create 'url' element
       */
      $url = $dom->createElement('url');

      /**
       * Create and append 'loc' element to 'url'
       */
      $loc = $dom->createElement('loc');
      $locTXT = $dom->createTextNode('http://dichev.net' . $this->view->url(array('code' => $page->code, 'lang' => $page->language), 'page'));
      $loc->appendChild($locTXT);
      $url->appendChild($loc);

      /**
       * Create and append 'lastmod'
       */
      $newDate = new Zend_Date($page->updated_at, Zend_Date::ISO_8601);
      $lastmod = $dom->createElement('lastmod');
      $lastmodTXT = $dom->createTextNode($newDate->toString('YYYY-MM-dd'));
      $lastmod->appendChild($lastmodTXT);
      $url->appendChild($lastmod);

      /**
       * Create and append 'changefreq' element to 'url'
       */
//			$changefreq = $dom->createElement('changefreq');
//			$changefreqTXT = $dom->createTextNode('monthly');
//			$changefreq->appendChild($changefreqTXT);
//			$url->appendChild($changefreq);

      /**
       * Create and append 'priority' element to 'url'
       */
      $priority = $dom->createElement('priority');
      $priorityTXT = $dom->createTextNode('1');
      $priority->appendChild($priorityTXT);
      $url->appendChild($priority);

      /**
       * Append the whole 'url' element to 'urlset'
       */

      $urlset->appendChild($url);
    }

    $dom->appendChild($urlset);

    echo $dom->saveXML();
  }

  public function postsAction()
  {
    $this->_helper->viewRenderer->setNoRender(); // Disable the viewscript
    $this->_helper->layout->disableLayout(); // Disable the layout
    $this->getResponse()->setHeader('Content-Type', 'application/xml');

    $postModel = Publicada_Model_Posts::getInstance();
    $posts = $postModel->getAll(array('status' => Publicada_Model_Posts::STATUS_PUBLISHED, 'lang' => $this->_getParam('lang')));

    $dom = new DOMDocument();

    $urlset = $dom->createElement('urlset');
    $dom->appendChild($urlset);
    $dom->createAttributeNS('http://www.sitemaps.org/schemas/sitemap/0.9', 'xmlns');
    $dom->encoding = 'UTF-8';

    /**
     * Add the posts to the sitemap
     */
    foreach ($posts as $post) {
      /**
       * Create 'url' element
       */
      $url = $dom->createElement('url');

      /**
       * Create and append 'loc' element to 'url'
       */
      $loc = $dom->createElement('loc');
      $locTXT = $dom->createTextNode('http://dichev.net' . $this->view->url(array('id' => $post->id, 'lang' => $post->language), 'post'));
      $loc->appendChild($locTXT);
      $url->appendChild($loc);

      /**
       * Create and append 'lastmod'
       */
      $newDate = new Zend_Date($post->updated_at, Zend_Date::ISO_8601);
      $lastmod = $dom->createElement('lastmod');
      $lastmodTXT = $dom->createTextNode($newDate->toString('YYYY-MM-dd'));
      $lastmod->appendChild($lastmodTXT);
      $url->appendChild($lastmod);

      /**
       * Create and append 'changefreq' element to 'url'
       */
//			$changefreq = $dom->createElement('changefreq');
//			$changefreqTXT = $dom->createTextNode('monthly');
//			$changefreq->appendChild($changefreqTXT);
//			$url->appendChild($changefreq);

      /**
       * Create and append 'priority' element to 'url'
       */
      $priority = $dom->createElement('priority');
      $priorityTXT = $dom->createTextNode('1');
      $priority->appendChild($priorityTXT);
      $url->appendChild($priority);

      /**
       * Append the whole 'url' element to 'urlset'
       */

      $urlset->appendChild($url);
    }

    $dom->appendChild($urlset);

    echo $dom->saveXML();
  }
}