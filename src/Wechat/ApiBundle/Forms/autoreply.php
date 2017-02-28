<?php
namespace Wechat\ApiBundle\Forms;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\Session\Session;

class autoreply extends FormRequest{

  public function rule(){
    return array(
      // 'menuName' => new Assert\NotBlank(),
      // 'eventtype' => '',
      // 'eventKey' => '',
      // 'MsgType' =>  '',
      // 'Content' => '',
      // 'getMsgType' => '',
      // 'newslist' => '',
    );
  }

  public function FormName(){
    return 'autoreply';
  }

  public function DoData(){
    if($this->Confirm()){
      return array('code' => '11' ,'msg' => 'your input error');
    }
    return $this->dealData();
  }

  public function dealData(){
    $dataSql = $this->container->get('my.dataSql');
    if($menuId = $dataSql->searchData(array('getMsgType' => $this->getdata['getMsgType'], 'getEvent' => $this->getdata['getEvent']), array('menuId'), 'wechat_events')){
      $menuId = $menuId[0]['menuId'];
      $dataSql->delEvent($menuId);
    }
    $menuId = 'auto'.uniqid();
    $event = $this->getevents($menuId);
    $dataSql->addEvent($event);
    return array('code' => '10', 'msg' => 'success');
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
        'getMsgType' => $this->getdata['getMsgType'],
        'getEvent' => isset($this->getdata['getEvent'])?$this->getdata['getEvent']:'',
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
        'getMsgType' => $this->getdata['getMsgType'],
        'getEvent' => isset($this->getdata['getEvent'])?$this->getdata['getEvent']:'',
        'MsgType' => 'news',
      );
      return $events;
    }
    return $events;
  }

}
