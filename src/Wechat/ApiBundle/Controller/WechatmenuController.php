<?php

namespace Wechat\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class WechatmenuController extends Controller
{
    public function getmenusAction(){
      $fun = $this->container->get('my.functions');
      $data = array(
        'code' => '10',
        'menus' => $fun->getmenus(),
      );
      return new Response(json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    public function getmmenuAction(){
      $fun = $this->container->get('my.functions');
      $data = array(
        'code' => '10',
        'menus' => $fun->getmmenu(),
      );
      return new Response(json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    public function createmenuAction(){
      $wehcat = $this->container->get('my.Wechat');
      $data = array('code' => '9', 'msg' => 'update wechat menus error');
      $check = $wehcat->checkmenuarray();
      if(!is_array($check) && $check){
        $build = $wehcat->buildmenu();
        if($build === true ){
          $data = array('code' => '10', 'msg' => 'update wechat menus success');
        }else{
          $data = array('code' => '11' ,'msg' => $build);
        }
      }else{
        $data = $check;
      }
      return  new Response(json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    public function getbuttoninfoAction(){
      $adminadd = $this->container->get('form.buttoninfo');
      $data = $adminadd->DoData();
      return new Response(json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    public function deletebuttonAction(){
      $adminadd = $this->container->get('form.buttondel');
      $data = $adminadd->DoData();
      return new Response(json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    public function addsubbuttonAction(){
      $adminadd = $this->container->get('form.buttonaddsub');
      $data = $adminadd->DoData();
      return new Response(json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    public function addmbuttonAction(){
      $adminadd = $this->container->get('form.buttonaddm');
      $data = $adminadd->DoData();
      return  new Response(json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    public function updatebuttonAction(){
      $adminadd = $this->container->get('form.buttonupdate');
      $data = $adminadd->DoData();
      return  new Response(json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    public function updateeventAction(){
      $dataSql = $this->container->get('my.dataSql');
      $data = array('code' => '9');
      if($dataSql->updateEvent($data, array()))
        $data = array('code' => '10');
      return  new Response(json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    public function geteventAction(){
      $dataSql = $this->container->get('my.dataSql');
      $data = array('code' => '9');
      if($out = $dataSql->getEvent($data))
        $data = array('code' => '10', 'event' => $out);
      return  new Response(json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    public function newmenurankingAction(){
      $form = $this->container->get('form.newmenuranking');
      $data = $form->DoData();
      return  new Response(json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    public function cleartokenAction(){
      $redis = $this->container->get('my.RedisLogic');
      $redis->delkey('access_token');
      $redis->delkey('access_ticket');
      return  new Response(json_encode(array('code' => 10, 'msg' => 'success'), JSON_UNESCAPED_UNICODE));
    }
}
