<?php
namespace Wechat\ApiBundle\Modals\Database;

use \Redis;

class RedisCache extends Redis{

  public function set_prefix($value = ''){
    $this->setOption(Redis::OPT_PREFIX, $value);
  }
}
