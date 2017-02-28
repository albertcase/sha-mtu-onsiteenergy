<?php

namespace Wechat\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Filesystem\Filesystem;

class PageController extends Controller
{
  public function menuAction(){
    $functions = $this->container->get('my.functions');
    $menus = $functions->getmenus();$menus=array();
    return $this->render('WechatApiBundle:Page:menu.html.twig', array('menus' => $menus));
  }

  public function keywordAction(){
    $sql = $this->container->get('my.dataSql');
    $wordlist = $sql->getkeywordlist();
    return $this->render('WechatApiBundle:Page:keyword.html.twig', array('wordlist' => $wordlist));
  }

  public function replyAction(){
    return $this->render('WechatApiBundle:Page:replay.html.twig');
  }

  public function jssdkAction(){
    return $this->render('WechatApiBundle:Page:jssdk.html.twig',array('host' => $this->container->get('request_stack')->getCurrentRequest()->getSchemeAndHttpHost()));
  }

  public function qrcodeAction(){
    return $this->render('WechatApiBundle:Page:qrcode.html.twig');
  }

  public function oauthAction(){
    return $this->render('WechatApiBundle:Page:oauth.html.twig',array('host' => $this->container->get('request_stack')->getCurrentRequest()->getSchemeAndHttpHost()));
  }

  public function storesAction(){
    return $this->render('WechatApiBundle:Page:stores.html.twig');
  }

  public function groupnewsAction(){
    $wehcat = $this->container->get('my.Wechat');
    $search = array(
      'type' => 'news',
      'offset' => '0',
      'count' => '18',
    );
    // $check = $wehcat->getMateriallist($search);
    // if(isset($check['errcode'])){
      // $data = array();
    // }else{
      // $data = $check['item'];
      // $this->container->get('my.dataSql')->syncMaterial($data);
      // foreach($data as $x => $x_val){
      //   foreach($x_val['content']['news_item'] as $xx => $xx_val){
      //     $data[$x]['content']['news_item'][$xx]['thumb_url'] = '/cimg.php?style=w_400&image='.urlencode($data[$x]['content']['news_item'][$xx]['thumb_url']);
      //   }
      // }
    // }
    $data = array();
    return $this->render('WechatApiBundle:Page:groupnews.html.twig', array('newslist' => $data));
  }

  public function storeAction($id){
    $store = $this->container->get('my.dataSql')->searchData(array('id' => $id) ,array(), 'stores');
    if(!$store)
      return $this->render('UserBundle:Page:notfound.html.twig');
    $store = $store[0];
    return $this->render('WechatApiBundle:Page:store.html.twig', $store);
  }

  public function testAction(Request $request){
    $filename = "upload/feedbacks.php";
    $fs = new Filesystem();
    ob_start();
    print "<?php\nreturn ";
    var_export($GLOBALS["HTTP_RAW_POST_DATA"]);
    $string = ob_get_contents();
    ob_end_clean();
    $fs->dumpFile($filename, $string.";");
    return  new Response(json_encode("aaaaaaaaaaa", JSON_UNESCAPED_UNICODE));
  }

  public function buildphpcacheAction(Request $request){
    $fs = new Filesystem();
    if(!$fs->exists(dirname('upload/wechatcache/feedbacks.php')));
      $fs->mkdir(dirname('upload/wechatcache/feedbacks.php'));
    $lsa = $this->container->get('my.dataSql')->searchData(array() ,array(), 'wechat_feedbacks');
    $lsb = $this->container->get('my.dataSql')->searchData(array() ,array(), 'wechat_events');
    $feedbacks = array();
    $keywords = array();
    $events = array();
    foreach ($lsa as $value) {
      if($value['menuId'])
        $feedbacks[$value['menuId']] = array(
          'menuId' => $value['menuId'],
          'MsgType' => $value['MsgType'],
          'MsgData' => $value['MsgData'],
        );
    }
    // print_r($list);
    ob_start();
    print "<?php\nreturn ";
    var_export($feedbacks);
    $string = ob_get_contents();
    ob_end_clean();
    $fs->dumpFile("upload/wechatcache/feedbacks.php", $string.";");

    foreach ($lsb as $value) {
      if($value['getContent'])
        $keywords[$value['getContent']] = array(
          'menuId' => $value['menuId'],
          'MsgType' => $value['MsgType']
        );
    }
    ob_start();
    print "<?php\nreturn ";
    var_export($keywords);
    $string = ob_get_contents();
    ob_end_clean();
    $fs->dumpFile("upload/wechatcache/keywords.php", $string.";");

    foreach ($lsb as $value) {
      if($value['getEventKey'])
        $events[$value['getEventKey']] = array(
          'menuId' => $value['menuId'],
          'MsgType' => $value['MsgType']
        );
      if($value['getEvent'] && ($value['getEvent'] == 'subscribe' || $value['getEvent'] == 'defaultback'))
        $events[$value['getEvent']] = array(
          'menuId' => $value['menuId'],
          'MsgType' => $value['MsgType']
        );
    }
    ob_start();
    print "<?php\nreturn ";
    var_export($events);
    $string = ob_get_contents();
    ob_end_clean();
    $fs->dumpFile("upload/wechatcache/events.php", $string.";");

    $lsc = $this->container->get('my.dataSql')->searchData(array() ,array(), 'wechat_qrcode');
    $qrcode = array();
    foreach ($lsc as $value) {
      if($value['qrTicket'])
        $qrcode[$value['qrTicket']] = array(
          'feedbackid' => $value['feedbackid']
        );
    }
    ob_start();
    print "<?php\nreturn ";
    var_export($qrcode);
    $string = ob_get_contents();
    ob_end_clean();
    $fs->dumpFile("upload/wechatcache/qrcodes.php", $string.";");
// lbs
    $lsd = $this->container->get('my.dataSql')->searchData(array() ,array(), 'stores');
    $stores = array();
      if($lsd)
        $stores = $lsd;
    ob_start();
    print "<?php\nreturn ";
    var_export($stores);
    $string = ob_get_contents();
    ob_end_clean();
    $fs->dumpFile("upload/wechatcache/stores.php", $string.";");
// jssdkids
    $jssdk = $this->container->getParameter('wechat_jssdkids');
    $stores = array();
      if($jssdk)
        $stores = $jssdk;
    ob_start();
    print "<?php\nreturn ";
    var_export($stores);
    $string = ob_get_contents();
    ob_end_clean();
    $fs->dumpFile("upload/wechatcache/jssdkids.php", $string.";");
    return  new Response(json_encode("success", JSON_UNESCAPED_UNICODE));
  }

  public function test2Action(Request $request){
    // print_r($request->query);print "\n";
    // print_r($_COOKIE);
    return $this->render('WechatApiBundle:Page:test.html.twig');
  }
}
