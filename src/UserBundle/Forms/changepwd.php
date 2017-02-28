<?php
namespace UserBundle\Forms;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\Session\Session;
use Wechat\ApiBundle\Forms\FormRequest;

class changepwd extends FormRequest{

  public function rule(){
    return array(
      'oldpassword' => new Assert\NotBlank(),
      'newpassword' => new Assert\NotBlank(),
    );
  }

  public function FormName(){
    return 'changepwd';
  }

  public function DoData(){
    if($this->Confirm()){
      return array('code' => '11' ,'msg' => 'your input error');
    }
    return $this->dealData();
  }

  public function dealData(){
    $dataSql = $this->container->get('my.dataSql');
    $Session = new Session();
    $user = $Session->get($this->container->getParameter('session_login'));
    $data = array('username' => $user, 'password' => $this->getdata['oldpassword']);
    $change = array('password' => $this->getdata['newpassword']);
    if($dataSql->changepassword($data, $change)){
      return array('code' => '10', 'msg' => 'change success');
    }
    return array('code' => '9', 'msg' => 'oldpassword error');
  }

}
