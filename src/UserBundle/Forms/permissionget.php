<?php
namespace UserBundle\Forms;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\Session\Session;
use Wechat\ApiBundle\Forms\FormRequest;

class permissionget extends FormRequest{
  public function rule(){
    return array(
      'uid' => new Assert\Range(array('min' => 2)),
    );
  }

  public function FormName(){
    return 'POST';
  }

  public function DoData(){
    if($this->Confirm()){
      return array('code' => '11' ,'msg' => 'your input error');
    }
    return $this->dealData();
  }

  public function dealData(){
    $dataSql = $this->container->get('my.dataSql');
    $pres = $dataSql->getUserPermission($this->getdata['uid']);
    return array(
      'code' => '10' ,
      'msg' => 'success' ,
      'pers' => $pres,
      'allpers' => $this->getAllPermission(),
    );
  }

  private function getAllPermission(){
    $papis = array();
    $bundles = $this->container->getParameter('bundles');
    foreach ($bundles as $x) {
      if($this->container->hasParameter($x.'_permission'))
        $papis = array_merge($papis ,$this->container->getParameter($x.'_permission'));
    }
    return $papis;
  }
}
