<?php
namespace UserBundle\Forms;

use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\Session\Session;
use Wechat\ApiBundle\Forms\FormRequest;

class admininfo extends FormRequest{

  public function rule(){
    return array(
      'id' => new Assert\NotBlank(),
    );
  }

  public function FormName(){
    return 'admininfo';
  }

  public function DoData(){
    if($this->Confirm()){
      return array('code' => '11' ,'msg' => 'your input error');
    }
    return $this->dealData();
  }

  public function dealData(){
    $dataSql = $this->container->get('my.dataSql');
    if($info = $dataSql->getAdmininfo($this->getdata['id'])){
      return array('code' => '10', 'msg' => 'get success', 'info' => $info['0']);
    }
    return array('code' => '9', 'msg' => 'get error');
  }

}
