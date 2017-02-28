<?php

namespace Wechat\ApiBundle\Modals\classes\WechatRequest;

use Wechat\ApiBundle\Modals\Database\dataSql;
use Wechat\ApiBundle\Modals\classes\WechatMsg;

abstract class RequestFormat{

  protected $postStr = null;
  protected $postObj = null;
  protected $fromUsername = null;
  protected $toUsername = null;
  protected $msgType = null;
  protected $dataSql;
  protected $container;
  protected $feedbackdefault = false;

  public function __construct($container, $postObj, $postStr){
    $this->container = $container;
    $this->postObj = $postObj;
    $this->postStr = $postStr;
    $this->msgType = isset($this->postObj->MsgType)?strtolower($this->postObj->MsgType):'';
    $this->fromUsername = isset($this->postObj->FromUserName)?trim($this->postObj->FromUserName):'';
    $this->toUsername = isset($this->postObj->ToUserName)?trim($this->postObj->ToUserName):'';
    $this->dataSql = $container->get('my.dataSql');
  }

  abstract public function RequestFeedback();

  public function msgResponse($rs){
    if(!isset($rs[0]['MsgType']))
      return false;
    $WechatMsg = new WechatMsg($this->fromUsername, $this->toUsername);
    return $WechatMsg->sendMsgxml($rs);
  }

  protected function sendMsgForText($fromUsername, $toUsername, $time, $msgType, $contentStr)
  {
    $textTpl = "<xml>
          <ToUserName><![CDATA[%s]]></ToUserName>
          <FromUserName><![CDATA[%s]]></FromUserName>
          <CreateTime>%s</CreateTime>
          <MsgType><![CDATA[%s]]></MsgType>
          <Content><![CDATA[%s]]></Content>
          <FuncFlag>0</FuncFlag>
          </xml>";
    return sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
  }

  public function defaultfeedback(){
    $rs = $this->dataSql->defaultField();
    if(is_array($rs) && count($rs)> 0 ){
      return $this->msgResponse($rs);
    }
    return "";
  }

  public function systemLog(){
    $this->dataSql->systemLog($this->postStr, $this->fromUsername, $this->msgType);
  }

}
