<?php

namespace UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class ApiController extends Controller
{
  public function adminchangepwAction(){
    $sql = $this->container->get('form.admincp');
    $data = $sql->DoData();
    return new Response(json_encode($data, JSON_UNESCAPED_UNICODE));
  }

  public function creatadminAction(){
    $Session = new Session();
    $user = $Session->get($this->container->getParameter('session_login'));
    $adminadd = $this->container->get('form.adminadd');
    $data = $adminadd->DoData();
    return new Response(json_encode($data, JSON_UNESCAPED_UNICODE));
  }

  public function changepwdAction(){
    $adminadd = $this->container->get('form.changepwd');
    $data = $adminadd->DoData();
    return new Response(json_encode($data, JSON_UNESCAPED_UNICODE));
  }

  public function getadminsAction(){
    $Session = new Session();
    $user = $Session->get($this->container->getParameter('session_login'));
    $dataSql = $this->container->get('my.dataSql');
    if($data = $dataSql->getAdmins()){
      return new Response(json_encode(array('code' => '10', 'msg' => 'get success', 'list' => $data), JSON_UNESCAPED_UNICODE));
    }
    return new Response(json_encode(array('code' => '9', 'msg' => 'there are not any admin user'), JSON_UNESCAPED_UNICODE));
  }

  public function userdelAction(){
    $Session = new Session();
    $user = $Session->get($this->container->getParameter('session_login'));
    if($user != 'admin')//only admin can create admin user
      return new Response(json_encode(array('code' => '3', 'msg' => "your don't have permission"), JSON_UNESCAPED_UNICODE));
    $form = $this->container->get('form.admindel');
    $data = $form->DoData();
    return new Response(json_encode($data, JSON_UNESCAPED_UNICODE));
  }

  public function getadminerinfoAction(){
    $Session = new Session();
    $user = $Session->get($this->container->getParameter('session_login'));
    if($user != 'admin')//only admin can create admin user
      return new Response(json_encode(array('code' => '3', 'msg' => "your don't have permission"), JSON_UNESCAPED_UNICODE));
    $form = $this->container->get('form.admininfo');
    $data = $form->DoData();
    return new Response(json_encode($data, JSON_UNESCAPED_UNICODE));
  }

  public function admincpwAction(){
    $Session = new Session();
    $user = $Session->get($this->container->getParameter('session_login'));
    if($user != 'admin')//only admin can create admin user
      return new Response(json_encode(array('code' => '3', 'msg' => "your don't have permission"), JSON_UNESCAPED_UNICODE));
    $form = $this->container->get('form.admincpw');
    $data = $form->DoData();
    return new Response(json_encode($data, JSON_UNESCAPED_UNICODE));
  }

  public function getpermissionAction(){
    $form = $this->container->get('form.permissionget');
    $data = $form->DoData();
    return new Response(json_encode($data, JSON_UNESCAPED_UNICODE));
  }

  public function permissionsetAction(){
    $form = $this->container->get('form.permissionchange');
    $data = $form->DoData();
    return new Response(json_encode($data, JSON_UNESCAPED_UNICODE));
  }
}
