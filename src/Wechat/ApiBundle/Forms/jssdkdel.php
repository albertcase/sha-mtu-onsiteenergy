<?php
namespace Wechat\ApiBundle\Forms;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Filesystem\Filesystem;

class jssdkdel extends FormRequest{

    public function rule(){
      return array(
        'id' => new Assert\NotBlank(),
      );
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
      $jinfo = $this->container->get('my.dataSql')->jssdkinfo($this->getdata['id'], $uid);
      if(!$jinfo)
        return array('code' => '8' ,'msg' => 'this data is not exists');
      $this->container->get('my.dataSql')->jssdkdel($this->getdata['id']);
      if($jinfo && isset($jinfo['jsfilename']))
        $this->container->get('my.RedisLogic')->delkey('jssdk:'.$jinfo['jsfilename']);
      return array('code' => '10' ,'msg' => 'delete success');
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
