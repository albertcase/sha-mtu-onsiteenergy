<?php
namespace Wechat\ApiBundle\Forms;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\Session\Session;

class storeslist extends FormRequest{

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
    $stores = $this->container->get('my.dataSql')->searchData(array() ,array('id', 'storename', 'phone', 'brandtype', 'storelog'), 'stores');
    if($stores && isset($stores['0'])){
      return array('code' => '10', 'msg' => 'get success' ,'list' => $stores);
    }
    return array('code' => '10', 'msg' => 'there not any data');
  }

}
