<?php
namespace Wechat\ApiBundle\Forms;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\Session\Session;

class storesdel extends FormRequest{

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
    if($this->container->get('my.dataSql')->deleteData(array('id' => $this->getdata['id']), 'stores')){
      return array('code' => '10', 'msg' => 'delete success');
    }
    return array('code' => '9', 'msg' => 'delete error');
  }

}
