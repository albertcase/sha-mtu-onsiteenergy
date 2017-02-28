<?php

namespace Wechat\ApiBundle\Modals\classes\WechatRequest;

use Wechat\ApiBundle\Modals\Database\dataSql;
use Wechat\ApiBundle\Modals\classes\WechatMsg;

class textRequest extends RequestFormat{

  protected $feedbackdefault = true;

  public function RequestFeedback(){
    $tempmsg = $this->doTempevent($this->postObj->Content);//temp listener
    if($tempmsg)
      return $tempmsg;
    $rs = $this->dataSql->textField($this->postObj->Content);
    $fbck = '';
    if(is_array($rs) && count($rs)> 0 )
      $fbck = $this->msgResponse($rs);
    if($fbck)
      return $fbck;
    return $this->feedbackdefault;
  }

  /**
   * goto temp event. 'tempid' is a default value.
   *
   * @var    $data = array(
   *    'groupname' => $temp['groupname'],
   *    'grouptagid' => $temp['grouptagid'],
   *    'mediaid' => $temp['mediaid'],
   *    'tempid' => $tempid,
   *    'eventname' => 'tagnewssend',
   *    'fromopenid' => $this->fromUsername,
   *  );
   */
  public function doTempevent($tempid){
    $redis = $this->container->get('my.RedisLogic');
    if($redis->checkString('wechattemplistener')){
      $temp = json_decode($redis->getString('wechattemplistener'), true);
      if($temp['tempid'] == $tempid){
        if(method_exists($this, $temp['eventname'].'Tempevent')){
          return call_user_func_array(array($this,  $temp['eventname'].'Tempevent'), array($temp));
        }
      }
    }
    return "";
  }

  // Temp Events
    public function sendpreviewnewsTempevent($temp){
      $redis = $this->container->get('my.RedisLogic');
      $tempid = mt_rand(0,9).mt_rand(0,9).mt_rand(0,9).mt_rand(0,9).mt_rand(0,9).mt_rand(0,9);
      $data = array(
        'groupname' => $temp['groupname'],
        'grouptagid' => $temp['grouptagid'],
        'mediaid' => $temp['mediaid'],
        'tempid' => $tempid,
        'eventname' => 'tagnewssend',
        'fromopenid' => $this->fromUsername,
      );
      $redis->setString('wechattemplistener', json_encode($data, JSON_UNESCAPED_UNICODE), 120);
      $wehcat = $this->container->get('my.Wechat');
      $prev = array(
        "touser" => $this->fromUsername,
        "mpnews" => array(
           "media_id" => $temp['mediaid'],
         ),
        "msgtype" => "mpnews",
      );
      $wehcat->sendTagPreviewMsg($prev);
      return $this->sendMsgForText(
        $this->fromUsername,
        $this->toUsername,
        time(),
        'text',
        "pls confirm this news preview \n if you make true \n pls within 80s feedback code <a href='#'>".$tempid."</a> \n to send this news to group [{$temp['groupname']}]"
      );
    }

    public function tagnewssendTempevent($temp){
      $redis = $this->container->get('my.RedisLogic');
      if($temp['fromopenid'] == $this->fromUsername){
        $wehcat = $this->container->get('my.Wechat');
        $data = array(
          "filter" => array(
            "is_to_all" => false,
            "tag_id" => $temp['grouptagid'],
          ),
          "mpnews" => array(
            "media_id" => $temp['mediaid']
          ),
          "msgtype" => "mpnews"
        );
        $result = $wehcat->sendTagMsg(json_encode($data, JSON_UNESCAPED_UNICODE));
        $dataSql = $this->container->get('my.dataSql');
        $dataSql->tempEventLog($this->fromUsername, $temp['tempid'],json_encode($data, JSON_UNESCAPED_UNICODE),json_encode($result));
        $redis->delkey('wechattemplistener');
        return $this->sendMsgForText(
          $this->fromUsername,
          $this->toUsername,
          time(),
          'text',
          "your send a news to group [{$temp['groupname']}]"
        );
      }
      return '';
    }
}
