<?php

namespace Wechat\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class KeywordController extends Controller
{
  public function keywordaddAction(){
    $adminadd = $this->container->get('form.keywordadd');
    $data = $adminadd->DoData();
    return new Response(json_encode($data, JSON_UNESCAPED_UNICODE));
  }

  public function getkeywordlistAction(){
    $sql = $this->container->get('my.dataSql');
    if(!$list = $sql->getkeywordlist())
      return new Response(json_encode(array('code' => '9', 'msg' => 'there not any event'), JSON_UNESCAPED_UNICODE));
    $data = array(
      'code' => '10',
      'list' => $list,
      'msg' => 'success'
    );
    return new Response(json_encode($data, JSON_UNESCAPED_UNICODE));
  }

  public function keyworddelAction(){
    $sql = $this->container->get('form.keyworddel');
    $data = $sql->DoData();
    return new Response(json_encode($data, JSON_UNESCAPED_UNICODE));
  }

  public function getkeywordinfoAction(){
    $sql = $this->container->get('form.keywordinfo');
    $data = $sql->DoData();
    return new Response(json_encode($data, JSON_UNESCAPED_UNICODE));
  }

  public function keywordupdateAction(){
    $sql = $this->container->get('form.keywordupdate');
    $data = $sql->DoData();
    return new Response(json_encode($data, JSON_UNESCAPED_UNICODE));
  }
}
