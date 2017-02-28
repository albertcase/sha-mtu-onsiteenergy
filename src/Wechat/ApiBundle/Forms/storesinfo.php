<?php
namespace Wechat\ApiBundle\Forms;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\Session\Session;

class storesinfo extends FormRequest{

  public function rule(){
    return array(
      'id' => new Assert\Range(array('min' => 0)),
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
    $stores = $this->container->get('my.dataSql')->searchData(array('id' => $this->getdata['id']) ,array(), 'stores');
    if($stores && isset($stores['0'])){
      return array('code' => '10', 'msg' => 'get success', 'info' => $stores['0']);
    }
    return array('code' => '9', 'msg' => 'get error');
  }

}
