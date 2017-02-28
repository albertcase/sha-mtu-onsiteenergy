<?php

namespace Wechat\ApiBundle\Modals\Database;

class RedisLogic
{
    private $redis;
    private $prostr;

    public function __construct($container){
    	$this->redis = $container->get('php.redis');
      $this->prostr = $container->getParameter('redis_prostr');
    }

    public function setString($key ,$val ,$exp = 0){
    	$this->redis->set($this->prostr.$key ,$val);
    	if($exp >0 )
    	    $this->redis->setTimeout($this->prostr.$key ,$exp);
    	return true;
    }

    public function getString($key){
	     return $this->redis->get($this->prostr.$key);
    }

    public function checkString($key){
	     return $this->redis->exists($this->prostr.$key);
    }

    public function setLList($key ,$val ,$exp = 0){
    	$this->redis->lPush($this->prostr.$key ,$val);
    	if($exp > 0)
    	    $this->redis->setTimeout($this->prostr.$key ,$exp);
    	return true;
    }

    public function setLLists($key ,$vals,$exp = 0){
    	foreach($vals as $x){
    	    $this->setLList($key ,$x);
    	}
    	if($exp > 0)
    	    $this->redis->setTimeout($this->prostr.$key ,$exp);
    	return true;
    }


    public function setRList($key ,$val ,$exp = 0){
    	$this->redis->rPush($this->prostr.$key ,$val);
    	if($exp > 0)
    	    $this->redis->setTimeout($this->prostr.$key ,$exp);
    	return true;
    }

    public function setRLists($key ,$vals ,$exp = 0){
    	foreach($vals as $x){
    	    $this->setRList($key , $x);
    	}
    	if($exp > 0)
    	    $this->redis->setTimeout($this->prostr.$key ,$exp);
    	return true;
    }

    public function popLList($key){
	     return $this->redis->lPop($this->prostr.$key);
    }

    public function popRList($key){
	     return $this->redis->rPop($this->prostr.$key);
    }

    public function setSet($key ,$val ,$exp = 0){
    	$result = $this->redis->sAdd($this->prostr.$key ,$val);
    	if($exp > 0)
    	    $this->redis->setTimeout($this->prostr.$key ,$exp);
    	return $result;
    }

    public function setSets($key ,$vals ,$exp = 0){
    	foreach($vals as $x){
    	    $this->setSet($key ,$x);
    	}
    	if($exp > 0)
    	    $this->redis->setTimeout($this->prostr.$key ,$exp);
    	return true;
    }

    public function checkSet($key ,$val){
	     return $this->redis->sIsMember($this->prostr.$key ,$val);
    }

    public function delSet($key ,$val){
	     return $this->redis->sRem($this->prostr.$key ,$val);
    }

    public function getallSet($key){
	     return $this->redis->sMembers($this->prostr.$key);
    }

    public function setzSet($key , $sort, $val ,$exp = 0){
    	$result = $this->redis->zAdd($this->prostr.$key , $sort, $val);
    	if($exp > 0)
    	    $this->redis->setTimeout($this->prostr.$key ,$exp);
    	return $result;
    }

    public function setszSet($key , $vals ,$exp = 0){
    	foreach( $vals as $x => $x_val ){
    	    $this->setzSet($key , $x, $x_val);
    	}
    	if($exp > 0)
    	    $this->redis->setTimeout($this->prostr.$key ,$exp);
    	return true;
    }

    public function getzSet($key ,$start ,$end ,$sort = false){
	     return $this->redis->zRange($key ,$start ,$end ,$sort);
    }

    public function getzSetlen($key){
	     return $this->redis->zSize($key);
    }

    public function delzSet($key ,$val){
	     return $this->redis->zDelete($this->prostr.$key ,$val);
    }

    public function delszSet($key ,$start ,$end ,$sort = false){
	     return $this->redis->zRevRange($this->prostr.$key ,$start ,$end ,$sort);
    }

    public function rerankzSet($key ,$val){
	     return $this->redis->zRank($key ,$val);
    }

/////////////////////////////////////////////////////////
////////////////////// HASH /////////////////////////////
/////////////////////////////////////////////////////////

    public function sethSet($h ,$key ,$val ,$exp = 0){
    	$result = $this->redis->hSet($this->prostr.$h ,$key ,$val);
    	if($exp > 0 ){
    	    $this->redis->setTimeout($this->prostr.$h ,$exp);
    	}
    	 return $result;
    }

    public function setshSet($h ,$datas){
    	foreach($datas as $x => $x_val){
    	    $this->sethSet($this->prostr.$h ,$x ,$x_val);
    	}
    	return true;
    }

    public function gethSetlen($h){//get hSet长度
	     return $this->redis->hLen($this->prostr.$h);
    }

    public function gethSet($h ,$key){
	     return $this->redis->hGet($this->prostr.$h ,$key);
    }

    public function gethSets($h,$keys){
    	$out = array();
    	foreach($keys as $x){
    	    if($this->checkhSet($h ,$x))
    		$out[$x] = $this->gethSet($h,$x);
    	}
    	return $out;
    }

    public function checkhSet($h ,$key){
	     return $this->redis->hExists($this->prostr.$h ,$key);
    }

    public function gethSetkeys($h){
	     return $this->redis->hKeys($this->prostr.$h);
    }

    public function gethSetvals($h){
	     return $this->redis->hVals($this->prostr.$h);
    }

    public function gethSetall($h){
	     return $this->redis->hGetAll($this->prostr.$h);
    }

    public function delhSet($h ,$key){
	     return $this->redis->hDel($this->prostr.$h ,$key);
    }

    public function setKeyexpir($key ,$exp){
	     return $this->redis->setTimeout($this->prostr.$key ,$exp);
    }

    public function delkey($key){
	     return $this->redis->delete($this->prostr.$key);
    }

    public function checkexists($key){
	     return $this->redis->exists($this->prostr.$key);
    }

}
