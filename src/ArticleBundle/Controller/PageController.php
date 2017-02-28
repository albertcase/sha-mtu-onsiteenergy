<?php

namespace ArticleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class PageController extends Controller
{
  public function pageAction(){
    $sql = $this->container->get('my.dataSql');
    if(!$w = $sql->getArticlelist(array())){
      $w = array();
    }
    // $host = $this->getRequest()->getSchemeAndHttpHost();//2.6
    $host = $this->container->get('request_stack')->getCurrentRequest()->getSchemeAndHttpHost();//2.8
    return $this->render('ArticleBundle:Page:pag.html.twig', array('list' => $w, 'host' => $host));
  }

  public function articleAction($pageid = ''){
    $sql = $this->container->get('my.dataSql');
    $w = $sql->getArticle(array('pageid' => $pageid));
    if(!$w)
      return $this->render('UserBundle:Page:notfound.html.twig');
    $w = $w['0'];
    return $this->render('ArticleBundle:Page:article.html.twig', $w);
  }
}
