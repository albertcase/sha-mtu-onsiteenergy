<?php

namespace Wechat\ApiBundle\Modals\classes;

use Wechat\ApiBundle\Modals\classes\WechatResponse;

class Wechat{

  private $_container;
  private $_urls;
  private $_TOKEN = null;
  private $_appid = null;
  private $_secret = null;

  public function __construct($container){
    $this->_container = $container;
    $this->setUrls();
    $this->_TOKEN = $container->getParameter('wechat_Token');
    $this->_appid = $container->getParameter('wechat_AppID');
    $this->_secret = $container->getParameter('wechat_AppSecret');
  }

  public function valid($echoStr){
   if($this->checkSignature())
      return $echoStr;
  }

  private function checkSignature()
  {
    $signature = $_GET["signature"];
    $timestamp = $_GET["timestamp"];
    $nonce = $_GET["nonce"];
    $token = $this->_TOKEN;
    $tmpArr = array($token, $timestamp, $nonce);
    sort($tmpArr, SORT_STRING);
    $tmpStr = implode( $tmpArr );
    $tmpStr = sha1( $tmpStr );

    if( $tmpStr == $signature ){
      return true;
    }else{
      return false;
    }
  }

  // response function *******************************************************
  public function responseMsg($postStr){
    if (!empty($postStr)){
      $WechatResponse = new WechatResponse($postStr, $this->_container);
      return $WechatResponse->RequestFeedback();
    }
    return "";
  }
// response function end *****************************************************
  public function getWechatKey($key = 'access_token'){
    if(!in_array($key, array('access_token', 'access_ticket')))
      return false;
    return call_user_func_array(array($this, $key.'_Renew'), array());
  }

  public function access_token_Renew(){
    $redis = $this->_container->get('my.RedisLogic');
    $access_token = $redis->getString('access_token');
    if(!$access_token){
      $url = $this->_urls['access_token'];
      $url = str_replace('APPID',$this->_appid ,$url);
      $url = str_replace('APPSECRET',$this->_secret ,$url);
      $rs = $this->get_data($url);
      if(isset($rs['access_token'])){
        $access_token = $rs['access_token'];
        $this->getTicket($access_token);
        $redis->setString('access_token', $access_token, 5000);
        return $rs['access_token'];
      }else{
        return false;
      }
    }
    return $access_token;
  }

  public function access_ticket_Renew(){
    $access_ticket = $this->_container->get('my.RedisLogic')->getString('access_ticket');
    if(!$access_ticket){
      $this->access_token_Renew();
      $access_ticket = $this->_container->get('my.RedisLogic')->getString('access_ticket');
    }
    return $access_ticket;
  }
// token and Ticket start

  public function getJsSDK($url){
    $time = time();
    $this->getAccessToken();
    $redis = $this->_container->get('my.RedisLogic');
    // $time = $redis->getString('token_time');
    $ticket = $redis->getString('access_ticket');
    $str = '1234567890abcdefghijklmnopqrstuvwxyz';
    $noncestr = '';
    for($i=0;$i<8;$i++){
      $randval = mt_rand(0,35);
      $noncestr .= $str[$randval];
    }
    $ticketstr = "jsapi_ticket=" . $ticket . "&noncestr=" . $noncestr . "&timestamp=" . $time . "&url=" . $url;
    $sign = sha1($ticketstr);
    $jssdk = array("appid" => $this->_appid,"time" => $time, "noncestr" => $noncestr, "sign" => $sign, "url" => $url);
    return array('code' => '10', 'msg' => 'success', 'jssdk' => $jssdk);
  }

  public function getTicket($access_token){
    $redis = $this->_container->get('my.RedisLogic');
    $url = $this->_urls['access_api_ticket'];
    $url = str_replace('ACCESS_TOKEN', $access_token ,$url);
    $ticketfile = $this->get_data($url);
    $redis->setString('access_ticket', $ticketfile['ticket'], 5000);
  }

  public function getCardSign($datain){
    if(!is_array($datain) || !isset($datain['0']))
      return array('code' => '9', 'msg' => 'input errors');
    $ticket = $this->getCardTicket();
    if($ticket && isset($ticket['code']) && $ticket['code'] == '10'){
      $str = '1234567890abcdefghijklmnopqrstuvwxyz';
      $noncestr = '';
      for($i=0;$i<8;$i++){
        $randval = mt_rand(0,35);
        $noncestr .= $str[$randval];
      }
      $timestamp = time();
      $list = array();
      foreach($datain as $x){
        if(isset($x['card_id']) && $x['card_id']){
          $sortdata = array(
            'api_ticket' => isset($ticket['ticket'])?$ticket['ticket']:'',
            'timestamp' => "{$timestamp}",
            'card_id' => $x['card_id'],
            'code' => isset($x['code'])?$x['code']:'',
            'openid' => isset($x['openid'])?$x['openid']:'',
            'nonce_str' => $noncestr
          );
          asort($sortdata);
          $signature = sha1(implode("", $sortdata));
          $list[] = array(
            'cardId' => $x['card_id'],
            'cardExt' => array(
              'code' => $sortdata['code'],
              'openid' => $sortdata['openid'],
              'timestamp' => $timestamp,
              'balance' => '',
              'signature' => $signature,
              'nonce_str' => $noncestr,
              'ticket' => $ticket['ticket']
            )
          );
        }
      }
      return array(
        'code' => '10',
        'msg' => 'success',
        'data' => array(
          'cardList' => $list,
        ),
      );
    }
    return array('code' => '11', 'msg' => $ticket['msg']);
  }

  public function getCardTicket(){//ready
    $redis = $this->_container->get('my.RedisLogic');
    if($ticket = $redis->getString('access_wxcard_ticket'))
      return array('code' => '10', 'msg' => 'ok', 'ticket' => $ticket);
    if(!$access_token = $this->getAccessToken())
      return array('code' => '9', 'msg' => 'token errors');
    $url = $this->_urls['access_wxcard_ticket'];
    $url = str_replace('ACCESS_TOKEN', $access_token ,$url);
    $ticketfile = $this->get_data($url);
    if(isset($ticketfile['ticket'])){
      $redis->setString('access_wxcard_ticket', $ticketfile['ticket'], 5000);
      return array('code' => '10', 'msg' => 'ok', 'ticket' => $ticketfile['ticket']);
    }else{
      return array('code' => '11', 'msg' => $ticketfile['errmsg']);
    }
  }

  public function getAccessToken()//ready
  {
    $time = 0;
    $access_token = 0;
    $redis = $this->_container->get('my.RedisLogic');
    // if($redis->checkString('token_time')){
      // $time = $redis->getString('token_time');
      $access_token = $redis->getString('access_token');
    // }
    // if (!$time || (time() - $time >= 3600) || !$access_token){
    if(!$access_token){
      $url = $this->_urls['access_token'];
      $url = str_replace('APPID',$this->_appid ,$url);
      $url = str_replace('APPSECRET',$this->_secret ,$url);
      $rs = $this->get_data($url);
      if(isset($rs['access_token'])){
        $access_token = $rs['access_token'];
        $this->getTicket($access_token);
        // $redis->setString('token_time', time(), 5000);
        $redis->setString('access_token', $access_token, 5000);
        return $rs['access_token'];
      }else{
        return false;
      }
    }
    return $access_token;
  }
// token and Ticket start

// creat_menu start
  public function buildmenu(){//ready
    if(!$access_token = $this->getAccessToken())
      return false;
    $url = $this->_urls['create_menu'];
    $url = str_replace('ACCESS_TOKEN', $access_token ,$url);
    $result = $this->post_data($url, json_encode($this->create_menu_array(), JSON_UNESCAPED_UNICODE));
    if(!$result['errcode']){
      return true;
    }
    return $result['errmsg'];
  }

  public function checkmenuarray(){//ready
    $menus = $this->create_menu_array();
    $menus = $menus['button'];
    foreach($menus as $x){
      if(strlen($x['name']) > 16)
        return array('code' => '11', 'msg' => 'the length of menu "'.$x['name'].'" name not more than 16');
      if(!isset($x['sub_button']) && !isset($x['type'])){
        return array('code' => '11', 'msg' => 'the main menu "'.$x['name'].'" not have a feedback event');
      }
      if(isset($x['sub_button'])){
        foreach($x['sub_button'] as $xx){
          if(strlen($xx['name']) > 40)
            return array('code' => '11', 'msg' => 'the length of submenu "'.$xx['name'].'" name not more than 40');
          if(!isset($xx['type'])){
            return array('code' => '11', 'msg' => 'the submenu "'.$xx['name'].'" not have a feedback event');
          }
        }
      }
    }
    return true;
  }

  public function create_menu_array(){//ready
    $fun = $this->_container->get('my.functions');
    $data = array();
    $menus = $fun->getmenus();
    $this->buildsubmenu($data, $menus);
    return $this->filterbutton($data);
  }

  public function buildsubmenu(&$data, $menus){//ready
    foreach($menus as $x){
      if(isset($x['data'])){
        $tem = array(
          'name' => $x['data']['menuName'],
        );
        if(isset($x['son']) && count($x['son'])){
          $tem['sub_button'] = array();
          $this->buildsubmenu($tem['sub_button'], $x['son']);
        }else{
          foreach($x['data'] as $xx => $xx_val){
            if($xx_val){
              if($xx == 'eventtype')
                $tem['type'] = $xx_val;
              if($xx == 'eventKey')
                $tem['key'] = $xx_val;
              if($xx == 'eventUrl')
                $tem['url'] = $xx_val;
              if($xx == 'eventmedia_id')
                $tem['media_id'] = $xx_val;
            }
          }
        }
        array_push($data, $tem);
      }
    }
  }

  public function filterbutton($data){//ready
    $out = array();
    $ox = 0;
    foreach($data as $x){
      if(isset($x['sub_button'])){
        $out[$ox] = $this->deletekeys($x);
        $out[$ox]['sub_button'] = $this->rebuildArray($out[$ox]['sub_button']);
      }else{
        $out[$ox] = $x;
      }
      $ox++;
    }
    return array('button' => $out);
  }

  public function rebuildArray($data){//ready
    $out = array();
    foreach($data as $x){
      $out[] = $x;
    }
    return $out;
  }

  public function deletekeys($data){//ready
    foreach($data as $x => $x_val){
      if($x != 'sub_button' && $x != 'name' ){
          unset($data[$x]);
      }
    }
    return $data;
  }
// creat_menu end

//subfunction
  public function get_data($url, $return_array = true){
      if($return_array)
        return json_decode( file_get_contents($url), true );
      return file_get_contents($url);
  }

  public function post_data($url, $param, $is_file = false, $return_array = true){
    if (! $is_file && is_array ( $param )) {
    $param = $this->JSON ( $param );
    }
    if ($is_file) {
    $header [] = "content-type: multipart/form-data; charset=UTF-8";
    } else {
    $header [] = "content-type: application/json; charset=UTF-8";
    }
    $ch = curl_init ();
    curl_setopt ( $ch, CURLOPT_URL, $url );
    curl_setopt ( $ch, CURLOPT_CUSTOMREQUEST, "POST" );
    curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
    curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, FALSE );
    curl_setopt ( $ch, CURLOPT_HTTPHEADER, $header );
    curl_setopt ( $ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)' );
    curl_setopt ( $ch, CURLOPT_FOLLOWLOCATION, 1 );
    curl_setopt ( $ch, CURLOPT_AUTOREFERER, 1 );
    curl_setopt ( $ch, CURLOPT_POSTFIELDS, $param );
    curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
    $res = curl_exec ( $ch );

    // 	$flat = curl_errno ( $ch );
    // 	if ($flat) {
    // 		$data = curl_error ( $ch );
    // 		addWeixinLog ( $flat, 'post_data flat' );
    // 		addWeixinLog ( $data, 'post_data msg' );
    // 	}

    curl_close ( $ch );

    if($return_array)
    $res = json_decode ( $res, true );
    return $res;
  }

  public function arrayRecursive(&$array, $function, $apply_to_keys_also = false) {
    static $recursive_counter = 0;
    if (++ $recursive_counter > 1000) {
    die ( 'possible deep recursion attack' );
    }
    foreach ( $array as $key => $value ) {
    if (is_array ( $value )) {
      $this->arrayRecursive ( $array [$key], $function, $apply_to_keys_also );
    } else {
      $array [$key] = $function ( $value );
    }

    if ($apply_to_keys_also && is_string ( $key )) {
      $new_key = $function ( $key );
      if ($new_key != $key) {
        $array [$new_key] = $array [$key];
        unset ( $array [$key] );
      }
    }
    }
    $recursive_counter --;
  }

  public function JSON($array) {
    $this->arrayRecursive ( $array, 'urlencode', true );
    $json = json_encode ( $array, JSON_UNESCAPED_UNICODE);
    return urldecode ( $json );
  }
// tag control
  public function adduserTags($data){
    $access_token = $this->getAccessToken();
    $url = $this->_urls['add_tags'];
    $url = str_replace('ACCESS_TOKEN', $access_token, $url);
    $this->post_data($url, $data);
    return true;
  }

  public function deluserTags($data){
    $access_token = $this->getAccessToken();
    $url = $this->_urls['del_tags'];
    $url = str_replace('ACCESS_TOKEN', $access_token, $url);
    $this->post_data($url, $data);
    return true;
  }

  // @$data 'https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421140549&token=&lang=zh_CN'
  public function sendTagMsg($data){//push message by tags  //ready
    $access_token = $this->getAccessToken();
    $url = $this->_urls['tag_msg'];
    $url = str_replace('ACCESS_TOKEN', $access_token, $url);
    $result = $this->post_data($url, $data);
    return $result;
  }

  public function sendTagPreviewMsg($data){//push message by tags
    $access_token = $this->getAccessToken();
    $url = $this->_urls['tag_msg_preview'];
    $url = str_replace('ACCESS_TOKEN', $access_token, $url);
    $result = $this->post_data($url, $data);
    return $result;
  }

  public function getMateriallist($data){
    $access_token = $this->getAccessToken();
    $url = $this->_urls['batchget_material'];
    $url = str_replace('ACCESS_TOKEN', $access_token, $url);
    $result = $this->post_data($url, $data);
    return $result;
  }

  public function getWechatGroup(){
    $AccessToken = $this->getAccessToken();
    $url = $this->_urls['wechat_group'];
    $url = str_replace('ACCESS_TOKEN', $AccessToken, $url);
    return $this->get_data($url);
  }
// tag control end

  public function qrcodeRegister($data){ //{"action_name": "QR_LIMIT_STR_SCENE", "action_info": {"scene": {"scene_str": "123"}}}
    $access_token = $this->getAccessToken();
    $url = $this->_urls['wechat_qrcode'];
    $url = str_replace('ACCESS_TOKEN', $access_token, $url);
    return $this->post_data($url, $data);
  }

// oauth2
  public function getoauth2url($goto, $snsapi = 'snsapi_userinfo', $state = ''){//ready
    if(!in_array($snsapi, array('snsapi_userinfo', 'snsapi_base')))
      return false;
     $url = $this->_urls['oauth2_code'];
     $url = str_replace('APPID', $this->_appid ,$url);
     $url = str_replace('REDIRECT_URI', $goto ,$url);
     $url = str_replace('SCOPE', $snsapi, $url);
     $url = str_replace('STATE', $state, $url);
     return $url;
   }

  public function getoauth2token($code){
    $url = $this->_urls['oauth2_token'];
    $url = str_replace('APPID', $this->_appid ,$url);
    $url = str_replace('SECRET', $this->_secret ,$url);
    $url = str_replace('CODE', $code, $url);
    return $this->get_data($url);
  }

  public function getoauthuserinfo($code){
    $oauth = $this->getoauth2token($code);
    if(!$oauth || isset($oauth['errcode']))
      return false;
    $url = $this->_urls['oauth2_token_userinfo'];
    $url = str_replace('ACCESS_TOKEN', isset($oauth['access_token'])?$oauth['access_token']:'', $url);
    $url = str_replace('OPENID', isset($oauth['openid'])?$oauth['openid']:'', $url);
    return $this->get_data($url);
  }

  public function getOpenidInfo($openid){
    $AccessToken = $this->getAccessToken();
    $url = $this->_urls['token_userinfo'];
    $url = str_replace('ACCESS_TOKEN', $AccessToken, $url);
    $url = str_replace('OPENID', $openid, $url);
    $info = $this->get_data($url);
    if(isset($info['errcode']))
    return array('code' => '9', 'info' => $info);
  return array('code' => '10', 'info' => $info);
  }
// oauth2 end
  public function getOpenidlist($next_openid = ''){
    $AccessToken = $this->getAccessToken();
    $url = $this->_urls['wechat_openid_list'];
    $url = str_replace('ACCESS_TOKEN', $AccessToken, $url);
    if($next_openid){
      $url = $url."&next_openid=".$next_openid;
    }
    $info = $this->get_data($url);
    if(isset($info['errcode']))
      return array('code' => '9', 'info' => $info);
    return array('code' => '10', 'info' => $info);
  }

  public function setUrls(){
    $this->_urls = array(
      'access_token' => 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=APPID&secret=APPSECRET',
      'access_api_ticket' => 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=ACCESS_TOKEN&type=jsapi',
      'access_wxcard_ticket' => 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=ACCESS_TOKEN&type=wx_card',
      'create_menu' => 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token=ACCESS_TOKEN',
      'custom_msend' => 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=ACCESS_TOKEN',
      'oauth2_code' => 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=APPID&redirect_uri=REDIRECT_URI&response_type=code&scope=SCOPE&state=STATE#wechat_redirect',
      'oauth2_token' => 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=APPID&secret=SECRET&code=CODE&grant_type=authorization_code',
      'oauth2_refresh_token' => 'https://api.weixin.qq.com/sns/oauth2/refresh_token?appid=APPID&grant_type=refresh_token&refresh_token=REFRESH_TOKEN',
      'oauth2_token_userinfo' => 'https://api.weixin.qq.com/sns/userinfo?access_token=ACCESS_TOKEN&openid=OPENID&lang=zh_CN',
      'add_tags' => 'https://api.weixin.qq.com/cgi-bin/tags/members/batchtagging?access_token=ACCESS_TOKEN',
      'del_tags' => 'https://api.weixin.qq.com/cgi-bin/tags/members/batchuntagging?access_token=ACCESS_TOKEN',
      'tag_msg' => 'https://api.weixin.qq.com/cgi-bin/message/mass/sendall?access_token=ACCESS_TOKEN',
      'tag_msg_preview' => 'https://api.weixin.qq.com/cgi-bin/message/mass/preview?access_token=ACCESS_TOKEN',
      'batchget_material' => 'https://api.weixin.qq.com/cgi-bin/material/batchget_material?access_token=ACCESS_TOKEN',
      'token_userinfo' => 'https://api.weixin.qq.com/cgi-bin/user/info?access_token=ACCESS_TOKEN&openid=OPENID&lang=zh_CN ',
      'wechat_group' => 'https://api.weixin.qq.com/cgi-bin/tags/get?access_token=ACCESS_TOKEN',
      'wechat_qrcode' => 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=ACCESS_TOKEN',
      'wechat_openid_list' => 'https://api.weixin.qq.com/cgi-bin/user/get?access_token=ACCESS_TOKEN'
    );
  }




}
