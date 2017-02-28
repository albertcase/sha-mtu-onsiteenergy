<?php

namespace Wechat\ApiBundle\Modals\classes;

use Wechat\ApiBundle\Modals\classes\WechatResponse;

class WechatSocialite{

  private $_container;
  private $_urls;
  private $_TOKEN = null;
  private $_appid = null;
  private $_secret = null;

  public function __construct($container){
    $this->_container = $container;
    $this->_TOKEN = $container->getParameter('wechat_Token');
    $this->_appid = $container->getParameter('wechat_AppID');
    $this->_secret = $container->getParameter('wechat_AppSecret');
  }

  public function getWechatKey($key = 'access_token'){
    if(!in_array($key, array('access_token', 'access_ticket')))
      return false;
    return call_user_func_array(array($this, 'WechatKey_'.$key), array());
  }

  public function WechatKey_access_token($renew = false){
    $EasyWechat = $this->_container->get('EasyWeChat.App');
    return $EasyWechat->access_token->getToken($renew);
  }

  public function WechatKey_access_jsapi_ticket($renew = false){
    $EasyWechat = $this->_container->get('EasyWeChat.App');
    return $EasyWechat->js->ticket($renew);
  }

  public function WechatKey_access_wxcard_ticket($renew = false){
    $EasyWechat = $this->_container->get('EasyWeChat.App');
    return $EasyWechat->card->getAPITicket($renew);
  }

  public function getAccessToken(){
    return $this->WechatKey_access_token();
  }

  public function getCardTicket(){
    return $this->WechatKey_access_wxcard_ticket();
  }

  public function getCardSign($datain){
    if(!is_array($datain) || !isset($datain['0']))
      return json_encode(array('code' => '9', 'msg' => 'input errors'));
    $EasyWechat = $this->_container->get('EasyWeChat.App');
    return $EasyWechat->card->jsConfigForAssign($datain);
  }

  public function getChooseCardSign($datain){
    if(!is_array($datain) || !isset($datain['card_type']))
      return false;
    $ticket = $this->getCardTicket();
    $str = '1234567890abcdefghijklmnopqrstuvwxyz';
    $noncestr = '';
    for($i=0;$i<8;$i++){
      $randval = mt_rand(0,35);
      $noncestr .= $str[$randval];
    }
    $timestamp = time();
    $list = array();
    $sortdata = array(
      'api_ticket' => isset($ticket)?$ticket:'',
      'time_stamp' => "{$timestamp}",
      'card_id' => isset($datain['card_id'])?$datain['card_id']:'',
      'location_id' => isset($datain['location_id'])?$datain['location_id']:'',
      'card_type' => isset($datain['card_type'])?$datain['card_type']:'',
      'nonce_str' => $noncestr,
      'app_id' => $this->_appid,
    );
    asort($sortdata);
    $signature = sha1(implode("", $sortdata));
    return array(
      'code' => '10',
      'msg' => 'success',
      'data' => array(
        'shopId' => $sortdata['location_id'],
        'cardType' => $sortdata['card_type'],
        'cardId' => $sortdata['card_id'],
        'timestamp' => $timestamp,
        'nonceStr' => $sortdata['nonce_str'],
        'signType' => 'SHA1',
        'cardSign' => $signature
      ),
    );
  }

  public function getJsSDK($url){
    $EasyWechat = $this->_container->get('EasyWeChat.App');
    $jssdk = $EasyWechat->js->signature($url);
    if(!$jssdk || !isset($jssdk))
      return array('code' => '11', 'msg' => 'signature error', 'jssdk' => array());
    return array(
      'code' => '10',
      'msg' => 'success',
      'jssdk' => array(
        "appid" => $jssdk['appId'],
        "time" => $jssdk['timestamp'],
        "noncestr" => $jssdk['nonceStr'],
        "sign" => $jssdk['signature'],
        "url" => $jssdk['url']
      ),
    );
  }

// creat_menu start
  public function buildmenu(){
    if(!$access_token = $this->getAccessToken())
      return false;
    $url = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token=ACCESS_TOKEN';
    $url = str_replace('ACCESS_TOKEN', $access_token ,$url);
    $result = $this->post_data($url, json_encode($this->create_menu_array(), JSON_UNESCAPED_UNICODE));
    if(!$result['errcode']){
      return true;
    }
    return $result['errmsg'];
  }

  public function checkmenuarray(){
    $menus = $this->create_menu_array();
    $menus = $menus['button'];
    foreach($menus as $x){
      if(strlen($x['name']) == 0)
        return array('code' => '11', 'msg' => 'the menu name not empty');
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

  public function create_menu_array(){
    $fun = $this->_container->get('my.functions');
    $data = array();
    $menus = $fun->getmenus();
    $this->buildsubmenu($data, $menus);
    return $this->filterbutton($data);
  }

  public function buildsubmenu(&$data, $menus){
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

  public function filterbutton($data){
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

  public function rebuildArray($data){
    $out = array();
    foreach($data as $x){
      $out[] = $x;
    }
    return $out;
  }

  public function deletekeys($data){
    foreach($data as $x => $x_val){
      if($x != 'sub_button' && $x != 'name' ){
          unset($data[$x]);
      }
    }
    return $data;
  }
// creat_menu end

//tag start
// @$data 'https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421140549&token=&lang=zh_CN'
  public function sendTagMsg($data){//push message by tags
    $access_token = $this->getAccessToken();
    $url = 'https://api.weixin.qq.com/cgi-bin/message/mass/sendall?access_token=ACCESS_TOKEN';
    $url = str_replace('ACCESS_TOKEN', $access_token, $url);
    $result = $this->post_data($url, $data);
    return $result;
  }

  public function sendTagPreviewMsg($data){//push message by tags
    $access_token = $this->getAccessToken();
    $url = 'https://api.weixin.qq.com/cgi-bin/message/mass/preview?access_token=ACCESS_TOKEN';
    $url = str_replace('ACCESS_TOKEN', $access_token, $url);
    $result = $this->post_data($url, $data);
    return $result;
  }
  //tag end

  public function getMateriallist($data){
    $access_token = $this->getAccessToken();
    $url = 'https://api.weixin.qq.com/cgi-bin/material/batchget_material?access_token=ACCESS_TOKEN';
    $url = str_replace('ACCESS_TOKEN', $access_token, $url);
    $result = $this->post_data($url, $data);
    return $result;
  }

  public function getWechatGroup(){
    $AccessToken = $this->getAccessToken();
    $url = 'https://api.weixin.qq.com/cgi-bin/tags/get?access_token=ACCESS_TOKEN';
    $url = str_replace('ACCESS_TOKEN', $AccessToken, $url);
    return $this->get_data($url);
  }

  public function qrcodeRegister($data){ //{"action_name": "QR_LIMIT_STR_SCENE", "action_info": {"scene": {"scene_str": "123"}}}
    $access_token = $this->getAccessToken();
    $url = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=ACCESS_TOKEN';
    $url = str_replace('ACCESS_TOKEN', $access_token, $url);
    return $this->post_data($url, $data);
  }

  // oauth2
    public function getoauth2url($goto, $snsapi = 'snsapi_userinfo', $state = ''){
      if(!in_array($snsapi, array('snsapi_userinfo', 'snsapi_base')))
        return false;
       $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=APPID&redirect_uri=REDIRECT_URI&response_type=code&scope=SCOPE&state=STATE#wechat_redirect';
       $url = str_replace('APPID', $this->_appid ,$url);
       $url = str_replace('REDIRECT_URI', $goto ,$url);
       $url = str_replace('SCOPE', $snsapi, $url);
       $url = str_replace('STATE', $state, $url);
       return $url;
     }

    public function getoauth2token($code){
      $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=APPID&secret=SECRET&code=CODE&grant_type=authorization_code';
      $url = str_replace('APPID', $this->_appid ,$url);
      $url = str_replace('SECRET', $this->_secret ,$url);
      $url = str_replace('CODE', $code, $url);
      return $this->get_data($url);
    }

    public function getoauthuserinfo($code){
      $oauth = $this->getoauth2token($code);
      if(!$oauth || isset($oauth['errcode']))
        return false;
      $url = 'https://api.weixin.qq.com/sns/userinfo?access_token=ACCESS_TOKEN&openid=OPENID&lang=zh_CN';
      $url = str_replace('ACCESS_TOKEN', isset($oauth['access_token'])?$oauth['access_token']:'', $url);
      $url = str_replace('OPENID', isset($oauth['openid'])?$oauth['openid']:'', $url);
      return $this->get_data($url);
    }

    public function getOpenidInfo($openid){
      $AccessToken = $this->getAccessToken();
      $url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token=ACCESS_TOKEN&openid=OPENID&lang=zh_CN';
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
      $url = 'https://api.weixin.qq.com/cgi-bin/user/get?access_token=ACCESS_TOKEN';
      $url = str_replace('ACCESS_TOKEN', $AccessToken, $url);
      if($next_openid){
        $url = $url."&next_openid=".$next_openid;
      }
      $info = $this->get_data($url);
      if(isset($info['errcode']))
        return array('code' => '9', 'info' => $info);
      return array('code' => '10', 'info' => $info);
    }

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
}
