<?php

namespace Wechat\ApiBundle\Modals\classes\WechatRequest;

use Wechat\ApiBundle\Modals\Database\dataSql;
use Wechat\ApiBundle\Modals\classes\WechatMsg;

class locationRequest extends RequestFormat{

  public function RequestFeedback(){
    $fbck = $this->feedbackStores($this->postObj->Location_X, $this->postObj->Location_Y);
    if($fbck)
      return $fbck;
    return $this->feedbackdefault;
  }

  protected function returnSquarePoint($lng, $lat,$distance = 0.5){
    $earthRadius = 6378138;
    $dlng =  2 * asin(sin($distance / (2 * $earthRadius)) / cos(deg2rad($lat)));
    $dlng = rad2deg($dlng);
    $dlat = $distance/$earthRadius;
    $dlat = rad2deg($dlat);
    return array(
                  'left-top'=>array('lat'=>$lat + $dlat,'lng'=>$lng-$dlng),
                  'right-top'=>array('lat'=>$lat + $dlat, 'lng'=>$lng + $dlng),
                  'left-bottom'=>array('lat'=>$lat - $dlat, 'lng'=>$lng - $dlng),
                  'right-bottom'=>array('lat'=>$lat - $dlat, 'lng'=>$lng + $dlng)
    );
  }

  protected function getDistance($lat1, $lng1, $lat2, $lng2){
    $earthRadius = 6378138; //近似地球半径米
    // 转换为弧度
    $lat1 = ($lat1 * pi()) / 180;
    $lng1 = ($lng1 * pi()) / 180;
    $lat2 = ($lat2 * pi()) / 180;
    $lng2 = ($lng2 * pi()) / 180;
    // 使用半正矢公式  用尺规来计算
    $calcLongitude = $lng2 - $lng1;
    $calcLatitude = $lat2 - $lat1;
    $stepOne = pow(sin($calcLatitude / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($calcLongitude / 2), 2);
    $stepTwo = 2 * asin(min(1, sqrt($stepOne)));
    $calculatedDistance = $earthRadius * $stepTwo;
    return round($calculatedDistance);
  }

  public function feedbackStores($l_x, $l_y){
    $baidu = file_get_contents("http://api.map.baidu.com/geoconv/v1/?coords={$l_x},{$l_y}&from=3&to=5&ak=Z5FOXZbjH3AEIukiiRTtD7Xy");
    $baidu = json_decode($baidu, true);
    $lat = $baidu['result'][0]['x'];
    $lng = $baidu['result'][0]['y'];
    $squares = $this->returnSquarePoint($lng,$lat,100000);
    $latbig = $squares['right-bottom']['lat'] > $squares['left-top']['lat'] ? $squares['right-bottom']['lat'] : $squares['left-top']['lat'];
    $latsmall = $squares['right-bottom']['lat'] > $squares['left-top']['lat'] ? $squares['left-top']['lat'] : $squares['right-bottom']['lat'];
    $lngbig = $squares['left-top']['lng'] > $squares['right-bottom']['lng'] ? $squares['left-top']['lng'] : $squares['right-bottom']['lng'];
    $lngsmall = $squares['left-top']['lng'] > $squares['right-bottom']['lng'] ? $squares['right-bottom']['lng'] : $squares['left-top']['lng'];
    $info_sql = "select * from `stores` where lat<>0 and (lat between {$latsmall} and {$latbig}) and (lng between {$lngsmall} and {$lngbig})";
    $dataSql = $this->container->get('my.dataSql');
    $rs = $dataSql->querysql($info_sql);
    if(!$rs){
      return $this->sendMsgForText($this->fromUsername, $this->toUsername, time(), "text", '很抱歉，您的附近没有门店');
    }
    $datas = array();
    $fs = new \Symfony\Component\Filesystem\Filesystem();
    $data = array();
      for($i=0;$i<count($rs);$i++){
        $meter = $this->getDistance($lat,$lng,$rs[$i]['lat'],$rs[$i]['lng']);
        $meters = "(距离约" . $meter ."米)";
        $datas[$meter] = array(
          'Title' => $rs[$i]['storename'].$meters,
          'Description' => $rs[$i]['storename'],
          'PicUrl' => $rs[$i]['storelog'],
          'Url' => $this->container->get('request_stack')->getCurrentRequest()->getSchemeAndHttpHost().'/wechat/store/'.$rs[$i]['id'].'?orix='.$lat.'&oriy='.$lng,
        );
      }
    ksort($datas);
    $i=0;
    foreach($datas as $value){
      $data[$i] = $value;
      $i++;
    }
    $xml = array();
    $xml['0'] = array(
      "MsgType" => "news",
      "MsgData" => json_encode(array("Articles" => $data), JSON_UNESCAPED_UNICODE),
    );
    $WechatMsg = new WechatMsg($this->fromUsername, $this->toUsername);
    return $WechatMsg->sendMsgxml($xml);
  }

}
