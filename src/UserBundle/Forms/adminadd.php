<?php
namespace UserBundle\Forms;

use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\Session\Session;
use Wechat\ApiBundle\Forms\FormRequest;

class adminadd extends FormRequest{

  public function rule(){
    return array(
      'username' => new Assert\NotBlank(),
      'password' => new Assert\NotBlank(),
    );
  }

  public function FormName(){
    return 'adminadd';
  }

  public function DoData(){
    if($this->Confirm()){
      return array('code' => '11' ,'msg' => 'your input error');
    }
    return $this->dealData();
  }

  public function dealData(){
    $dataSql = $this->container->get('my.dataSql');
    if($dataSql->getCount(array('username' => $this->getdata['username']), 'wechat_admin'))
      return array('code' => '8', 'msg' => 'this user already exists');
    if($dataSql->createwechatAdmin($this->getdata)){
      return array('code' => '10', 'msg' => 'new admin added success');
    }
    return array('code' => '9', 'msg' => 'new admin added error');
  }

}
