<?php

namespace Wechat\ApiBundle\Modals\classes\WechatRequest\eventRequest;

use Wechat\ApiBundle\Modals\Database\dataSql;
use Wechat\ApiBundle\Modals\classes\WechatMsg;
use Wechat\ApiBundle\Modals\classes\WechatRequest\RequestFormat;

class clickEvent extends RequestFormat{

  public function RequestFeedback(){
    $eventKey = isset($this->postObj->EventKey)?$this->postObj->EventKey:'';
    if(!$eventKey)
      return "";
    $rs = $this->dataSql->clickField($eventKey);
    $fbck = '';
    if(is_array($rs) && count($rs)> 0 )
      $fbck = $this->msgResponse($rs);
    if($fbck)
      return $fbck;
    return $this->feedbackdefault;
  }

}
