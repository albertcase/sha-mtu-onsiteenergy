<?php
namespace Wechat\ApiBundle\Forms;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\Session\Session;

class qrcodeadd extends FormRequest{

  public function rule(){
    return array(
      // 'qrName' => new Assert\NotBlank(),
      // 'qrSceneid' => new Assert\Range(array('min' => 1, 'max' => 100000)),
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
    $data = array(
      'action_name' => "QR_LIMIT_SCENE",
      'action_info' => array(
        'scene' => array('scene_id' => $this->getdata['qrSceneid'])
      )
    );
    $result = $this->container->get('my.Wechat')->qrcodeRegister($data);
// $result = array(
//   'ticket' => 'sadasdhagsdjagsudgasjdgasgdjasg'.uniqid(),
//   'url' => 'http://asdasdasdasd.com'
// );
    if($result && isset($result['ticket'])){
      $menuId = 'qrcode'.uniqid();
      $sqlin = array(
        'qrTicket' => $result['ticket'],
        'qrUrl' => $result['url'],
        'qrName' => $this->getdata['qrName'],
        'qrSceneid' => $this->getdata['qrSceneid'],
        'feedbackid' => $menuId,
      );
      if($this->container->get('my.dataSql')->qrcodeAdd($sqlin)){
        if($event = $this->getevents($menuId))
          $this->container->get('my.dataSql')->insertData($event, 'wechat_feedbacks');
        return array('code' => '10', 'msg' => 'add success');
      }
      return array('code' => '9', 'msg' => 'add error');
    }
    return array('code' => '9', 'msg' => 'add error');
  }

  public function getevents($id){
    $events = array();
    if(!isset($this->getdata['MsgType']))
      return $events;
    if($this->getdata['MsgType'] == 'text'){
      $MsgData = array(
        'Content' => $this->getdata['Content'],
      );
      $events = array(
        'menuId' => $id,
        'MsgType' => 'text',
        'MsgData' => json_encode($MsgData, JSON_UNESCAPED_UNICODE),
      );
      return $events;
    }
    if($this->getdata['MsgType'] == 'news'){
      $MsgData = array(
        'Articles' => json_decode($this->getdata['newslist'] ,true),
      );
      $events = array(
        'menuId' => $id,
        'MsgType' => 'news',
        'MsgData' => json_encode($MsgData, JSON_UNESCAPED_UNICODE),
      );
      return $events;
    }
    return $events;
  }
}
