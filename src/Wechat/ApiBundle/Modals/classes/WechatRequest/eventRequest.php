<?php

namespace Wechat\ApiBundle\Modals\classes\WechatRequest;

use Wechat\ApiBundle\Modals\Database\dataSql;
use Wechat\ApiBundle\Modals\classes\WechatMsg;

class eventRequest extends RequestFormat{

  public function RequestFeedback(){
    $event = isset($this->postObj->Event)?strtolower($this->postObj->Event):'';
    if(!$event)
      return "";
    $backxml = "";
    if(is_file(dirname(__FILE__) . '/eventRequest/' . $event . 'Event.php')){
      $className = '\\Wechat\\ApiBundle\\Modals\\classes\\WechatRequest\\eventRequest\\' . $event . 'Event';
      $newclass = new $className($this->container, $this->postObj, $this->postStr);
      $backxml = call_user_func_array(array($newclass, 'RequestFeedback'), array());
    }
    if($backxml === true)
      return $this->feedbackdefault;
    if($backxml)
      return $backxml;
    return "";
  }

}
