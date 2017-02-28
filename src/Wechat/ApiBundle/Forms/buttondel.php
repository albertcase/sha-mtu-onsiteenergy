<?php
namespace Wechat\ApiBundle\Forms;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\Session\Session;

class buttondel extends FormRequest{

  public function rule(){
    return array(
      'id' => new Assert\Range(array('min' => 0)),
    );
  }

  public function FormName(){
    return 'buttondel';
  }

  public function DoData(){
    if($this->Confirm()){
      return array('code' => '11' ,'msg' => 'your input error');
    }
    return $this->dealData();
  }

  public function dealData(){
    $dataSql = $this->container->get('my.dataSql');
    if($dataSql->deleteButton($this->getdata['id'])){
      return array('code' => '10', 'msg' => 'delete button success');
    }
    return array('code' => '9', 'msg' => 'delete button error');
  }

}
