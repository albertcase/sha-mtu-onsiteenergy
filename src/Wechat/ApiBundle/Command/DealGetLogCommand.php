<?php
namespace Wechat\ApiBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;

class DealGetLogCommand extends Command{

  protected function configure(){
    $this->setName('wechat:deal.getlog')
        ->setDescription('Deal GetLog Data');
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $this->DealLog();
    $output->writeln('<info>update log analyse success</info>');
  }

  protected function DealLog(){
    $sql2 = "SELECT max(`analyseid`) as maxid FROM `request_analyse`";
    $db = $this->getApplication()->getKernel()->getContainer()->get('my.dataSql');
    $analyse = $db->querysql($sql2);
    $start_id = 0;
    if($analyse && isset($analyse['0']) && isset($analyse['0']['maxid']))
      $start_id = $analyse['0']['maxid'];
    $this->DealRow(intval($start_id));
  }

  public function GetObjStr($obj, $param, $default = ''){
    return trim(isset( $obj->$param ) ? $obj->$param : $default);
  }

  protected function GetNextRow($start_id){
    $db = $this->getApplication()->getKernel()->getContainer()->get('my.LdataSql');
    $sql = "SELECT `id`,`msgXml`,`createTime` FROM `wechat_getmsglog` WHERE id>{$start_id} LIMIT 1";
    return $db->querysql($sql);
  }

  protected function DealRow($start_id){
    $db = $this->getApplication()->getKernel()->getContainer()->get('my.LdataSql');
    while ($result = $this->GetNextRow($start_id)) {
      if($result && isset($result['0'])){
        $postObj = simplexml_load_string($result['0']['msgXml'], 'SimpleXMLElement', LIBXML_NOCDATA);
        $data = array(
          'ToUserName' => $this->GetObjStr($postObj, 'ToUserName'),
          'FromUserName' => $this->GetObjStr($postObj, 'FromUserName'),
          'MsgType' => strtolower($this->GetObjStr($postObj, 'MsgType')),
          'analyseid' => $result['0']['id'],
          'CreateTime' => $result['0']['createTime'],
        );
        $db->insertData($data, 'request_analyse');
        if(method_exists($this, 'request_'.$data['MsgType'])){
          call_user_func_array(array($this, 'request_'.$data['MsgType']), array($postObj, $data['analyseid']));
        }
        $start_id = $data['analyseid'];
      }else{
        break;
      }
    }

  }

  protected function request_event($postObj, $id){
    $this->getApplication()->getKernel()->getContainer()->get('my.LdataSql')->insertData(
    array(
      'analyseid' => $id,
      'Event' => $this->GetObjStr($postObj, 'Event'),
      'EventKey' => $this->GetObjStr($postObj, 'EventKey'),
      'Ticket' => $this->GetObjStr($postObj, 'Ticket'),
    ), 'request_event');
  }

  protected function request_image($postObj, $id){
    $this->getApplication()->getKernel()->getContainer()->get('my.LdataSql')->insertData(
    array(
      'analyseid' => $id,
      'PicUrl' => $this->GetObjStr($postObj, 'PicUrl'),
      'MediaId' => $this->GetObjStr($postObj, 'MediaId'),
    ), 'request_image');
  }

  protected function request_link($postObj, $id){
    $this->getApplication()->getKernel()->getContainer()->get('my.LdataSql')->insertData(
    array(
      'analyseid' => $id,
      'Title' => $this->GetObjStr($postObj, 'Title'),
      'Description' => $this->GetObjStr($postObj, 'Description'),
      'Url' => $this->GetObjStr($postObj, 'Url'),
    ), 'request_link');
  }

  protected function request_location($postObj, $id){
    $this->getApplication()->getKernel()->getContainer()->get('my.LdataSql')->insertData(
    array(
      'analyseid' => $id,
      'Location_X' => $this->GetObjStr($postObj, 'Location_X'),
      'Location_Y' => $this->GetObjStr($postObj, 'Location_Y'),
      'Scale' => $this->GetObjStr($postObj, 'Scale'),
      'Label' => $this->GetObjStr($postObj, 'Label'),
    ), 'request_location');
  }

  protected function request_text($postObj, $id){
    $this->getApplication()->getKernel()->getContainer()->get('my.LdataSql')->insertData(
    array(
      'analyseid' => $id,
      'Content' => $this->GetObjStr($postObj, 'Content'),
    ), 'request_text');
  }

  protected function request_video($postObj, $id){
    $this->getApplication()->getKernel()->getContainer()->get('my.LdataSql')->insertData(
    array(
      'analyseid' => $id,
      'MediaId' => $this->GetObjStr($postObj, 'MediaId'),
      'ThumbMediaId' => $this->GetObjStr($postObj, 'ThumbMediaId'),
    ), 'request_video');
  }

  protected function request_voice($postObj, $id){
    $this->getApplication()->getKernel()->getContainer()->get('my.LdataSql')->insertData(
    array(
      'analyseid' => $id,
      'MediaId' => $this->GetObjStr($postObj, 'MediaId'),
      'Format' => $this->GetObjStr($postObj, 'Format'),
    ), 'request_voice');
  }

}
