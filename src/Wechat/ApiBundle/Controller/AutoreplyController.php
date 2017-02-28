<?php

namespace Wechat\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class AutoreplyController extends Controller
{
  public function autoreplyAction(){
    $sql = $this->container->get('form.autoreply');
    $data = $sql->DoData();
    return new Response(json_encode($data, JSON_UNESCAPED_UNICODE));
  }

  public function autoreplyinfoAction(){
    $sql = $this->container->get('form.autoreplyload');
    $data = $sql->DoData();
    return new Response(json_encode($data, JSON_UNESCAPED_UNICODE));
  }

  public function autoreplydelAction(){
    $sql = $this->container->get('form.autoreplydel');
    $data = $sql->DoData();
    return new Response(json_encode($data, JSON_UNESCAPED_UNICODE));
  }

  public function uploadimageAction(Request $request){ //upload image
    $fs = new \Symfony\Component\Filesystem\Filesystem();
    $dir = date('Ym' ,strtotime("now"));
    if(!$fs->exists('upload/image/'.$dir)){
      $fs->mkdir('upload/image/'.$dir);
    }
    if(!$request->files->has('uploadfile'))
      return new Response(json_encode(array('code' => '8', 'msg'=> 'error params'), JSON_UNESCAPED_UNICODE));
    $photo = $request->files->get('uploadfile');
    $Ext = strtolower($photo->getClientOriginalExtension());
    if(!in_array($Ext, array('png', 'gif', 'bmp', 'jpg', 'jpeg')))
      return new Response(json_encode(array('code' => '9', 'msg' => 'this is not a image file'), JSON_UNESCAPED_UNICODE));
    $image = 'upload/image/'.$dir.'/'.uniqid().'.'.$photo->getClientOriginalExtension();
    $fs->rename($photo, $image, true);
    // $host = $this->getRequest()->getSchemeAndHttpHost();//2.6
    $host = $this->container->get('request_stack')->getCurrentRequest()->getSchemeAndHttpHost();//2.8
    return new Response(json_encode(array('code' => '10', 'path'=> $host.'/'.$image)));
  }
}
