<?php
namespace ArticleBundle\Forms;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\Session\Session;
use Wechat\ApiBundle\Forms\FormRequest;

class articleadd extends FormRequest{

  public function rule(){
    return array(
      'pagename' => new Assert\NotBlank(),
      'pagetitle' => new Assert\NotBlank(),
      'content' => new Assert\NotBlank(),
    );
  }

  public function FormName(){
    return 'articleadd';
  }

  public function DoData(){
    if($this->Confirm()){
      return array('code' => '11' ,'msg' => 'your input error');
    }
    return $this->dealData();
  }

  public function dealData(){
    $dataSql = $this->container->get('my.dataSql');
    $Session = new Session();
    $this->getdata['submiter'] = $Session->get($this->container->getParameter('session_login'));
    if($dataSql->createArticle($this->getdata)){
      return array('code' => '10', 'msg' => 'add article success');
    }
    return array('code' => '9', 'msg' => 'add article error');
  }

}
