<?php
namespace Wechat\ApiBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;

class GetWechatUsersCommand extends Command{

  protected function configure(){
    $this->setName('wechat:retrieve.openidlist')
        ->setDescription('Insert all online openid')
        ->addArgument('next_openid',
          InputArgument::OPTIONAL,
          'the start openid'
        );
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $next_openid = $input->getArgument('next_openid');
    $this->openidList($next_openid);
    $output->writeln('<info>update openid list success</info>');
  }

  protected function openidList($next_openid){
      if(!$next_openid)
        $next_openid = '';
      $wechat = $this->getApplication()->getKernel()->getContainer()->get('my.Wechat');
      $list = $wechat->getOpenidlist($next_openid);
      if($list['code'] == '10'){
        if(isset($list['info']['data']) && isset($list['info']['data']['openid']) && $list['info']['data']['openid'])
          $this->updateList($list['info']['data']['openid']);
        if(isset($list['info']['count']) && $list['info']['count'] >= 10000)
          $this->openidList($list['info']['next_openid']);
      }else{
        return print_r($list);
      }
  }

  public function updateList($list){
    if(!is_array($list))
      return false;
    $sql = $this->getApplication()->getKernel()->getContainer()->get('my.LdataSql');
    foreach($list as $x){
      if(!$sql->searchData(array('openid' => $x) , array('id'), 'wechat_users'))
        $sql->insertData(array('openid' => $x), 'wechat_users');
    }
  }

}
