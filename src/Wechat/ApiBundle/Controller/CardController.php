<?php

namespace Wechat\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class CardController extends Controller
{
  public function cardsjsaddAction($cid){
    if(isset($_SERVER['HTTP_REFERER'])){
      $url = $_SERVER['HTTP_REFERER'];
    }else{
      $url = '';
    }
    $form = $this->container->get('form.cardjsadd');
    $form->getdata['cid'] = $cid;
    $form->getdata['refererurl'] = $url;
    $data = $form->DoData();
    return new Response($data);
  }

  public function cardsjschooseAction($cid){
    if(isset($_SERVER['HTTP_REFERER'])){
      $url = $_SERVER['HTTP_REFERER'];
    }else{
      $url = '';
    }
    $form = $this->container->get('form.cardsjschoose');
    $form->getdata['cid'] = $cid;
    $form->getdata['refererurl'] = $url;
    $data = $form->DoData();
    return new Response(json_encode($data, JSON_UNESCAPED_UNICODE));
  }
}
