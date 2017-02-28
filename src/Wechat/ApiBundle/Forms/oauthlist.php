<?php

namespace Wechat\ApiBundle\Forms;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Filesystem\Filesystem;

class oauthlist extends FormRequest{
  public function rule(){
    return array();
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
    $list = $this->container->get('my.dataSql')->oauthlist();
    if($list && isset($list['0']))
      return array('code' => '10' ,'msg' => 'get success', 'list' => $list);
    return array('code' => '10' ,'msg' => 'there are not any data', 'list' => array());
  }
}
