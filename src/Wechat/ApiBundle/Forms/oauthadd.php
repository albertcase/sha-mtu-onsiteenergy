<?php

namespace Wechat\ApiBundle\Forms;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Filesystem\Filesystem;

class oauthadd extends FormRequest{
  public function rule(){
    return array(
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
    $datain = array(
      'name' => $this->getdata['name'],
      'redirect_url' => $this->getdata['redirect_url'],
      'oauthfile' => uniqid(),
    );
    $this->container->get('my.dataSql')->addoauth($datain);
    return array('code' => '10' ,'msg' => 'build success');
  }
}
