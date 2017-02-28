<?php
namespace ArticleBundle\Forms;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\Session\Session;
use Wechat\ApiBundle\Forms\FormRequest;

class articledel extends FormRequest{

  public function rule(){
    return array(
      'pageid' => new Assert\NotBlank(),
    );
  }

  public function FormName(){
    return 'articledel';
  }

  public function DoData(){
    if($this->Confirm()){
      return array('code' => '11' ,'msg' => 'your input error');
    }
    return $this->dealData();
  }

  public function dealData(){
    $dataSql = $this->container->get('my.dataSql');
    if($dataSql->delArticle($this->getdata)){
      return array('code' => '10', 'msg' => 'delete article success');
    }
    return array('code' => '9', 'msg' => 'delete article error');
  }

}
