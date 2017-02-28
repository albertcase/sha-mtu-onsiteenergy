<?php
namespace Wechat\ApiBundle\Forms;

use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\Session\Session;

class buttoninfo extends FormRequest{

  public function rule(){
    return array(
      'id' => new Assert\NotBlank(),
    );
  }

  public function FormName(){
    return 'buttoninfo';
  }

  public function DoData(){
    if($this->Confirm()){
      return array('code' => '11' ,'msg' => 'your input error');
    }
    return $this->dealData();
  }

  public function dealData(){
    $dataSql = $this->container->get('my.dataSql');
    if($info = $dataSql->buttoninfo($this->getdata['id'])){
      return array('code' => '10', 'info' => $info, 'msg' => 'get success');
    }
    return array('code' => '9', 'msg' => 'get error');
  }
}
