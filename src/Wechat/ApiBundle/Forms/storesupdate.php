<?php
namespace Wechat\ApiBundle\Forms;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\Session\Session;

class storesupdate extends FormRequest{

  public function rule(){
    return array(
      // 'id' => new Assert\Range(array('min' => 0)),
      // 'storename' => new Assert\NotBlank(),
      // 'address' => new Assert\NotBlank(),
      // 'phone' => new Assert\NotBlank(),
      // 'lat' => new Assert\NotBlank(),
      // 'lng' => new Assert\NotBlank(),
      // 'openhours' => new Assert\NotBlank(),
      // 'brandtype' => new Assert\NotBlank(),
      // 'storelog' => new Assert\Url(),
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
    $change = array(
      'storename' => isset($this->getdata['storename'])?$this->getdata['storename']:'',
      'address' => isset($this->getdata['address'])?$this->getdata['address']:'',
      'phone' => isset($this->getdata['phone'])?$this->getdata['phone']:'',
      'lat' => isset($this->getdata['lat'])?$this->getdata['lat']:'',
      'lng' => isset($this->getdata['lng'])?$this->getdata['lng']:'',
      'openhours' => isset($this->getdata['openhours'])?$this->getdata['openhours']:'',
      'brandtype' => isset($this->getdata['brandtype'])?$this->getdata['brandtype']:'',
      'storelog' => isset($this->getdata['storelog'])?$this->getdata['storelog']:'',
    );
    if($this->container->get('my.dataSql')->updateData(array('id' => $this->getdata['id']), $change, 'stores')){
      return array('code' => '10', 'msg' => 'update success');
    }
    return array('code' => '9', 'msg' => 'update error');
  }

}
