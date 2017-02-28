<?php

namespace Wechat\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Filesystem\Filesystem;

class JssdkController extends Controller
{
  public function buildAction()
  {
    $form = $this->container->get('form.jssdkbuild');
    $data = $form->DoData();
    return  new Response(json_encode($data, JSON_UNESCAPED_UNICODE));
  }

  public function listAction()
  {
    $form = $this->container->get('form.jssdklist');
    $data = $form->DoData();
    return  new Response(json_encode($data, JSON_UNESCAPED_UNICODE));
  }

  public function infoAction()
  {
    $form = $this->container->get('form.jssdkinfo');
    $data = $form->DoData();
    return  new Response(json_encode($data, JSON_UNESCAPED_UNICODE));
  }

  public function updateAction()
  {
    $form = $this->container->get('form.jssdkupdate');
    $data = $form->DoData();
    return  new Response(json_encode($data, JSON_UNESCAPED_UNICODE));
  }

  public function deleteAction()
  {
    $form = $this->container->get('form.jssdkdelete');
    $data = $form->DoData();
    return  new Response(json_encode($data, JSON_UNESCAPED_UNICODE));
  }

  public function ownjsAction(){
    $fs = new Filesystem();
    $respose = new Response();
    $respose->headers->set('Content-Type', 'application/javascript');
    $wechat = $this->container->get('my.Wechat');
    if(isset($_SERVER['HTTP_REFERER'])){
      $url = $_SERVER['HTTP_REFERER'];
    }else{
      $url = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    }
    $jssdk = $wechat->getJsSDK($url);
    if($jssdk['code'] != 10)
      return new Response(json_encode($jssdk, JSON_UNESCAPED_UNICODE));
    $urll = "var wxsdk=".json_encode($jssdk['jssdk'], JSON_UNESCAPED_UNICODE).";\n";
    $respose->setContent($urll);
    return $respose->send();
  }

  public function jsAction($fid)
  {
    if(isset($_SERVER['HTTP_REFERER'])){
      $url = $_SERVER['HTTP_REFERER'];
    }else{
      $url = $this->container->get('request_stack')->getCurrentRequest()->getSchemeAndHttpHost().(isset($_SERVER['REQUEST_URI'])?$_SERVER['REQUEST_URI']:'');
    }
    $form = $this->container->get('form.jssdkjsfile');
    $form->getdata['jsid'] = $fid;
    $form->getdata['refererurl'] = $url;
    $data = $form->DoData();
    if(is_array($data))
      return new Response(json_encode($data, JSON_UNESCAPED_UNICODE));
    return new Response($data);
  }
}
