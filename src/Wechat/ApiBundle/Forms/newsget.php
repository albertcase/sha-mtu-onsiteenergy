<?php

namespace Wechat\ApiBundle\Forms;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Filesystem\Filesystem;

class newsget extends FormRequest{
  public function rule(){
    return array(
      'timestamp' => array(
        'exists' => new Assert\Range(array('min' => 0)),
        ),
      'order' => array(
        'exists' => new Assert\Choice(array(
            'choices' => array('top', 'bottom'),
          )
        )
        )
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
    $where = '';
    if(isset($this->getdata['order']) && isset($this->getdata['timestamp'])){
      $this->getdata['timestamp'] = intval($this->getdata['timestamp']);
      $where = "WHERE `update_time`".(($this->getdata['order'] == 'bottom')?"<":">").$this->getdata['timestamp'];
      if($this->getdata['order'] == 'top')
        $this->snycMaterial();
    }
    $sql = "SELECT A.id,A.media_id,A.update_time,B.title,B.digest,B.url,B.thumb_url FROM wechat_material as A LEFT JOIN wechat_material_news as B on A.media_id=B.media_id WHERE A.id in (SELECT t.id FROM (SELECT `id`,`update_time` FROM wechat_material {$where} ORDER BY update_time DESC LIMIT 18) as t) ORDER BY A.update_time DESC";
    $list = $this->container->get('my.dataSql')->querysql($sql);
    if($list && isset($list['0'])){
      return array('code' => '10' ,'msg' => 'get success', 'list' => $this->dealList($list));
    }
    return array('code' => '10' ,'msg' => 'there are not any data', 'list' => array());
  }

  public function dealList($list){
    $result = array();
    foreach($list as $x){
      if(!isset($result[$x['media_id']]))
        $result[$x['media_id']] = array();
      $result[$x['media_id']][] = $x;
    }
    return $result;
  }

  public function snycMaterial(){
    $search = array(
      'type' => 'news',
      'offset' => '0',
      'count' => '18',
    );
    $check = $this->container->get('my.Wechat')->getMateriallist($search);
    if(!isset($check['errcode'])){
      $data = isset($check['item'])?$check['item']:array();
      $this->container->get('my.dataSql')->syncMaterial($data);
    }
  }
}
