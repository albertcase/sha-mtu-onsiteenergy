<?php

namespace Wechat\ApiBundle\Modals\classes\WechatRequest\eventRequest;

use Wechat\ApiBundle\Modals\Database\dataSql;
use Wechat\ApiBundle\Modals\classes\WechatMsg;
use Wechat\ApiBundle\Modals\classes\WechatRequest\RequestFormat;

class subscribeEvent extends RequestFormat{

  public function RequestFeedback(){
    $this->dataSql->userSubscript($this->fromUsername);
    if(isset($this->postObj->Ticket)){//truck qrcode
      $rs = $this->dataSql->qrcodeSubscribeTimes($this->postObj->Ticket);
      return $this->msgResponse($rs);
    }
    $rs = $this->dataSql->subscribeField();
    if(is_array($rs) && count($rs)> 0 ){
      return $this->msgResponse($rs);
    }
    return "";
  }
}
