<?php
class Service_Bootstrap extends Zend_Application_Module_Bootstrap{
	
	protected function _initRoutes()
	{				
		try{
			$this->bootstrap('frontController');
		    $front = $this->getResource('frontController');		    			   
		    $Router=$front->getRouter();	    
			$config = new Zend_Config_Ini(MODULES_PATH.'/service/configs/routers.ini','service');	   
		    $router = $Router->addConfig($config, 'routes');
		}catch(Exception $e){
			//print_r($e->getMessage());
		}						          						    
	} 
}