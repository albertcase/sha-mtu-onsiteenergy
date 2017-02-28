<?php
namespace Wechat\ApiBundle\Forms;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\Session\Session;

class groupnewssend extends FormRequest{
  public function rule(){
    return array(
      'grouptagid' => new Assert\NotBlank(),
      'mediaid' => new Assert\NotBlank(),
      'groupname' => new Assert\NotBlank(),
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
    $redis = $this->container->get('my.RedisLogic');
    $lis = $this->container->getParameter('wechat_temp_listener');
    $tempid = uniqid().uniqid();
    $data = array(
      'groupname' => $this->getdata['groupname'],
      'grouptagid' => $this->getdata['grouptagid'],
      'mediaid' => $this->getdata['mediaid'],
      'tempid' => $tempid,
      'eventname' => 'sendpreviewnews',
    );
    $redis->setString($lis, json_encode($data, JSON_UNESCAPED_UNICODE), 120);
    return array('code' => '10' ,'msg' => 'success', 'tempid' => $tempid);
  }
}
