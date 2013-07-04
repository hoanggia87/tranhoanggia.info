<?php
class Zendvn_Cache{
	
	public function getCacheObject($name){
		$cacheManager=Zend_Registry::get('cacheManager',$cache);
		$cacheManager=$cacheManager[$name];
		return Zend_Cache::factory($cacheManager['frontend']['name'],
									 $cacheManager['backend']['name'],
									 $cacheManager['frontend']['options'],
									 $cacheManager['backend']['options']);
									 
	}
}