<?php

namespace UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class PageController extends Controller
{
  public function indexAction(){
    $Session = new Session();
    if($Session->has($this->container->getParameter('session_login'))){
      return $this->redirectToRoute('wechat_page_menu');
    }
    return $this->render('UserBundle:Page:index.html.twig');
  }

  public function nopermissionAction(){
      return $this->render('UserBundle:Page:nopermission.html.twig');
  }

  public function preferenceAction(){
    $dataSql = $this->container->get('my.dataSql');
    $list = $dataSql->getAdmins();
    return $this->render('UserBundle:Page:preference.html.twig', array('list' => $list));
  }

  public function notfoundAction(){
    return $this->render('UserBundle:Page:notfound.html.twig');
  }
}
