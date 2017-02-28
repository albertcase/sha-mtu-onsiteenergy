<?php
namespace Wechat\ApiBundle\Forms;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Filesystem\Filesystem;


class jssdklist extends FormRequest{

    public function rule(){
      return array();
    }

    public function FormName(){
      return 'POST';
    }

    public function DoData(){
      if($this->Confirm()){
        return array('code' => '11' ,'msg' => 'your input error');
      }
      return $this->dealData();
    }

    public function dealData(){
      $userinfo = $this->getUserinfo();
      $uid = $userinfo['uid'];
      if(in_array('user_usercontrol', $userinfo['permission']))
        $uid = 1;
      $list = $this->container->get('my.dataSql')->jssdklist($uid);
      if($list)
        return array('code' => '10' ,'msg' => 'get success', 'list' => $list);
      return array('code' => '10' ,'msg' => 'there are not any data', 'list' => array());
    }

    private function getUserinfo(){
      $userinfo = array(
        "username" => 'anoymous',
        "uid" => 0,
        "permission" => array()
      );
      $Session = new Session();
      if($Session->has($this->container->getParameter('session_login'))){
        $user = $Session->get($this->container->getParameter('session_login'));
        if($this->container->get('my.RedisLogic')->checkString("user:".$user)){
          return json_decode($this->container->get('my.RedisLogic')->getString("user:".$user), true);
        }
        return $userinfo;
      }
      return $userinfo;
    }
}
