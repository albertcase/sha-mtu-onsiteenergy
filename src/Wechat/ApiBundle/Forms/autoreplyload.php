<?php
namespace Wechat\ApiBundle\Forms;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\Session\Session;

class autoreplyload extends FormRequest{

  public function rule(){
    return array(
      'getEvent' => new Assert\NotBlank(),
    );
  }

  public function FormName(){
    return 'autoreplyload';
  }

  public function DoData(){
    if($this->Confirm()){
      return array('code' => '11' ,'msg' => 'your input error');
    }
    return $this->dealData();
  }

  public function dealData(){
    $dataSql = $this->container->get('my.dataSql');
    if($menuId = $dataSql->searchData(array('getEvent' => $this->getdata['getEvent']), array('menuId'), 'wechat_events')){
      $menuId = $menuId[0]['menuId'];
      return array(
        'code' => '10',
        'msg' => 'success',
        'info' => $dataSql->getreplyEvent($menuId),
      );
    }
    return array('code' => '9' ,'msg' => 'you not setting this reply');
  }

}
