<?php
namespace Wechat\ApiBundle\Modals\CustomMsg;

class customsResponse
{
  private $_wechatinfo = array();
  private $_TOKEN = null;
  private $_appid = null;
  private $_secret = null;
  private $_redis;
  private $prostr = null;
  private $list = 'list';
  private $changT = 'changT';
  private $outtime = '100';

  public function __construct(){
    $this->_wechatinfo = require_once dirname(__FILE__).'/../script/wechatinfo.php';
    $this->_TOKEN = $this->_wechatinfo['TOKEN'];
    $this->_appid = $this->_wechatinfo['appid'];
    $this->_secret = $this->_wechatinfo['secret'];
    $this->prostr = $this->_wechatinfo['prostr'].'custom:';
    $this->_redis = new \Redis();
    $this->_redis->connect('127.0.0.1', '6379');
    $this->closeCustomMsg();
  }

//system list
  public function addCustomMsg($data){
    if(isset($data['msgtype']) && method_exists($this, $data['msgtype'].'CustomMsg')){
      $msg = call_user_func_array(array($this, $data['msgtype'].'CustomMsg'), array($data));
      $this->_redis->rPush($this->prostr.$this->list, json_encode($msg ,JSON_UNESCAPED_UNICODE));
    }
  }

  public function sendCustomMsg(){
    exec("nohup ".dirname(__FILE__)."/sendMsg.sh >>".dirname(__FILE__)."/sendMsg.log 2>&1 &");
  }

  public function testsendMsg(){
    while($this->ststus())
    {
      $this->pushMsg();
    }
  }

  public function closeCustomMsg(){
    if($time = $this->_redis->get($this->prostr.$this->changT)){
      if((time() - $time) > $this->outtime){
        exec("nohup ".dirname(__FILE__)."/colseMsg.sh >>".dirname(__FILE__)."/colseMsg.log 2>&1 &");
        $this->_redis->delete($this->prostr.$this->changT);
      }
    }
  }
//system list end

  public function custom_msend($data){
    $token = $this->getAccessToken();
    if(!$token)
      return false;
    $url = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=ACCESS_TOKEN';
    $url = str_replace('ACCESS_TOKEN',$token ,$url);
    return $this->post_data($url, $data);
  }

  public function getTicket($access_token){
    $url = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=ACCESS_TOKEN&type=jsapi';
    $url = str_replace('ACCESS_TOKEN', $access_token ,$url);
    $ticketfile = $this->get_data($url);
    $this->_redis->set($this->_wechatinfo['access_ticket'], $ticketfile['ticket']);
    $this->_redis->setTimeout($this->_wechatinfo['access_ticket'], 5000);
  }

  public function getAccessToken()
  {
    $time = 0;
    $access_token = 0;
    if($this->_redis->exists($this->_wechatinfo['token_time'])){
      $time = $this->_redis->get($this->_wechatinfo['token_time']);
      $access_token = $this->_redis->get($this->_wechatinfo['access_token']);
    }
    if (!$time || (time() - $time >= 3600) || !$access_token){
      $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=APPID&secret=APPSECRET';
      $url = str_replace('APPID',$this->_appid ,$url);
      $url = str_replace('APPSECRET',$this->_secret ,$url);
      $rs = $this->get_data($url);
      if(isset($rs['access_token'])){
        $access_token = $rs['access_token'];
        $this->getTicket($access_token);
        $this->_redis->set($this->_wechatinfo['token_time'], time());
        $this->_redis->set($this->_wechatinfo['access_token'], $access_token);
        $this->_redis->setTimeout($this->_wechatinfo['token_time'], 5000);
        $this->_redis->setTimeout($this->_wechatinfo['access_token'], 5000);
        return $rs['access_token'];
      }else{
        return false;
      }
    }
    return $access_token;
  }

//Custom type
public function textCustomMsg($data){
  if(!$this->checkData(array("touser", "content"), $data))
    return false;
  $msg = array(
  "touser" => $data["touser"],
  "msgtype" => "text",
  "text" => array(
    "content" => $data["content"],
  )
  );
  return $msg;
}

public function imageCustomMsg($data){
  if(!$this->checkData(array("touser", "media_id"), $data))
    return false;
  $msg = array(
  "touser" => $data["touser"],
  "msgtype" => "image",
  "image" => array(
    "media_id" => $data["media_id"],
  )
  );
  return $msg;
}

public function voiceCustomMsg($data){
  if(!$this->checkData(array("touser", "media_id"), $data))
    return false;
  $msg = array(
  "touser" => $data["touser"],
  "msgtype" => "voice",
  "voice" => array(
    "media_id" => $data["media_id"],
  )
  );
  return $msg;
}

public function videoCustomMsg($data){
  if(!$this->checkData(array("touser", "media_id"), $data))
    return false;
  $msg = array(
  "touser" => $data["touser"],
  "msgtype" => "video",
  "video" => array(
    "media_id" => $data["media_id"],
    "thumb_media_id" => $data["thumb_media_id"],
    "title" => $data["title"],
    "description" => $data["description"]
  )
  );
  return $msg;
}

public function musicCustomMsg($data){
  if(!$this->checkData(array("touser", "title", "description", "musicurl", "hqmusicurl", "thumb_media_id"), $data))
    return false;
  $msg = array(
  "touser" => $data["touser"],
  "msgtype" => "music",
  "music" => array(
    "title" => $data["title"],
    "description" => $data["description"],
    "musicurl" => $data["musicurl"],
    "hqmusicurl" => $data["hqmusicurl"],
    "thumb_media_id" => $data["thumb_media_id"]
  )
  );
  return $msg;
}

public function newsCustomMsg($data){
  if(!$this->checkData(array("touser", "articles"), $data) && count($data["articles"]) > 8)
    return false;
  $msg = array(
  "touser" => $data["touser"],
  "msgtype" => "news",
  "news" => array(
    "articles" => array(),
  )
  );
  foreach($data["articles"] as $x){
    if(!$this->checkData(array("title", "description", "url", "picurl"), $x))
      return false;
    $article = array(
      "title" => $x["title"],
      "description" => $x["description"],
      "url" => $x["url"],
      "picurl" => $x["picurl"]
    );
    array_push($msg["news"]["articles"], $article);
    unset($article);
    unset($x);
  }
  return $msg;
}

public function mpnewsCustomMsg($data){
  if(!$this->checkData(array("touser", "media_id"), $data))
    return false;
  $msg = array(
  "touser" => $data["touser"],
  "msgtype" => "mpnews",
  "mpnews" => array(
    "media_id" => $data["media_id"],
  )
  );
  return $msg;
}

public function wxcardCustomMsg($data){
  if(!$this->checkData(array("touser", "card_id", "card_ext"), $data))
    return false;
  $msg = array(
  "touser" => $data["touser"],
  "msgtype" => "wxcard",
  "wxcard" => array(
    "card_id" => $data["card_id"],
    "card_ext" => $data["card_ext"],
  )
  );
  return $msg;
}
//Custom type end
//check list
  public function ststus(){
    if($this->_redis->lSize($this->prostr.$this->list) > 0){
      return true;
    }else{
      return false;
    }
  }

  public function pushMsg(){
    $this->_redis->set($this->prostr.$this->changT, time());
    $key = $this->_redis->lPop($this->prostr.$this->list);
    $this->custom_msend($key);
    $this->_redis->delete($this->prostr.$this->changT);
  }

  //check list

  // sub function
  public function checkData($stand, $data){
    $keys = array_keys($data);
    foreach($stand as $x){
      if(!in_array($x, $keys))
        return false;
    }
    return true;
  }

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
    $json = json_encode ( $array );
    return urldecode ( $json );
  }

}

?>
