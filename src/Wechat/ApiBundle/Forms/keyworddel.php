<?php
namespace Wechat\ApiBundle\Forms;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\Session\Session;

class keyworddel extends FormRequest{

  public function rule(){
    return array(
      'menuId' => new Assert\NotBlank(),
    );
  }

  public function FormName(){
    return 'keyworddel';
  }

  public function DoData(){
    if($this->Confirm()){
      return array('code' => '11' ,'msg' => 'your input error');
    }
    return $this->dealData();
  }

  public function dealData(){
    $dataSql = $this->container->get('my.dataSql');
    if($dataSql->delTag($this->getdata['menuId'])){
      return array('code' => '10', 'msg' => 'delete success');
    }
    return array('code' => '8', 'msg' => 'delete errors');
  }
}
