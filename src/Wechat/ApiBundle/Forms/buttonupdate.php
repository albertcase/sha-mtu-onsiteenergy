<?php
namespace Wechat\ApiBundle\Forms;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\Session\Session;

class buttonupdate extends FormRequest{

  public function rule(){
    return array(
      // 'id' => new Assert\NotBlank(),
      // 'menuName' => new Assert\NotBlank(),
      // 'eventtype' => new Assert\NotBlank(),
      // 'eventKey' => '',
      // 'eventUrl' => '',
      // 'MsgType' =>  new Assert\NotBlank(),
      // 'Content' => '',
      // 'newslist' => '',
    );
  }

  public function FormName(){
    return 'buttonupdate';
  }

  public function DoData(){
    if($this->Confirm()){
      return array('code' => '11' ,'msg' => 'your input error');
    }
    return $this->dealData();
  }

  public function dealData(){
    $dataSql = $this->container->get('my.dataSql');
    $button = $this->getbutton();
    $event = $this->getevents($this->getdata['id']);
    if($dataSql->updateButton($this->getdata['id'], $button, $event)){
      return array('code' => '10', 'msg' => 'update button success');
    }
    return array('code' => '9', 'msg' => 'update button error');
  }

  public function getbutton(){
    $button = array();
    foreach($this->getdata as $x => $x_val){
      if($x_val){
        if($x == 'menuName' || $x == 'eventtype' || $x == 'eventKey' || $x == 'eventUrl' || $x == 'eventmedia_id')
          $button[$x] = $x_val;
      }
    }
    return $button;
  }

  public function getevents($id){
    $events = array();
    if(!isset($this->getdata['MsgType']))
      return $events;
    if($this->getdata['MsgType'] == 'text'){
      $MsgData = array(
        'Content' => $this->getdata['Content'],
      );
      $events['feedbacks'] = array(
        'menuId' => $id,
        'MsgType' => 'text',
        'MsgData' => json_encode($MsgData, JSON_UNESCAPED_UNICODE),
      );
      $events['getevent'] = array(
        'menuId' => $id,
        'getMsgType' => 'event',
        'getEvent' => 'click',
        'getEventKey' => $this->getdata['eventKey'],
        'MsgType' => 'text',
      );
      return $events;
    }
    if($this->getdata['MsgType'] == 'news'){
      $MsgData = array(
        'Articles' => json_decode($this->getdata['newslist'] ,true),
      );
      $events['feedbacks'] = array(
        'menuId' => $id,
        'MsgType' => 'news',
        'MsgData' => json_encode($MsgData, JSON_UNESCAPED_UNICODE),
      );
      $events['getevent'] = array(
        'menuId' => $id,
        'getMsgType' => 'event',
        'getEvent' => 'click',
        'getEventKey' => $this->getdata['eventKey'],
        'MsgType' => 'news',
      );
      return $events;
    }
    return $events;
  }

}
