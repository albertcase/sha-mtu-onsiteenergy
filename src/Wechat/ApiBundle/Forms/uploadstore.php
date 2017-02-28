<?php
namespace Wechat\ApiBundle\Forms;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\Session\Session;

class uploadstore extends FormRequest{

  public function rule(){
    return array(
      'storeexcel' => new Assert\File(),
    );
  }

  public function FormName(){
    return 'FILES';
  }

  public function DoData(){
    if($this->Confirm()){
      return array('code' => '11' ,'msg' => 'your input error');
    }
    return $this->dealData();
  }

  public function dealData(){
    $ext = $this->getdata['storeexcel']->getClientOriginalExtension();
    if($ext != "xlsx" && $ext != "xls")
      return array('code' => '7', 'msg' => 'this file is not a excel');
    $fs = new \Symfony\Component\Filesystem\Filesystem();
    if(!$fs->exists('upload/files/')){
      $fs->mkdir('upload/files/');
    }
    $dist = 'upload/files/storeexcel.'.$ext;
    $fs->rename($this->getdata['storeexcel'], $dist, true);
    return $this->saveData($dist);
  }

  public function saveData($dist){
    $myexcel = new \Wechat\ApiBundle\Modals\classes\MyPHPExcel();
    $excel = $myexcel->input(realpath($dist));
    $sheet = $excel['sheet'];
    $highestRow = $excel['highestRow'];
    $highestColumm = $excel['highestColumm'];
    $title = array();
    for ($row = 1; $row <= $highestRow; $row++){//行数是以第1行开始
      for ($column = 'A'; $column <= $highestColumm; $column++) {//列数是以A列开始
        $title[$column] = $this->translate(trim($sheet->getCell($column.$row)->getValue()));
      }
      if(!in_array('',$title)){//$title is the keys
        break;
      }
    }
    $row++;
    $out = array();
    $i = 0;
    for ($row ; $row <= $highestRow; $row++){
      for ($column = 'A'; $column <= $highestColumm; $column++) {
        $col = $title[$column];
        // if(in_array($col, array('cardno','firstname','secondname', 'bak', 'openid')))//control insert datas
          $out[$i][$col] = trim($sheet->getCell($column.$row)->getValue());
      }
      $i++;
    }
    $dataSql = $this->container->get('my.dataSql');
    $dataSql->querysql('TRUNCATE TABLE `stores`');
    $dataSql->insertsData($out, 'stores');
    return array('code' => '10', 'msg' => 'update success');
  }

  public function translate($in){
    $t = array(
      'Store name' => 'storename',
      'Address' => 'address',
      'Phone' => 'phone',
      'Open Hours' => 'openhours',
    );
    if(isset($t[$in]))
      return $t[$in];
    return $in;
  }

}
