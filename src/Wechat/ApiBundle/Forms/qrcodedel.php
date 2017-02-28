<?php
namespace Wechat\ApiBundle\Forms;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\Session\Session;

class qrcodedel extends FormRequest{

  public function rule(){
    return array(
      'id' => new Assert\NotBlank(),
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
    $qrcodeinfo = $this->container->get('my.dataSql')->qrcodeinfo($this->getdata['id']);
    $info = array();
    if($qrcodeinfo && isset($qrcodeinfo['0'])){
      if(isset($qrcodeinfo['0']['feedbackid']) && $qrcodeinfo['0']['feedbackid']){
        $fb = $this->container->get('my.dataSql')->qrcodefeedback($qrcodeinfo['0']['feedbackid']);
        if($fb && isset($fb[0])){
          $this->container->get('my.dataSql')->deleteData(array('menuId' => $qrcodeinfo['0']['feedbackid']), 'wechat_feedbacks');
        }
      }
      $this->container->get('my.dataSql')->deleteData(array('id' => $this->getdata['id']), 'wechat_qrcode');
      return array('code' => '10', 'msg' => 'delete success');
    }
    return array('code' => '9', 'msg' => 'not exists');
  }
}
