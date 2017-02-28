<?php

namespace Wechat\ApiBundle\Modals\Database;

class LdataSql{
  private $_db;
  private $_container;

  public function __construct($container){
    $this->_db = $container->get('vendor.LMysqliDb');
    $this->_container = $container;
  }

  public function rebuilddb(){
    return clone $this->_db;
  }

  public function insertData($data, $table){
    $db = $this->rebuilddb();
    return $db->insert($table, $data);
  }

  public function insertsData($datas, $table){
    foreach($datas as $x){
      $this->insertData($x, $table);
    }
  }

  public function searchData(array $data=array() ,array $dataout=array(), $table, $limit = null){
    $db = $this->rebuilddb();
    foreach($data as $x => $x_val){
      $db->where($x,$x_val);
    }
    return $db->get($table, $limit ,$dataout);
  }

  public function updateData($data, $change, $table){
    $db = $this->rebuilddb();
    foreach($data as $x => $x_val){
      $db->where($x,$x_val);
    }
    if($db->update($table, $change))
      return true;
    return false;
  }

  public function deleteData($data, $table){
    $db = $this->rebuilddb();
    foreach($data as $x => $x_val){
      $db->where($x,$x_val);
    }
    if($db->delete($table))
      return true;
    return false;
  }

  public function getCount($data, $table){
    $db = $this->rebuilddb();
    foreach($data as $x => $x_val){
      $db->where($x,$x_val);
    }
    $stats = $db->getOne ($table, "count(*) as cnt");
    return $stats['cnt'];
  }

  public function querysql($sql){
    $db = $this->rebuilddb();
    return $db->rawQuery ($sql);
  }

  public function querysqlp($sql, $param){
    $db = $this->rebuilddb();
    return $db->rawQuery ($sql,$param);
  }
}
