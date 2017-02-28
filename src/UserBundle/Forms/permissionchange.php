<?php
namespace UserBundle\Forms;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\Session\Session;
use Wechat\ApiBundle\Forms\FormRequest;

class permissionchange extends FormRequest{
  public function rule(){
    return array(
      'uid' => new Assert\Range(array('min' => 2)),
      'premission' => new Assert\NotBlank(),
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
    $dataSql = $this->container->get('my.dataSql');
    isset($this->getdata['premission'])?"":($this->getdata['premission'] = "");
    $this->getdata['premission'] = json_decode($this->getdata['premission'], true);
    if(!$this->getdata['premission']){
      $this->getdata['premission'] = array();
    }
    $dataSql->setPermission($this->getdata['uid'], $this->getdata['premission']);
    $info = $dataSql->getUserInfoId($this->getdata['uid']);
    $this->container->get('my.RedisLogic')->setString("user:".$info['username'], json_encode($info, JSON_UNESCAPED_UNICODE));
    return array('code' => '10' ,'msg' => 'success');
  }
}
