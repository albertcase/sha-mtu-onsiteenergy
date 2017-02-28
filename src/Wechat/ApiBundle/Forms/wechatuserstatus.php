<?php

namespace Wechat\ApiBundle\Forms;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Filesystem\Filesystem;

class wechatuserstatus extends FormRequest{
  public function rule(){
    return array(
      'openid' => new Assert\NotBlank(),
    );
  }

  public function FormName(){
    return 'GET';
  }

  public function DoData(){
    if($this->Confirm()){
      return array('code' => '11' ,'msg' => 'your input error');
    }
    return $this->dealData();
  }

  public function dealData(){
    $info = $this->container->get('my.dataSql')->searchData(array('openid' => $this->getdata['openid']) ,array('status'), 'wechat_users');
    if($info && isset($info['0'])){
      if($info['0']['status'] == '2'){
        return array('code' => '9' ,'msg' => 'this openid already unsubscribed');
      }else{
        return array('code' => '10' ,'msg' => 'this openid already subscribed');
      }
    }
    return array('code' => '8' ,'msg' => 'this openid not subscribed');
  }
}
