<?php

namespace Wechat\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Cookie;

class OauthController extends Controller
{
  public function oauth2Action(Request $request){
    $callback = urldecode($request->query->get('callback'));
    $oauths = $request->query->get('oauths', 1);
    $code = $request->query->get('code');
    $openid = $request->cookies->get('woauths');
    if(intval($oauths) > 1){
        $functions = $this->container->get('my.functions');
        if($functions->updataWechatUser($code) || $functions->openidDecode($openid)){
          return $this->redirect($callback);
        }
        if(intval($oauths) > 4){ //the more oauth error times;
          return  new Response('oauth error');
        }
    }
    $oauths = intval($oauths) + 1;
    // $Request = $this->getRequest();//2.6
    $Request = $this->container->get('request_stack')->getCurrentRequest();//2.8
    $goto = $Request->getSchemeAndHttpHost().$Request->getBaseUrl().$Request->getPathInfo().'?oauths='.$oauths.'&callback='.urlencode($callback);
    return $this->redirect($this->container->get('my.Wechat')->getoauth2url(urlencode($goto)));
  }

  public function vendorAction(){
    $form = $this->container->get('form.oauthorize');
    $data = $form->DoData();
    if($data['code'] == '10')
      return $this->redirect($data['redirect']);
    return new Response(json_encode($data, JSON_UNESCAPED_UNICODE));
  }

  public function addAction()
  {
    $form = $this->container->get('form.oauthadd');
    $data = $form->DoData();
    return  new Response(json_encode($data, JSON_UNESCAPED_UNICODE));
  }

  public function listAction()
  {
    $form = $this->container->get('form.oauthlist');
    $data = $form->DoData();
    return  new Response(json_encode($data, JSON_UNESCAPED_UNICODE));
  }

  public function infoAction()
  {
    $form = $this->container->get('form.oauthinfo');
    $data = $form->DoData();
    return  new Response(json_encode($data, JSON_UNESCAPED_UNICODE));
  }

  public function updateAction()
  {
    $form = $this->container->get('form.oauthupdate');
    $data = $form->DoData();
    return  new Response(json_encode($data, JSON_UNESCAPED_UNICODE));
  }

  public function deleteAction()
  {
    $form = $this->container->get('form.oauthdelete');
    $data = $form->DoData();
    return  new Response(json_encode($data, JSON_UNESCAPED_UNICODE));
  }
}
