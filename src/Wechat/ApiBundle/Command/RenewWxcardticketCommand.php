<?php
namespace Wechat\ApiBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;

class RenewWxcardticketCommand extends Command{

  protected function configure(){
    $this->setName('wechat:renew.wxcardticket')
        ->setDescription('Renew WxCard Ticket');
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $this->getApplication()->getKernel()->getContainer()->get('my.Wechat')->WechatKey_access_wxcard_ticket(true);
    $output->writeln('<info>renew WxCard ticket success</info>');
  }

}
