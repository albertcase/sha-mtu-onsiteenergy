<?php

namespace Wechat\ApiBundle\Forms;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\Session\Session;

class newmenuranking extends FormRequest{
  public function rule(){
    return array(
       'menulist' => new Assert\NotBlank(),
    );
  }

  public function FormName(){
    return 'newmenuranking';
  }

  public function DoData(){
    if($this->Confirm()){
      return array('code' => '11' ,'msg' => 'your input error');
    }
    return $this->dealData();
  }

  public function dealData(){
    $dataSql = $this->container->get('my.dataSql');
    $dataSql->buildnewrankingMenu($this->getdata['menulist']);
    return  array('code' => '10' ,'msg' => 'new ranking success');
  }
}
