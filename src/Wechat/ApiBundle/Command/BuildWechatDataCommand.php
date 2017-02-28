<?php
namespace Wechat\ApiBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Filesystem\Filesystem;

class BuildWechatDataCommand extends Command{

  protected function configure(){
    $this->setName('wechat:renew.wechatdata')
        ->setDescription('Update Wechat Data. path:web/upload/wechatcache')
        ->addArgument('path',
          InputArgument::OPTIONAL,
          'cache file path'
        );
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $fs = new Filesystem();
    $path = dirname(__FILE__).'/../../../../web/upload/wechatcache';
    if($in_path = $input->getArgument('path'))
      $path = $in_path;
    $path = realpath($path);
    if(!$fs->exists($path));
      $fs->mkdir($path);
    $_db = $this->getApplication()->getKernel()->getContainer()->get('my.LdataSql');
    $lsa = $_db->searchData(array() ,array(), 'wechat_feedbacks');
    $lsb = $_db->searchData(array() ,array(), 'wechat_events');
    $feedbacks = array();
    $keywords = array();
    $events = array();
    foreach ($lsa as $value) {
      if($value['menuId'])
        $feedbacks[$value['menuId']] = array(
          'menuId' => $value['menuId'],
          'MsgType' => $value['MsgType'],
          'MsgData' => $value['MsgData'],
        );
    }
    // print_r($list);
    ob_start();
    print "<?php\nreturn ";
    var_export($feedbacks);
    $string = ob_get_contents();
    ob_end_clean();
    $fs->dumpFile($path."/feedbacks.php", $string.";");

    foreach ($lsb as $value) {
      if($value['getContent'])
        $keywords[$value['getContent']] = array(
          'menuId' => $value['menuId'],
          'MsgType' => $value['MsgType']
        );
    }
    ob_start();
    print "<?php\nreturn ";
    var_export($keywords);
    $string = ob_get_contents();
    ob_end_clean();
    $fs->dumpFile($path."/keywords.php", $string.";");

    foreach ($lsb as $value) {
      if($value['getEventKey'])
        $events[$value['getEventKey']] = array(
          'menuId' => $value['menuId'],
          'MsgType' => $value['MsgType']
        );
      if($value['getEvent'] && ($value['getEvent'] == 'subscribe' || $value['getEvent'] == 'defaultback'))
        $events[$value['getEvent']] = array(
          'menuId' => $value['menuId'],
          'MsgType' => $value['MsgType']
        );
    }
    ob_start();
    print "<?php\nreturn ";
    var_export($events);
    $string = ob_get_contents();
    ob_end_clean();
    $fs->dumpFile($path."/events.php", $string.";");

    $lsc = $_db->searchData(array() ,array(), 'wechat_qrcode');
    $qrcode = array();
    foreach ($lsc as $value) {
      if($value['qrTicket'])
        $qrcode[$value['qrTicket']] = array(
          'feedbackid' => $value['feedbackid']
        );
    }
    ob_start();
    print "<?php\nreturn ";
    var_export($qrcode);
    $string = ob_get_contents();
    ob_end_clean();
    $fs->dumpFile($path."/qrcodes.php", $string.";");
// lbs
    $lsd = $_db->searchData(array() ,array(), 'stores');
    $stores = array();
      if($lsd)
        $stores = $lsd;
    ob_start();
    print "<?php\nreturn ";
    var_export($stores);
    $string = ob_get_contents();
    ob_end_clean();
    $fs->dumpFile($path."/stores.php", $string.";");
// jssdkids
    $list = $this->getApplication()->getKernel()->getContainer()->get('my.dataSql')->searchData(array(), array(), 'wechat_jssdk');
$parm = array();
if($list && isset($list['0'])){
  foreach($list as $l){
    $parm[$l['jsfilename']] = $l;
  }
}
$parm['60c4349e-c302-4313-9fa8-37a8ebd59853'] = array(
  'jsfilename' => '60c4349e-c302-4313-9fa8-37a8ebd59853',
  'domain' => $this->getApplication()->getKernel()->getContainer()->getParameter('hostdomain'),
  'jscontent' => '',
  'name' => 'host'
);
    ob_start();
    print "<?php\nreturn ";
    var_export($parm);
    $string = ob_get_contents();
    ob_end_clean();
    $fs->dumpFile($path."/jssdkids.php", $string.";");
    $output->writeln('<info>update wechat data success, Path:'.$path.'</info>');
  }

}
