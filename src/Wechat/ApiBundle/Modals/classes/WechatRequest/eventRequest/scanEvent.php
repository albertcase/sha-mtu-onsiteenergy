<?php

namespace Wechat\ApiBundle\Modals\classes\WechatRequest\eventRequest;

use Wechat\ApiBundle\Modals\Database\dataSql;
use Wechat\ApiBundle\Modals\classes\WechatMsg;
use Wechat\ApiBundle\Modals\classes\WechatRequest\RequestFormat;

class scanEvent extends RequestFormat{

  public function RequestFeedback(){
    $fbck = '';
    if(isset($this->postObj->Ticket)){//truck qrcode
      $rs = $this->dataSql->qrcodeScanTimes($this->postObj->Ticket);
      $fbck = $this->msgResponse($rs);
    }
    if($fbck)
      return $fbck;
    return $this->feedbackdefault;
  }

}
