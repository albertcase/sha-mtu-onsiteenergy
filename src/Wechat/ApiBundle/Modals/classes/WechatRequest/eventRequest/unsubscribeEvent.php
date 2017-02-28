<?php

namespace Wechat\ApiBundle\Modals\classes\WechatRequest\eventRequest;

use Wechat\ApiBundle\Modals\Database\dataSql;
use Wechat\ApiBundle\Modals\classes\WechatMsg;
use Wechat\ApiBundle\Modals\classes\WechatRequest\RequestFormat;

class unsubscribeEvent extends RequestFormat{

  public function RequestFeedback(){
    $this->dataSql->userUnsubscript($this->fromUsername);
    return "";
  }
}
