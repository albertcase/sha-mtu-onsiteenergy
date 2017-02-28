<?php
namespace Wechat\ApiBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;

class RenewTokenCommand extends Command{

  protected function configure(){
    $this->setName('wechat:renew.token')
        ->setDescription('Renew Token');
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $this->getApplication()->getKernel()->getContainer()->get('my.Wechat')->WechatKey_access_token(true);
    $output->writeln('<info>renew token success</info>');
  }

}
