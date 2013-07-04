<?php
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {
	
	protected function _initSession(){
		Zend_Session::start();
	}
	protected function _initLogger() {		
		$writer = new Zend_Log_Writer_Stream(FILES_PATH."/logs/application.log");
		$format = '%timestamp% %priorityName%: %message%' . PHP_EOL;
		$formatter = new Zend_Log_Formatter_Simple($format);
		$writer->setFormatter($formatter);
		$logger = new Zend_Log($writer);
		$logger->setTimestampFormat("d-M-Y H:i:s");
		Zend_Registry::set('logger', $logger);
	}

	protected function _initDb(){
		
		$optionResources = $this->getOption('resources');
		$dbOption = $optionResources['db'];
		$adapter = $dbOption['adapter'];
		$config = $dbOption['params'];
		
		$db = Zend_Db::factory($adapter,$config);
		$db->setFetchMode(Zend_Db::FETCH_ASSOC);
		$db->query("SET NAMES utf8");
		$db->query("SET CHARACTER SET utf8");
		
		Zend_Registry::set('connectDb',$db);
		
		Zend_Db_Table::setDefaultAdapter($db);
		
		return $db;
		
	}
	protected function _initCache()
	{	
			
		try{

			$cacheConfig = new Zend_Config_Ini(APPLICATION_PATH . '/configs/cache.ini','default');	   
			$cacheManager = new Zend_Cache_Manager;

			$cacheManager->setCacheTemplate('cachemanager', $cacheConfig);

			$cache = $cacheManager->getCacheTemplate('cachemanager');
		 	Zend_Registry::set('cacheManager',$cache);

		}catch(Exception $e){
			//print_r($e->getMessage());
		}						          						    
	} 
	protected function _initRoutes()
	{
		$this->bootstrap('frontController');
	    $front = $this->getResource('frontController');
		$config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/routers.ini','default');
	    //rewrite l?i url
	    $router_rewrite = new Zend_Controller_Router_Rewrite();     
	    $router = $router_rewrite->addConfig($config, 'routes');				          						
        $front->setRouter($router);
	} 
	protected function _initRequest()
	{/*
		$this->bootstrap('view');
        $view = $this->getResource('view');        
       	$serverUrl	=	$view->serverUrl();
       	$host	= parse_url($serverUrl,PHP_URL_HOST);
       	
       	if($host != 'nhannghia.themuasam.info')
       	{
       		header('Location:http://themuasam.vn');       		
       		return;       		 		    	
       	} */	  	     

       	
       	 
	}
	protected function _initFrontcontroller(){
		$front = Zend_Controller_Front::getInstance();
		$front->addModuleDirectory(APPLICATION_PATH . '/modules');
		$front->setDefaultModule('default');
		$front->registerPlugin(new Zendvn_Plugin_Permission());
		$front->registerPlugin(new Zendvn_Plugin_Uploadify());
		$front->registerPlugin(new Zendvn_Plugin_Mobile());
		$error = new Zend_Controller_Plugin_ErrorHandler(array('module'=>'default',
															   'controller'=>'public',
															   'action'=>'error',
																));
		$front->registerPlugin($error);

       $front->setControllerDirectory(array(
           'default'=>APPLICATION_PATH . '/modules/default/controllers',
           'product'=>APPLICATION_PATH . '/modules/product/controllers',
           'media'=>APPLICATION_PATH . '/modules/media/controllers',
           'article'=>APPLICATION_PATH . '/modules/article/controllers',
   	 	   'user'=>APPLICATION_PATH . '/modules/user/controllers',
   	 	   'oauth'=>APPLICATION_PATH . '/modules/oauth/controllers',
   	 	   'stats'=>APPLICATION_PATH . '/modules/stats/controllers',
           'content'=>APPLICATION_PATH . '/modules/content/controllers'
   	 	   
       ));
       	  
       
       
		return $front;
	}
}
