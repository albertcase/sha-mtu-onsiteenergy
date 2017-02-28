<?php

namespace Wechat\ApiBundle\Forms;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Filesystem\Filesystem;

class oauthinfo extends FormRequest{
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
    $oauthfile = $this->container->get('my.dataSql')->oauthinfo($this->getdata['id']);
    if(!$oauthfile || !isset($oauthfile['0']))
      return array('code' => '9' ,'msg' => 'this oauth not exists', 'info' => array());
    return array('code' => '10' ,'msg' => 'get success', 'info' => $oauthfile['0']);
  }
}
