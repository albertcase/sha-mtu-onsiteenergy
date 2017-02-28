<?php

namespace Wechat\ApiBundle\Forms;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Filesystem\Filesystem;

class materiallist extends FormRequest{
  public function rule(){
    return array();
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
    if(isset($this->getdata['order']) && isset($this->getdata['id'])){
      $this->getdata['id'] = intval($this->getdata['id']);
      $where = "WHERE id".(($this->getdata['order'] == 'bottom')?"<":">").$this->getdata['id'];
    }
    $sql = "SELECT `id`,`title`,`digest`,`url`,`thumb_url` FROM wechat_material_news {$where} ORDER BY `id` DESC LIMIT 5";
    $list = $this->container->get('my.dataSql')->querysql($sql);
    if($list && isset($list['0']))
      return array('code' => '10' ,'msg' => 'get success', 'list' => $list);
    return array('code' => '10' ,'msg' => 'there are not any data', 'list' => array());
  }
}
