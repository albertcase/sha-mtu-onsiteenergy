<?php

namespace Wechat\ApiBundle\Forms;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Filesystem\Filesystem;

class oauthdel extends FormRequest{
  public function rule(){
    return array(
      'id' => new Assert\NotBlank(),
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
    $redis = $this->container->get('my.RedisLogic');
    $oauthfile = $this->container->get('my.dataSql')->oauthinfo($this->getdata['id']);
    if($oauthfile && isset($oauthfile['0']) && isset($oauthfile['0']['redirect_url']))
      $redis->delSet('wechatoauthorize' ,$oauthfile['0']['redirect_url']);
    $this->container->get('my.dataSql')->oauthdel(array('id' => intval($this->getdata['id'])));
    return array('code' => '10' ,'msg' => 'delete success');
  }
}
