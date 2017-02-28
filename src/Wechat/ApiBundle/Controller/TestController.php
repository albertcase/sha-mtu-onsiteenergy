<?php

namespace Wechat\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class TestController extends Controller
{
  public function cardsignAction(){
    // $wechat = $this->container->get('my.Wechat');
    // $datain = array(
    //   array(
    //     'card_id' => 'p-jLIwVLOyheVf131TxZ3_IGg3Qo',
    //   ),
    // );
    // $data = $wechat->getCardSign($datain);
    $data = json_encode(array('code' => 9, 'list' => $_SERVER));
    return new Response($data);
  }

  public function cardpageAction(){
    // $wechat = $this->container->get('EasyWeChat.App');
    // // $wechat->set('aaaaa' ,'bbbbbb');
    return $this->render('WechatApiBundle:Test:index.html.twig');
    // $token = $wechat->access_token->getToken();
    // print_r($token);
    return new Response(json_encode(array("LLLLLLLLLLLL"), JSON_UNESCAPED_UNICODE));
  }

  public function oauthAction(Request $request){
    $access_token = $request->query->get('access_token','');
    $openid = $request->query->get('openid','');
    if($access_token){
      $url = 'https://api.weixin.qq.com/sns/userinfo?access_token=ACCESS_TOKEN&openid=OPENID&lang=zh_CN';
      $url = str_replace('ACCESS_TOKEN', $access_token, $url);
      $url = str_replace('OPENID', $openid, $url);
      print_r($this->container->get('my.Wechat')->get_data($url));
      return new Response("AAAAAAAAA");
    }
    $goto = 'http://dirc.samesamechina.com/v1/wx/web/oauth2/authorize?redirect_uri=http%3a%2f%2fdirc.samesamechina.com' . urlencode($_SERVER['REQUEST_URI']);
    return $this->redirect($goto);
  }

}
