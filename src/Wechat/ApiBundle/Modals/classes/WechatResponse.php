<?php

namespace Wechat\ApiBundle\Modals\classes;
use Wechat\ApiBundle\Modals\Database\dataSql;
use Wechat\ApiBundle\Modals\classes\WechatMsg;

class WechatResponse{

  private $postStr = null;
  private $postObj = null;
  private $fromUsername = null;
  private $toUsername = null;
  private $msgType = null;
  private $dataSql;
  private $container;

  public function __construct($container){
    $this->PostDateDeal();
    $this->container = $container;
    $this->dataSql = $container->get('my.dataSql');
  }

  public function PostDateDeal($postStr = ''){
    if($postStr){
      $this->postStr = $postStr;
    }else{
      $this->postStr = isset($GLOBALS["HTTP_RAW_POST_DATA"])?$GLOBALS["HTTP_RAW_POST_DATA"]:'';
    }
    $this->postObj = simplexml_load_string($this->postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
    $this->msgType = isset($this->postObj->MsgType)?strtolower($this->postObj->MsgType):'';
    $this->fromUsername = isset($this->postObj->FromUserName)?trim($this->postObj->FromUserName):'';
    $this->toUsername = isset($this->postObj->ToUserName)?trim($this->postObj->ToUserName):'';
  }

  public function valid($echoStr){
   if($this->checkSignature())
      return $echoStr;
  }

  private function checkSignature()
  {
    $signature = $_GET["signature"];
    $timestamp = $_GET["timestamp"];
    $nonce = $_GET["nonce"];
    $token = $this->container->getParameter('wechat_Token');
    $tmpArr = array($token, $timestamp, $nonce);
    sort($tmpArr, SORT_STRING);
    $tmpStr = implode( $tmpArr );
    $tmpStr = sha1( $tmpStr );

    if( $tmpStr == $signature ){
      return true;
    }else{
      return false;
    }
  }

// response function *******************************************************
  public function RequestFeedback(){
    if(!$this->postStr)
      return "";
    $this->systemLog();//log
    $backxml = "";
    if(is_file(dirname(__FILE__) . '/WechatRequest/'.$this->msgType . 'Request.php')){
      $className = '\\Wechat\\ApiBundle\\Modals\\classes\\WechatRequest\\' . $this->msgType.'Request';
      $newclass = new $className($this->container, $this->postObj, $this->postStr);
      $backxml = call_user_func_array(array($newclass, 'RequestFeedback'), array());
    }
    if($backxml === true)
      return $this->defaultfeedback();
    if($backxml)
      return $backxml;
    return "";
  }
// response function end *****************************************************

  public function msgResponse($rs){
    if(!isset($rs[0]['MsgType']))
      return false;
    $WechatMsg = new WechatMsg($this->fromUsername, $this->toUsername);
    return $WechatMsg->sendMsgxml($rs);
  }

//event function end
  public function systemLog(){
    $this->dataSql->systemLog($this->postStr, $this->fromUsername, $this->msgType);
  }

  public function defaultfeedback(){
    $rs = $this->dataSql->defaultField();
    if(is_array($rs) && count($rs)> 0 ){
      return $this->msgResponse($rs);
    }
    return "";
  }

}
