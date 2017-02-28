<?php

namespace Wechat\ApiBundle\Forms;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Filesystem\Filesystem;

class oauthupdate extends FormRequest{
  public function rule(){
    return array(
      'id' => new Assert\NotBlank(),
      'redirect_url' => new Assert\NotBlank(),
      'name' => new Assert\NotBlank(),
    );
  }

  public function FormName(){
    return 'POST';
  }

  public function DoData(){
    if($this->Confirm()){
      return array('code' => '11' ,'msg' => 'your input error');
    }
    return $this->dealData();
  }

  public function dealData(){
    $oauthfile = $this->container->get('my.dataSql')->oauthinfo($this->getdata['id']);
    if(!$oauthfile || !isset($oauthfile['0']))
      return array('code' => '9' ,'msg' => 'this oauth not exists');
    $datain = array(
      'name' => $this->getdata['name'],
      'redirect_url' => $this->getdata['redirect_url'],
      'oauthfile' => $oauthfile['0']['oauthfile'],
    );
    $redis = $this->container->get('my.RedisLogic');
    $redis->delSet('wechatoauthorize', $oauthfile['0']['redirect_url']);
    $this->container->get('my.dataSql')->oauthupdate(intval($this->getdata['id']), $datain);
    return array('code' => '10' ,'msg' => 'update success');
  }
}
