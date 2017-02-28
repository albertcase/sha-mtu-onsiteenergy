<?php
namespace Wechat\ApiBundle\Forms;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\Session\Session;

class qrcodeinfo extends FormRequest{

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
      $info['id'] = $qrcodeinfo['0']['id'];
      $info['qrName'] = $qrcodeinfo['0']['qrName'];
      if(isset($qrcodeinfo['0']['feedbackid']) && $qrcodeinfo['0']['feedbackid']){
        $fb = $this->container->get('my.dataSql')->qrcodefeedback($qrcodeinfo['0']['feedbackid']);
        if($fb && isset($fb[0])){
          $info['MsgType'] = $fb[0]['MsgType'];
          $MsgData = json_decode(($fb['0']['MsgData']?$fb['0']['MsgData']:array()), true);
          if($info['MsgType'] == 'news'){
            $info['newslist'] = $MsgData['Articles'];
          }
          if($info['MsgType'] == 'text'){
            $info['Content'] = $MsgData['Content'];
          }
        }
      }
      return array('code' => '10', 'msg' => 'get success', 'info' => $info);
    }
    return array('code' => '9', 'msg' => 'not exists');
  }
}
