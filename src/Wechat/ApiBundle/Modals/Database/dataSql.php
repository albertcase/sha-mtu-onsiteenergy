<?php

namespace Wechat\ApiBundle\Modals\Database;

class dataSql{
  private $_db;
  private $_container;

  public function __construct($container){
    $this->_db = $container->get('vendor.MysqliDb');
    $this->_container = $container;
  }

  public function rebuilddb(){
    return clone $this->_db;
  }

  public function syncMaterial($data){
    if(!is_array($data) || !isset($data[0]))
      return false;
    $article = array();
    $material = array();
    foreach($data as $x => $x_val){
      if(isset($x_val['content']) && isset($x_val['content']['news_item'])){
        $material[] = array(
          'media_id' => $x_val['media_id'],
          'update_time' => $x_val['update_time']
        );
        if(!isset($article[$x_val['media_id']]))
          $article[$x_val['media_id']] = array();
        foreach($x_val['content']['news_item'] as $xx => $xx_val){
          $article[$x_val['media_id']][] = array(
            'title' => $this->getArrayParams($xx_val, 'title'),
            'media_id' => $x_val['media_id'],
            'thumb_media_id' => $this->getArrayParams($xx_val, 'thumb_media_id'),
            'show_cover_pic' => $this->getArrayParams($xx_val, 'show_cover_pic'),
            'author' => $this->getArrayParams($xx_val, 'author'),
            'digest' => $this->getArrayParams($xx_val, 'digest'),
            'url' => $this->getArrayParams($xx_val, 'url'),
            'content_source_url' => $this->getArrayParams($xx_val, 'content_source_url'),
            'thumb_url' => $this->getArrayParams($xx_val, 'thumb_url'),
          );
        }
      }
    }
    $this->insertMainMaterial($material);
    $this->insertMaterial($article);
  }

  public function insertMaterial($data){
    foreach($data as $x => $x_val){
      if(!$this->searchData(array('media_id' => $x), array('id'), 'wechat_material_news')){
        foreach($x_val as $xx){
            $this->insertData($xx,'wechat_material_news');
        }
      }
    }
  }

  public function insertMainMaterial($data){
    foreach($data as $x){
      if(!$result = $this->searchData(array('media_id' => $x['media_id']), array('id', 'update_time'), 'wechat_material')){
        $this->insertData($x,'wechat_material');
      }else{
        if(isset($result['0']) && isset($result['0']['update_time']) && $result['0']['update_time']!=$x['update_time']){
          $this->deleteData(array("media_id" => $x['media_id']), 'wechat_material_news');
          $this->updateData(array('media_id' => $x['media_id']), array('update_time' => $x['update_time']), 'wechat_material');
        }
      }
    }
  }

  public function getArrayParams($arr, $name){
    return isset($arr[$name])?$arr[$name]:'';
  }

  public function tempEventLog($openid,$texts,$event,$templog){
    $data = array(
      'openid' => $openid,
      'texts' => $texts,
      'event' => $event,
      'templog' => $templog,
    );
    $this->insertData($data, 'temp_event_log');
  }

  public function getLocalpath($url){
    $paths = $this->searchData(array('url' => $url) ,array('path'), 'file_path');
    if($paths)
      return $paths['0']['path'];
    return false;
  }

  public function setLocalpath($url,$path){
    $paths = $this->searchData(array('url' => $url) ,array('path'), 'file_path');
    if($paths)
      return $paths['0']['path'];
    $db = $this->rebuilddb();
    $db->insert('file_path', array('url' => $url, 'path' => $path));
    return true;
  }

  public function getUserInfo($username){
    $id = $this->getUserid($username);
    return array(
      'username' => $username,
      'uid' => $id,
      'permission' => $this->getUserPermission($id),
    );
  }

  public function getUserInfoId($id){
    return array(
      'username' => $this->getUsername($id),
      'uid' => $id,
      'permission' => $this->getUserPermission($id),
    );
  }

  public function getUserid($username){
    $ids = $this->searchData(array('username' => $username) ,array('id'), 'wechat_admin');
    return $ids['0']['id'];
  }

  public function getUsername($id){
    $names = $this->searchData(array('id' => $id) ,array('username'), 'wechat_admin');
    return $names['0']['username'];
  }

  public function getUserPermission($id){
    $pers = $this->searchData(array('uid' => $id) ,array('premission'), 'user_premission');
    $out = array();
    foreach ($pers as $x) {
      array_push($out, $x['premission']);
    }
    return array_unique($out);
  }

  public function setPermission($uid, $pers){
    $this->deleteData(array('uid' => $uid), 'user_premission');
    $out = array();
    foreach($pers as $x){
      array_push($out, array('uid' => $uid, 'premission' => $x));
    }
    $this->insertsData($out, 'user_premission');
  }

  public function systemLog($postStr, $fromUsername, $msgType){
    $db = $this->rebuilddb();
    $data = array(
      'msgType' => $msgType,
      'msgXml' => $postStr,
      'openid' => $fromUsername,
    );
    $db->insert('wechat_getmsglog', $data);
  }

  public function textField($Content){
    $db = $this->rebuilddb();
    $db->join("wechat_events b", "a.menuId=b.menuId", "LEFT");
    $db->where("b.getMsgType", 'text');
    $db->where("b.getContent", trim($Content));
    return $db->get("wechat_feedbacks a", null, "a.MsgData, a.MsgType");
  }

  public function subscribeField(){
    $db = $this->rebuilddb();
    $db->join("wechat_events b", "a.menuId=b.menuId", "LEFT");
    $db->where("b.getMsgType", 'event');
    $db->where("b.getEvent", 'subscribe');
    return $db->get("wechat_feedbacks a", null, "a.MsgData, a.MsgType");
  }

  public function defaultField(){
    $db = $this->rebuilddb();
    $db->join("wechat_events b", "a.menuId=b.menuId", "LEFT");
    $db->where("b.getMsgType", 'event');
    $db->where("b.getEvent", 'defaultback');
    return $db->get("wechat_feedbacks a", null, "a.MsgData, a.MsgType");
  }

  public function clickField($EventKey){
    $db = $this->rebuilddb();
    $db->join("wechat_events b", "a.menuId=b.menuId", "LEFT");
    $db->where("b.getMsgType", 'event');
    $db->where("b.getEvent", 'click');
    $db->where("b.getEventKey", trim($EventKey));
    return $db->get("wechat_feedbacks a", null, "a.MsgData, a.MsgType");
  }

  public function getmenus(){
    return $this->searchData(array() ,array('menuName', 'eventtype', 'eventKey', 'eventUrl', 'width'), 'wechat_menu');
  }

  public function buildnewrankingMenu($list){
    foreach($list as $x => $x_val){
      $i = 1;
      if(is_array($x_val)){
        foreach($x_val as $xx){
          $this->updateData(array('id' => $xx ), array('width' => $i), 'wechat_menu');
          $i++;
        }
      }
    }
  }

  public function getmenusDb(){
    return $this->searchData(array() ,array('id', 'menuName', 'eventtype', 'eventKey', 'eventUrl', 'width' ,'eventmedia_id'), 'wechat_menu');
  }

  public function getMenuHierarchy(){
    return $this->searchData(array() ,array('tid', 'parent'), 'wechat_menu_hierarchy');
  }

  public function addSubButton($mOrder){//added sub button
    $count = $this->getCount(array('mOrder' => $mOrder), 'wechat_menu');
    $count = intval($count);
    if($count == '0')
      return false;
    if($count > '5')
      return false;
    if($id = $this->insertData(array(
      'mOrder' => $mOrder,
      'subOrder' => $count+1,
      'menuName' => '新菜单',
      'eventtype' => 'view',
      'eventUrl' => 'http://'),
      'wechat_menu'
    ))
      return $id;
    return false;
  }

  public function addMButton($data){//added main button
    $count = $this->getCount(array('subOrder' => '0'), 'wechat_menu');
    if($count >= 3)
      return false;
    if($id = $this->insertData(array('mOrder' => $count, 'subOrder' => '0', 'menuName' => '新菜单', 'eventtype' => 'view', 'eventUrl' => 'http://'), 'wechat_menu'))
      return $id;
    return false;
  }

  public function buttoninfo($id){
    $info = $this->searchData(array('id' => $id) ,array(), 'wechat_menu');
    if(isset($info[0])){
      $info = $info[0];
      $info['buttonevent'] = $this->getbuttonEvent(array('menuId' => $id));
      if($info['eventmedia_id']){
        $sql = "SELECT A.id,A.media_id,A.update_time,B.title,B.digest,B.url,B.thumb_url FROM wechat_material as A LEFT JOIN wechat_material_news as B ON A.media_id=B.media_id WHERE A.media_id = ?";
        if($list = $this->querysqlp($sql, array($info['eventmedia_id']))){
          $info['eventmedia_info'] = $this->dealList($list);
        }
      }
      return $info;
    }
    return false;
  }

  public function dealList($list){
    $result = array();
    foreach($list as $x){
      if(!isset($result[$x['media_id']]))
        $result[$x['media_id']] = array();
      $result[$x['media_id']][] = $x;
    }
    return $result;
  }

  public function updateButton($id, $button = array(), $event = array()){//set old button
    $change = array(
      'eventtype' => '',
      'eventKey' => '',
      'eventUrl' => '',
      'eventmedia_id' => '',
    );
    $this->updateData(array('id' => $id ), $change, 'wechat_menu');
    $this->updateData(array('id' => $id ), $button, 'wechat_menu');
    $this->updateEvent(array('menuId' => $id), $event);
    return true;
  }

  public function getbuttonEvent($data){
    $result = array();
    $out = $this->searchData($data, array(), 'wechat_feedbacks');
    if($out && isset($out['0'])){
      $MsgData = json_decode($out['0']['MsgData'], true);
      if($out['0']['MsgType'] == 'news'){
        $result['newslist'] = $MsgData['Articles'];
      }else{
        $result = $MsgData;
        $result['MsgType'] = $out['0']['MsgType'];
      }
      return $result;
    }
    return false;
  }

  public function getEvent($data){//get Event
    $result = array();
    $out = $this->searchData($data, array(), 'wechat_menu_event');
    if($out && isset($out[0])){
      if(count($out) == '1'){
        $result = $out['0'];
      }else{
        $result['getMsgType'] = $out[0]['getMsgType'];
        $result['getContent'] = $out[0]['getContent'];
        $result['getEvent'] = $out[0]['getEvent'];
        $result['getEventKey'] = $out[0]['getEventKey'];
        $result['getTicket'] = $out[0]['getTicket'];
        $result['alldata'] = $out;
      }
      return $result;
    }
    return false;
  }

// do event
  public function updateEvent($data, $change = array()){
    if($data){
      $this->deleteData($data, 'wechat_feedbacks');
      $this->deleteData($data, 'wechat_events');
    }
    if(isset($change['feedbacks']))
      $this->insertData($change['feedbacks'], 'wechat_feedbacks');
    if(isset($change['getevent']))
      $this->insertData($change['getevent'], 'wechat_events');
    return true;
  }

  public function addEvent($change = array()){
    if($change){
      $this->insertData($change['feedbacks'], 'wechat_feedbacks');
      $this->insertData($change['getevent'], 'wechat_events');
    }
    return true;
  }

  public function addTagEvents($change = array()){
    if($change){
      $this->insertsData($change['getevent'],'wechat_events');
      $this->insertData($change['feedbacks'], 'wechat_feedbacks');
    }
    return true;
  }

  public function delTag($menuId){
    $this->delEvent($menuId);
    return $this->deleteData(array('menuId' => $menuId), 'wechat_keyword_tag');
  }

  public function checktagnewname($menuId,$Tagname){
    $sql = 'SELECT * from wechat_keyword_tag where Tagname = ? and menuId != ?';
    $param = array($Tagname, $menuId);
    return $this->querysqlp($sql,$param);
  }

  public function checktagnewkey($menuId,$getContent){
    $sql = 'SELECT * from wechat_events where getContent = ? and menuId != ?';
    $param = array($getContent, $menuId);
    return $this->querysqlp($sql,$param);
  }

  public function delEvent($menuId){
    $this->deleteData(array('menuId' => $menuId), 'wechat_feedbacks');
    $this->deleteData(array('menuId' => $menuId), 'wechat_events');
    return true;
  }

  public function getreplyEvent($menuId){
    if($back = $this->searchData(array('menuId' => $menuId), array('menuId', 'MsgType', 'MsgData'), 'wechat_feedbacks')){
      $back[0]['MsgData'] = json_decode($back[0]['MsgData'], true);
      return $back[0];
    }
    return false;
  }

  public function getTagEvents($menuId){
    $result = array();
    $tag = $this->searchData(array('menuId' => $menuId), array(), 'wechat_keyword_tag');
    $feedbacks = $this->searchData(array('menuId' => $menuId), array(), 'wechat_feedbacks');
    $events = $this->searchData(array('menuId' => $menuId), array(), 'wechat_events');
    if($tag && isset($tag['0'])){
      if(!isset($feedbacks['0']))
        $feedbacks['0'] = array();
      if(!isset($events['0']))
        $events['0'] = array();
      if(isset($feedbacks['0']['MsgType']) && $feedbacks['0']['MsgType'] == 'news'){
        $newslist = json_decode(($feedbacks['0']['MsgData']?$feedbacks['0']['MsgData']:array()), true);
        $result['menuId'] = $menuId;
        $result['Tagname'] = isset($tag['0']['Tagname'])?$tag['0']['Tagname']:'';
        $result['getMsgType'] = 'text';
        $result['getContent'] = $this->getkeywords($events);
        $result['newslist'] = $newslist['Articles'];
        $result['MsgType'] = isset($events['0']['MsgType'])?$events['0']['MsgType']:'';
      }else{
        $MsgData = json_decode(($feedbacks['0']['MsgData']?$feedbacks['0']['MsgData']:array()), true);
        $result = $MsgData;
        $result['menuId'] = $menuId;
        $result['Tagname'] = isset($tag['0']['Tagname'])?$tag['0']['Tagname']:'';
        $result['getMsgType'] = 'text';
        $result['getContent'] = $this->getkeywords($events);
        $result['MsgType'] = isset($events['0']['MsgType'])?$events['0']['MsgType']:'';
      }
      return $result;
    }
    return false;
  }

  public function getkeywords($events){
    $keys = array();
    foreach($events as $x){
      $keys[] = $x['getContent'];
    }
    return $keys;
  }
// do event end
  public function getkeywordlist(){
    $sql = "SELECT menuId,Tagname FROM wechat_keyword_tag";
    return $this->querysql($sql);
  }

//deleteButton main start
  public function deleteButton($id){
    $info = $this->searchData(array('id' => $id), array('id','width'), 'wechat_menu');
    if(!isset($info[0]))
      return false;
    $info = $info[0];
    $this->deleteData(array('menuId' => $id), 'wechat_feedbacks');
    $this->deleteData(array('menuId' => $id), 'wechat_events');
    $hinfo = $this->searchData(array('parent' => $id), array('tid'), 'wechat_menu_hierarchy');
    if(count($hinfo)){
      $menuids = array();
      foreach($hinfo as $x)
        $menuids[] = $x['tid'];
      $this->deleteButtonEvent($menuids);
      $this->deleteSubMenu($menuids);
      $this->decmOrder($info['width'], $id);
    }else{
      $this->decmOrder($info['width'], $id);
      // $this->decsubOrder($info['width'], $id);
    }
    $this->deleteData(array('tid' => $id), 'wechat_menu_hierarchy');
    $this->deleteData(array('parent' => $id), 'wechat_menu_hierarchy');
    $this->deleteData(array('id' => $id), 'wechat_menu');
    return true;
  }

  public function deleteSubMenu($menuids){
    $this->rebuilddb()->where('id', $menuids, 'IN')->delete('wechat_menu');
  }

  public function deleteButtonEvent($menuids){
    if($this->deletesubevents($menuids))
      return true;
    return false;
  }

  public function deletesubevents($menuid){
    if($menuid && is_array($menuid)){
      $db = $this->rebuilddb();
      $db2 = $this->rebuilddb();
      $db->where('menuId', $menuid, 'IN');
      $db2->where('menuId', $menuid, 'IN');
      if(!$db->delete('wechat_feedbacks'))
        return false;
      if(!$db2->delete('wechat_events'))
        return false;
      return true;
    }
    return true;
  }

  public function decsubOrder($mOrder, $subOrder){
    $db = $this->rebuilddb();
    $data = array(
      'subOrder' => $db->dec(),
    );
    $db->where('mOrder', $mOrder);
    $db->where('subOrder', $subOrder, ">");
    if($db->update('wechat_menu', $data))
      return true;
    return false;
  }

  public function decmOrder($mOrder, $id){
    $db = $this->rebuilddb();
    $hinfo = $this->searchData(array('tid' => $id), array('parent'), 'wechat_menu_hierarchy');
    if(!$hinfo || !isset($hinfo[0]))
      return false;
    $hson = $this->searchData(array('parent' => $hinfo[0]['parent']), array('tid'), 'wechat_menu_hierarchy');
    if(!$hinfo)
      return false;
    $menuids = array();
    foreach($hson as $x)
      $menuids[] = $x['tid'];
    $data = array(
      'width' => $db->dec(),
    );
    $db->where('id', $menuids, "IN")->where('width', $mOrder, ">");
    if($db->update('wechat_menu', $data));
      return true;
    return false;
  }

//deleteButton main end
//admin start
  public function createwechatAdmin($data){
    $data['password'] = md5($data['password'].'185');
    return $this->insertData($data, 'wechat_admin');
  }

  public function changepassword($data, $change){
    $data['password'] = md5($data['password'].'185');
    if($this->getCount($data, 'wechat_admin')){
      $change['password'] = md5($change['password'].'185');
      return $this->updateData($data, $change, 'wechat_admin');
    }
    return false;
  }

  public function admincpw($data,$change){
    $change['password'] = md5($change['password'].'185');
    return $this->updateData($data, $change, 'wechat_admin');
  }

  public function comfirmAdmin($data){
    $data['password'] = md5($data['password'].'185');
    if($this->getCount($data, 'wechat_admin')){
      $this->updateData($data, array('latestTime' => date('Y-m-d H:i:s' ,strtotime("now"))), 'wechat_admin');
      return true;
    }
    return false;
  }

  public function getAdmins(){
    $sql = 'SELECT id,username,latestTime from wechat_admin where username != "admin"';
    return $this->querysql($sql);
  }

  public function getAdmininfo($id){
    $sql = 'SELECT id,username from wechat_admin where id = ?';
    $param = array($id);
    return $this->querysqlp($sql,$param);
  }

  public function delAdminuser($id){
    return $this->deleteData(array('id' => $id), 'wechat_admin');
  }
//admin end
// adp_article
  public function createArticle($data){
    $data['pageid'] = uniqid();
    if($this->insertData($data, 'adp_article'))
      return $data['pageid'];
    return false;
  }

  public function updateArticle($data, $change){
    $change['edittime'] = date('Y-m-d H:i:s' ,strtotime("now"));
    return $this->updateData($data, $change, 'adp_article');
  }

  public function getArticle($data){
    return $this->searchData($data , array(), 'adp_article');
  }

  public function delArticle($data){
    if($data)
      return $this->deleteData($data, 'adp_article');
    return false;
  }

  public function getArticlelist($data){
    return $this->searchData($data , array('pageid', 'pagename', 'pagetitle', 'submiter', 'edittime'), 'adp_article');
  }

// adp_article end
// oauth start
public function addoauth($data){
  return $this->insertData($data, 'wechat_oauth');
}

public function oauthlist(){
  return $this->searchData(array() ,array(), 'wechat_oauth');
}

public function checkoauth($data){
  $result = $this->searchData($data ,array('oauthfile'), 'wechat_oauth');
  if($result && isset($result['0']))
    return $result['0']['oauthfile'];
  return false;
}

public function oauthdel($data){
  if($data)
    return $this->deleteData($data, 'wechat_oauth');
  return false;
}

public function oauthupdate($id, $change){
  return $this->updateData(array('id' => $id), $change, 'wechat_oauth');
}

public function oauthinfo($id){
  return $this->searchData(array('id' => $id) ,array(), 'wechat_oauth');
}
// oauth end
//jssdk start
  public function addujssdk($data){
    $paths = $this->searchData(array('jsfilename' => $data['jsfilename']) ,array('id'), 'wechat_jssdk');
    if($paths)
      return '9';//this js file already exists
    return $this->insertData($data, 'wechat_jssdk');
  }

  public function jssdklist($uid){
    $uid = intval($uid);
    $sql = "SELECT A.id,A.name,A.jsfilename,B.username FROM wechat_jssdk A LEFT JOIN wechat_admin B ON B.id=A.editorid".(($uid==1)?"":" WHERE A.editorid={$uid}");
    return $this->querysql($sql);
  }

  public function jssdkinfo($id, $uid){
    if($uid == 1){
      $info = $this->searchData(array('id' => $id) ,array('name','domain','jscontent','jsfilename'), 'wechat_jssdk');
    }else{
      $info = $this->searchData(array('id' => $id, 'editorid' => $uid) ,array('name','domain','jscontent','jsfilename'), 'wechat_jssdk');
    }
    if($info && isset($info['0']))
      return $info['0'];
    return array();
  }

  public function jssdkupdate($id, $change){
    return $this->updateData(array('id' => $id), $change, 'wechat_jssdk');
  }

  public function jssdkdel($id){
    return $this->deleteData(array('id' => intval($id)), 'wechat_jssdk');
  }
//jssdk start
// qrcode start
  public function qrcodelist(){
    return $this->searchData(array() , array(), 'wechat_qrcode');
  }

  public function qrcodeAdd($data){
    return $this->insertData($data, 'wechat_qrcode');
  }

  public function qrcodeinfo($id){
    return $this->searchData(array('id' => intval($id)), array('id', 'qrName', 'feedbackid'), 'wechat_qrcode');
  }

  public function qrcodeUpdate($data, $change){
    return $this->updateData($data, $change, 'wechat_qrcode');
  }

  public function qrcodefeedback($menuId){
    return $this->searchData(array('menuId' => $menuId), array(), 'wechat_feedbacks');
  }

  public function qrcodeScanTimes($qrcode){
    $info = $this->searchData(array('qrTicket' => trim($qrcode)), array('id', 'feedbackid'), 'wechat_qrcode');
    if(!$info || !isset($info['0']))
      return array();
    $db = $this->rebuilddb();
    $change = array(
      'qrScan' => $db->inc(),
    );
    $db->where('qrTicket', trim($qrcode));
    $db->update('wechat_qrcode', $change);
    return $this->qrcodefeedback($info['0']['feedbackid']);
  }

  public function qrcodeSubscribeTimes($qrcode){
    $info = $this->searchData(array('qrTicket' => trim($qrcode)), array('id', 'feedbackid'), 'wechat_qrcode');
    if(!$info || !isset($info['0']))
      return array();
    $db = $this->rebuilddb();
    $change = array(
      'qrScan' => $db->inc(),
      'qrSubscribe' => $db->inc()
    );
    $db->where('qrTicket', trim($qrcode));
    $db->update('wechat_qrcode', $change);
    return $this->qrcodefeedback($info['0']['feedbackid']);
  }
// qrcode end
//subscript event

  public function userSubscript($openid){
    if(!$this->searchData(array('openid' => $openid) , array('id'), 'wechat_users')){
      $data = array(
        'openid' => $openid,
        'status' => 1,
      );
      return $this->insertData($data, 'wechat_users');
    }
    return $this->updateData(array('openid' => $openid), array('status' => 1), 'wechat_users');
  }

  public function userUnsubscript($openid){
    if(!$this->searchData(array('openid' => $openid) , array('id'), 'wechat_users')){
      $data = array(
        'openid' => $openid,
        'status' => 2,
      );
      return $this->insertData($data, 'wechat_users');
    }
    return $this->updateData(array('openid' => $openid), array('status' => 2), 'wechat_users');
  }
//subscript evnet end
  public function insertData($data, $table){
    $db = $this->rebuilddb();
    return $db->insert($table, $data);
  }

  public function insertsData($datas, $table){
    foreach($datas as $x){
      $this->insertData($x, $table);
    }
  }

  public function searchData(array $data=array() ,array $dataout=array(), $table, $limit = null){
    $db = $this->rebuilddb();
    foreach($data as $x => $x_val){
      $db->where($x,$x_val);
    }
    return $db->get($table, $limit ,$dataout);
  }

  public function updateData($data, $change, $table){
    $db = $this->rebuilddb();
    foreach($data as $x => $x_val){
      $db->where($x,$x_val);
    }
    if($db->update($table, $change))
      return true;
    return false;
  }

  public function deleteData($data, $table){
    $db = $this->rebuilddb();
    foreach($data as $x => $x_val){
      $db->where($x,$x_val);
    }
    if($db->delete($table))
      return true;
    return false;
  }

  public function getCount($data, $table){
    $db = $this->rebuilddb();
    foreach($data as $x => $x_val){
      $db->where($x,$x_val);
    }
    $stats = $db->getOne ($table, "count(*) as cnt");
    return $stats['cnt'];
  }

  public function querysql($sql){
    $db = $this->rebuilddb();
    return $db->rawQuery ($sql);
  }

  public function querysqlp($sql, $param){
    $db = $this->rebuilddb();
    return $db->rawQuery ($sql,$param);
  }
}
