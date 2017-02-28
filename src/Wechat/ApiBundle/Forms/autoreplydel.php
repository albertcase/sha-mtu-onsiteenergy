<?php
namespace Wechat\ApiBundle\Forms;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\Session\Session;

class autoreplydel extends FormRequest{

  public function rule(){
    return array(
      'getEvent' => new Assert\NotBlank(),
      'getMsgType' => new Assert\NotBlank(),
    );
  }

  public function FormName(){
    return 'autoreplydel';
  }

  public function DoData(){
    if($this->Confirm()){
      return array('code' => '11' ,'msg' => 'your input error');
    }
    return $this->dealData();
  }

  public function dealData(){
    $dataSql = $this->container->get('my.dataSql');
    if($menuId = $dataSql->searchData(array('getMsgType' => $this->getdata['getMsgType'], 'getEvent' => $this->getdata['getEvent']), array('menuId'), 'wechat_events')){
      $menuId = $menuId[0]['menuId'];
      $dataSql->delEvent($menuId);
    }
    return array('code' => '10', 'msg' => 'success');
  }
}
