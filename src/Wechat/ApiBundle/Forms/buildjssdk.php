<?php
namespace Wechat\ApiBundle\Forms;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Filesystem\Filesystem;
use Lootils\Uuid\Uuid;

class buildjssdk extends FormRequest{

    public function rule(){
      return array(
        // 'jscontent' => new Assert\NotBlank(),
        'domain' => new Assert\NotBlank(),
        'name' => new Assert\NotBlank(),
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
      $datain = array(
        'name' => $this->getdata['name'],
        'domain' => $this->getdata['domain'],
        'jsfilename' => (String)Uuid::createV4(),
        // 'jscontent' => $this->getdata['jscontent'],
        'editorid' => $userinfo['uid'],
      );
      $this->container->get('my.dataSql')->addujssdk($datain);
      return array('code' => '10' ,'msg' => 'build success');
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
