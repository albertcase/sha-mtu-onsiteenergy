<?php
namespace Wechat\ApiBundle\Forms;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Filesystem\Filesystem;


class cardsjschoose extends FormRequest{

    public function rule(){
      return array(
        'cid' => new Assert\NotBlank(),
        'refererurl' =>  new Assert\NotBlank(),
      );
    }

    public function FormName(){
      return 'GET';
    }

    public function DoData(){
      if($this->Confirm()){
        return json_encode(array('code' => '11' ,'msg' => 'your input error'));
      }
      return $this->dealData();
    }

    public function dealData(){
      $code = $this->checkCardId();
      if($code['code'] != '10')
        return $code;
      $wx_cardchoose = $this->container->getParameter('wx_cardchoose');
      $wechat = $this->container->get('my.Wechat');
      return $wechat->getChooseCardSign($wx_cardchoose[$this->getdata['cid']]['info']);
    }

    public function checkCardId(){
      $wx_cardchoose = $this->container->getParameter('wx_cardchoose');
      if(!isset($wx_cardchoose[$this->getdata['cid']]) && $this->getdata['cid'] != '60c4349e-c302-4313-9fa8-37a8ebd59853'){
          return array('code' => '12', 'msg' => 'not found this carduuid');
      }
      $jscontct = $wx_cardchoose[$this->getdata['cid']];
      preg_match_all("/^http[s]{0,1}:\/\/([^\/]*)\/.*/", $this->getdata['refererurl'], $newpath, PREG_SET_ORDER);
      $domain = isset($newpath['0']['1'])?$newpath['0']['1']:'';
      if($this->getdata['cid'] == '60c4349e-c302-4313-9fa8-37a8ebd59853')
        $jscontct['domain'] = isset($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:'';
      if($domain === $jscontct['domain'])
        return array('code' => '10', 'msg' => 'cid allow');
      return array('code' => '13', 'msg' => 'carduuid domain not allow');
    }
}
