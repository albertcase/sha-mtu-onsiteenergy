<?php

namespace Wechat\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('WechatApiBundle:Default:index.html.twig', array('name' => $name));
    }

    public function wechatAction()
    {
      $wechatObj = $this->container->get('my.Wechat.Response');
      if(isset($_GET["echostr"])){
        return new Response($wechatObj->valid($_GET["echostr"]));
      }
      return new Response($wechatObj->RequestFeedback());
    }

    public function uploadstoreAction()
    {
      $form = $this->container->get('form.uploadstore');
      $data = $form->DoData();
      return new Response(json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    public function reloadstoremapAction()
    {
      $sql = $this->container->get('my.dataSql');
      $fs = new \Symfony\Component\Filesystem\Filesystem();
      $stors = $sql->searchData(array() ,array(), 'stores');
      foreach($stors as $x){
        $center = explode('号', $x['address']);
        $center['0'] = str_replace(" ","",$center['0']);
        $x['storename'] = str_replace(" ","",$x['storename']);
        $url = "http://apis.map.qq.com/ws/staticmap/v2/?center={$center['0']}&key=T22BZ-4T3HX-4M64Y-7FRRM-5L7HT-MPBYF&zoom=17&markers=color:red|{$center['0']}&size=850*650&labels=border:0|size:13|color:0xff0000|anchor:0|offset:0_-5|{$x['storename']}|{$center['0']}";
        $image = file_get_contents($url);
        $path = 'source/change/store/'.$x['id'].'_map.jpg';
        $fs->dumpFile($path, $image);
      }
      return new Response(json_encode("success", JSON_UNESCAPED_UNICODE));
    }

    public function sharetokenAction(Request $request){//jsonp
      $wechat = $this->container->get('my.Wechat');
      $callback = $request->query->get('callback');
      $url = urldecode($request->query->get('url'));
      if(!$this->container->get('my.functions')->allowjssdk($url)){
        return new Response(json_encode(array('code' => '9', 'msg' => 'no permission domain')));
      }
      return new Response($callback.'('.$wechat->getJsSDK($url).')');
    }

    public function jssdkAction(){
      $wechat = $this->container->get('my.Wechat');
      // var_export($wechat->getJsSDK($_SERVER['HTTP_REFERER']));
      if(isset($_SERVER['HTTP_REFERER'])){
        $url = $_SERVER['HTTP_REFERER'];
      }else{
        $url = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
      }
      $urll = "var jssdk=".json_encode(array(
        "appid" => "appid",
        "time" => 'time',
        "noncestr" => "noncestr",
        "sign" => $url
      ), JSON_UNESCAPED_UNICODE).";";
      $respose = new Response($urll);
      $respose->headers->set('Content-Type', 'application/javascript');
      return $respose->send();
    }

    public function api1Action()
    {
      $fromid = uniqid();
      $postStr = "<xml><ToUserName><![CDATA[gh_2fa0c25f7db4]]></ToUserName><FromUserName><![CDATA[{$fromid}]]></FromUserName><CreateTime>1482392208</CreateTime><MsgType><![CDATA[event]]></MsgType><Event><![CDATA[subscribe]]></Event><EventKey><![CDATA[]]></EventKey></xml>";
      $wechatObj = $this->container->get('my.Wechat');
      return new Response($wechatObj->responseMsg($postStr));
    }
}
