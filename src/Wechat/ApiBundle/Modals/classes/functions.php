<?php

namespace Wechat\ApiBundle\Modals\classes;

use Wechat\ApiBundle\Modals\classes\WechatResponse;

class functions{

  private $_container;

  public function __construct($container){
    $this->_container = $container;
  }

  public function getmenus(){
    $menus = $this->DealMenus();
    $hierarchy = $this->DealMenuHierarchy();
    $hmenus = array();
    $tem = array();
    if(!isset($hierarchy['0']))
      return array();
    foreach($hierarchy['0'] as $x){
      $this->buildHierarchy($hmenus, $x ,$hierarchy, $menus);
    }
    $this->sortMenuslist($hmenus);
    // print_r($hmenus);
    return $hmenus;
  }

  public function sortMenuslist(&$hmenus){
    if(!is_array($hmenus))
      return false;
    $fun = function($a, $b){
      if(!isset($a['data']) || !isset($b['data']))
        return 0;
      return ($a['data']['width']>$b['data']['width'])?1:0;
    };
    usort($hmenus, $fun);
    foreach($hmenus as $x => $x_val){
      if(isset($x_val['son']) && count($x_val['son']))
        $this->sortMenuslist($hmenus[$x]['son']);
    }
  }

  public function buildHierarchy(&$hmenus, $head ,$hierarchy, $menus){
    $tem = array(
      'data' => isset($menus[$head])?$menus[$head]:array(),
      'son' => array(),
    );
    if(isset($hierarchy[$head])){
      foreach($hierarchy[$head] as $x)
        $this->buildHierarchy($tem['son'], $x, $hierarchy, $menus);
    }
    array_push($hmenus, $tem);
  }

  public function DealMenus(){
    $dataSql = $this->_container->get('my.dataSql');
    $menus = $dataSql->getmenusDb();
    $result = array();
    foreach($menus as $x){
      if(!isset($result[$x['id']]))
        $result[$x['id']] = $x;
    }
    return $result;
  }

  public function DealMenuHierarchy(){
    $dataSql = $this->_container->get('my.dataSql');
    $hierarchy = $dataSql->getMenuHierarchy();
    $result = array();
    foreach($hierarchy as $x){
      if(!isset($result[$x['parent']]))
        $result[$x['parent']] = array();
      array_push($result[$x['parent']], $x['tid']);
    }
    return $result;
  }

  public function getmmenu(){
    $main = array();
    $menus = $this->DealMenus();
    $hierarchy = $this->DealMenuHierarchy();
    if(!isset($hierarchy['0']))
      return array();
    foreach($hierarchy['0'] as $x){
      $main[$x] = $menus[$x]['menuName'];
    }
    return $main;
  }

  public function getOnlineImage($url){
    if(empty($url))
        return false;
    $dataSql = $this->_container->get('my.dataSql');
    $path = $dataSql->getLocalpath($url);
    if($path)
      return $path;
    $fs = new \Symfony\Component\Filesystem\Filesystem();
    $dir = date('Ym' ,strtotime("now"));
    if(!$fs->exists('upload/image/'.$dir)){
      $fs->mkdir('upload/image/'.$dir);
    }
    $image = file_get_contents($url);
    $path = 'upload/image/'.$dir.'/'.uniqid();
    $fs->dumpFile($path, $image);
    $path = '/'.$path;
    $dataSql->setLocalpath($url, $path);
    return $path;
  }

  public function vendorWechatUser($code = '', $url = '', $scope = 'snsapi_base'){
    if(!$code)
      return false;
    $userinfo = $this->_container->get('my.Wechat')->getoauthuserinfo($code);
    if(!$userinfo || isset($userinfo['errcode']))
      return false;
    if(isset($userinfo['openid']) && $userinfo['openid'] != ''){
      $post = array(
        'code' => 200,
        'msg' => 'success',
        'data' => $userinfo
      );
      $this->_container->get('my.Wechat')->post_data($url, $post);
      return $userinfo['openid'];
    }
    return false;
  }

  public function updataWechatUser($code = ''){
    if(!$code)
      return false;
    $userinfo = $this->_container->get('my.Wechat')->getoauthuserinfo($code);
    if(!$userinfo || isset($userinfo['errcode']))
      return false;
    unset($userinfo['privilege'],$userinfo['unionid'],$userinfo['language']);
    if(isset($userinfo['openid']) && $userinfo['openid'] != ''){
      $this->openidEncode($userinfo['openid']);
      $this->insertuserinfo($userinfo);
      return true;
    }
    return false;
  }

  public function insertuserinfo($userinfo){

  }

  public function openidEncode($openid){
    $code = base64_encode($this->aes128_cbc_encrypt($openid.session_id()));
    setCookie('woauths', $code, (time() + 1200), '/');//save user
    return $code;
  }

  public function openidDecode($incode){
    $code = $this->aes128_cbc_decrypt(base64_decode($incode));
    if(strpos($code, session_id()) === false)
      return false;
    setCookie('woauths', $incode, (time() + 1200), '/');//save user
    return str_replace(session_id(), "", $code);
  }

  public function aes128_cbc_encrypt($data, $key = 'aes128cbc', $iv = 'SAMEwECHAT') {
    if(16 !== strlen($key)) $key = hash('MD5', $key, true);
    if(16 !== strlen($iv)) $iv = hash('MD5', $iv, true);
    $padding = 16 - (strlen($data) % 16);
    $data .= str_repeat(chr($padding), $padding);
    return mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $data, MCRYPT_MODE_CBC, $iv);
  }

  public function aes128_cbc_decrypt($data, $key = 'aes128cbc', $iv = 'SAMEwECHAT') {
    if(16 !== strlen($key)) $key = hash('MD5', $key, true);
    if(16 !== strlen($iv)) $iv = hash('MD5', $iv, true);
    $data = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $data, MCRYPT_MODE_CBC, $iv);
    $padding = ord($data[strlen($data) - 1]);
    return substr($data, 0, -$padding);
  }

  public function allowjssdk($url){
    $allow = array(
      'keringwechat.samesamechina.com',
      'keringrecruitment.samesamechina.com'
    );
    foreach($allow as $x){
      if(preg_match("/^http:\/\/".$x."/i", $url))
      return true;
    }
    return false;
  }
}
