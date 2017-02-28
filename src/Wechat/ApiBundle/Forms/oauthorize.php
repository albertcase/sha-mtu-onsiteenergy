<?php

namespace Wechat\ApiBundle\Forms;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Filesystem\Filesystem;

class oauthorize extends FormRequest{
  public function rule(){
    return array(
      'state' => array(
        'exists' => new Assert\Length(array("min" => 0)),
      ),
      'scope' => array(
        'exists' => new Assert\Choice(array('snsapi_userinfo','snsapi_base')),
      ),
      'code' => array(
        'exists' => new Assert\NotBlank(),
      ),
      'redirect_uri' =>  new Assert\Url(),
    );
  }

  public function beforeDeal(){
    if(isset($this->getdata['redirect_uri']))
      $this->getdata['redirect_uri'] = urldecode($this->getdata['redirect_uri']);
  }

  public function FormName(){
    return 'GET';
  }

  public function DoData(){
    if($error = $this->Confirm()){
      return array('code' => '11' ,'msg' => $error);
    }
    return $this->dealData();
  }

  public function dealData(){
    if(!isset($this->getdata['scope']))
      $this->getdata['scope'] = 'snsapi_base';
    if(isset($this->getdata['code'])){
      $oauth = $this->container->get('my.Wechat')->getoauth2token($this->getdata['code']);
      if(!$oauth || !isset($oauth['access_token']))
        return array('code' => '12', 'msg' => $oauth);
      $openid = isset($oauth['openid'])?$oauth['openid']:'';
      $rct_rul = explode("?", $this->getdata['redirect_uri']);
      $redirecthost = isset($rct_rul[0])?$rct_rul[0]:'';
      $redirectpram = isset($rct_rul[1])?("&" . $rct_rul[1]) : '';
      return array(
        'code' => '10',
        'msg' => 'get information successed',
        'redirect' => $redirecthost . "?openid=" . $openid . "&access_token=" . $oauth['access_token'] . $redirectpram,
      );
    }
    $domaincheck = $this->redirectDomain();
    if($domaincheck['code'] == '10'){
      $goto = $this->Request->getSchemeAndHttpHost() . $this->Request->getBaseUrl() . $this->Request->getPathInfo() . '?redirect_uri=' . urlencode($this->getdata['redirect_uri']);
      return array(
        'code' => '10',
        'msg' => 'goto oauth',
        'redirect' => $this->container->get('my.Wechat')->getoauth2url(urlencode($goto), $this->getdata['scope'])
      );
    }
    return $domaincheck;
  }

  public function redirectDomain(){
    preg_match_all("/^http[s]{0,1}:\/\/([^\/]*)\/.*/", $this->getdata['redirect_uri'], $newpath, PREG_SET_ORDER);
    $domain = isset($newpath['0']['1'])?$newpath['0']['1']:'';
    $redis = $this->container->get('my.RedisLogic');
    if($redis->checkSet('wechatoauthorize', $domain))
      return array('code' => '10', 'msg' => 'exists');
    if($this->container->get('my.dataSql')->searchData(array('redirect_url' => $domain), array('id'), 'wechat_oauth')){
      $redis->setSet('wechatoauthorize', $domain);
      return array('code' => '10', 'msg' => 'exists');
    }
    return array('code' => '9', 'msg' => 'this domin not be allow');
  }

}
