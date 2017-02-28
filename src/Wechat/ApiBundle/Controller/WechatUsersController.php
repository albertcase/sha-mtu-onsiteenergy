<?php

namespace Wechat\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class WechatUsersController extends Controller
{
    public function userstatusAction()
    {
      $form = $this->container->get('form.wechatuserstatus');
      $data = $form->DoData();
      return  new Response(json_encode($data, JSON_UNESCAPED_UNICODE));
    }
}
