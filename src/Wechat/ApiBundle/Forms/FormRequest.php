<?php
namespace Wechat\ApiBundle\Forms;

use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\Request;

class FormRequest{
  private $formname;
  public $getdata;
  private $rule;
  public $container;
  public $Request;

  public function __construct($Request, $container){
    $this->Request = $Request->getCurrentRequest();
    $this->container = $container;
    $this->formname = $this->FormName();
    $this->getdata = $this->GetData();
    $this->rule = $this->rule();
  }

  public function rule(){}

  public function FormName(){}

  public function GetData(){
    if(strtoupper($this->formname) == "POST")
      return $this->Request->request->all();
    if(strtoupper($this->formname) == "GET")
      return $this->Request->query->all();
    if(strtoupper($this->formname) == "FILES")
      return $this->Request->files->all();
    return $this->Request->request->get($this->formname);
  }

  public function Confirm(){
    $this->beforeDeal();
    $validator = Validation::createValidator();
    foreach($this->rule as $g => $g_val){
      if(is_array($g_val) && isset($g_val['exists'])){
        if(isset($this->getdata[$g])){
          $this->rule[$g] = $g_val['exists'];
        }else{
          unset($this->rule[$g]);
        }
      }
    }
    $constraint = new Assert\Collection($this->rule);
    // $violations = $validator->validateValue($this->getdata, $constraint);//2.6
    $violations = $validator->validate($this->getdata, $constraint);//2.8
    return $this->getErrors($violations);
  }

  public function beforeDeal(){}

  public function getErrors($violations){
    $errors = array();
    if($c = $violations->count()){
      for($i=0; $i<$c; $i++){
        if($violations->has($i)){
          $pname = $violations->offsetGet($i)->getPropertyPath();
          $pmsg = $violations->offsetGet($i)->getMessage();
          $errors[$pname] = $pmsg;
        }
      }
    }
    // print_r($errors);
    return $errors;
  }

}
