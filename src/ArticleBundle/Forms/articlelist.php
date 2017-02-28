<?php
namespace ArticleBundle\Forms;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\Session\Session;
use Wechat\ApiBundle\Forms\FormRequest;

class articlelist extends FormRequest{

  public function rule(){
    return array(
      // 'pageid' => new Assert\NotBlank(),
      // 'pagename' => new Assert\NotBlank(),
      // 'pagetitle' => new Assert\NotBlank(),
      // 'content' => new Assert\NotBlank(),
    );
  }

  public function FormName(){
    return 'articlelist';
  }

  public function DoData(){
    if($this->Confirm()){
      return array('code' => '11' ,'msg' => 'your input error');
    }
    return $this->dealData();
  }

  public function dealData(){
    $dataSql = $this->container->get('my.dataSql');
    if($list = $dataSql->getArticlelist(array())){
      return array('code' => '10', 'list' => $list, 'msg' => 'success');
    }
    return array('code' => '9', 'msg' => 'not any');
  }

}
