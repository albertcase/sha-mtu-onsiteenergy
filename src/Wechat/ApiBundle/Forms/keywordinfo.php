<?php
namespace Wechat\ApiBundle\Forms;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\Session\Session;

class keywordinfo extends FormRequest{

  public function rule(){
    return array(
      'menuId' => new Assert\NotBlank(),
    );
  }

  public function FormName(){
    return 'keywordinfo';
  }

  public function DoData(){
    if($this->Confirm()){
      return array('code' => '11' ,'msg' => 'your input error');
    }
    return $this->dealData();
  }

  public function dealData(){
    $dataSql = $this->container->get('my.dataSql');
    if($info = $dataSql->getTagEvents($this->getdata['menuId'])){
      return array('code' => '10', 'info' => $info,'msg' => 'get success');
    }
    return array('code' => '9', 'msg' => 'get errors');
  }
}
