<?php
namespace Wechat\ApiBundle\Forms;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\Session\Session;

class keywordadd extends FormRequest{

  public function rule(){
    return array(
      // 'getMsgType' => new Assert\NotBlank(),
      // 'getContent' => new Assert\NotBlank(),
      // 'MsgType' =>  '',
      // 'Content' => '',
      // 'newslist' => '',
      // 'Tagname' => '',
      // 'keywords' => ''
    );
  }

  public function FormName(){
    return 'keywordadd';
  }

  public function DoData(){
    if($this->Confirm()){
      return array('code' => '11' ,'msg' => 'your input error');
    }
    return $this->dealData();
  }

  public function dealData(){
    $dataSql = $this->container->get('my.dataSql');
    if($dataSql->getCount(array('Tagname' => $this->getdata['Tagname']), 'wechat_keyword_tag')) //check tagname exists
      return array('code' => '8', 'msg' => 'this Tagname already exists');
    $keywords = $this->dealkeyword();
    if(!$keywords || !is_array($keywords))
      return array('code' => '6', 'msg' => 'less than include one keyword');
    foreach($keywords as $x){
      if($dataSql->getCount(array('getContent' => $x, 'getMsgType' => 'text'), 'wechat_events')) //check keywords exists
        return array('code' => '5', 'msg' => 'this keyword <<'.$x.'>> already exists in the other TAG');
    }
    $menuId = 'tag'.uniqid();
    if(!$dataSql->insertData(array('menuId' => $menuId,'Tagname' => $this->getdata['Tagname']), 'wechat_keyword_tag'))
      return array('code' => '7', 'msg' => 'Tag add errors');
    $event = $this->getevents($menuId, $keywords);
    if(count($event))
      $dataSql->addTagEvents($event);
    return array('code' => '10', 'msg' => 'add tag success');
  }

  public function getevents($id, $keywords){
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
      $events['getevent'] = array();
      foreach($keywords as $k){
        $events['getevent'][] = array(
          'menuId' => $id,
          'getMsgType' => 'text',
          'getContent' => $k,
          'MsgType' => 'text',
        );
      }
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
      $events['getevent'] = array();
      foreach($keywords as $k){
        $events['getevent'][] = array(
          'menuId' => $id,
          'getMsgType' => 'text',
          'getContent' => $k,
          'MsgType' => 'news',
        );
      }
      return $events;
    }
    return $events;
  }

  public function dealkeyword(){
    $out = array();
    $keywords = json_decode($this->getdata['keywords'], true);
    foreach($keywords as $x){
      $exp = explode("；", $x);
      $out = array_merge($exp, $out);
    }
    return array_unique($out);
  }


}
