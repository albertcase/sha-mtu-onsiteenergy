<?php
namespace ArticleBundle\Forms;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\Session\Session;
use Wechat\ApiBundle\Forms\FormRequest;

class articleedit extends FormRequest{

  public function rule(){
    return array(
      'pageid' => new Assert\NotBlank(),
      'pagename' => new Assert\NotBlank(),
      'pagetitle' => new Assert\NotBlank(),
      'content' => new Assert\NotBlank(),
    );
  }

  public function FormName(){
    return 'articleedit';
  }

  public function DoData(){
    if($this->Confirm()){
      return array('code' => '11' ,'msg' => 'your input error');
    }
    return $this->dealData();
  }

  public function dealData(){
    $dataSql = $this->container->get('my.dataSql');
    $change = array(
      'pagename' => $this->getdata['pagename'],
      'pagetitle' => $this->getdata['pagetitle'],
      'content' => $this->getdata['content'],
    );
    if($dataSql->updateArticle(array('pageid' => $this->getdata['pageid']), $change)){
      return array('code' => '10', 'msg' => 'change article success');
    }
    return array('code' => '9', 'msg' => 'change article error');
  }

}
