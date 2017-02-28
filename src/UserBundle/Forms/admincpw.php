<?php
namespace UserBundle\Forms;

use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\Session\Session;
use Wechat\ApiBundle\Forms\FormRequest;

class admincpw extends FormRequest{

  public function rule(){
    return array(
      'id' => new Assert\NotBlank(),
      'newpassword' => new Assert\NotBlank(),
    );
  }

  public function FormName(){
    return 'admincpw';
  }

  public function DoData(){
    if($this->Confirm()){
      return array('code' => '11' ,'msg' => 'your input error');
    }
    return $this->dealData();
  }

  public function dealData(){
    $dataSql = $this->container->get('my.dataSql');
    $data = array(
      'id' => $this->getdata['id'],
    );
    $change = array(
      'password' => $this->getdata['newpassword'],
    );
    if($dataSql->admincpw($data,$change)){
      return array('code' => '10', 'msg' => 'change password success');
    }
    return array('code' => '9', 'msg' => 'change password error');
  }

}
