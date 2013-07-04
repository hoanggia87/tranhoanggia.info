<?php
class Zendvn_Plugin_Test extends Zend_Controller_Plugin_Abstract{
	
	public function routeStartup(Zend_Controller_Request_Abstract $request){
		echo '<br>1. ' . __METHOD__;
	}
	
	public function routeShutdown(Zend_Controller_Request_Abstract $request){
		echo '<br>2. ' . __METHOD__;
	}
	
	public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request){
		echo '<br>3. ' . __METHOD__;
    }
    
 	public function preDispatch(Zend_Controller_Request_Abstract $request){
 		echo '<br>4. ' . __METHOD__;
 		echo '<br>=================';
 		$arrParam = $this->_request->getParams();
 		$arrParam = $this->getRequest()->getParams();
 		echo '<pre>';
 		print_r($arrParam);
 		echo '</pre>';
 		$this->getRequest()->setActionName('index2');
 		
 		echo '<pre>';
 		print_r($this->getRequest());
 		echo '</pre>';
 		
 		
 	}
    
 	public function postDispatch(Zend_Controller_Request_Abstract $request){
 		echo '<br>5. ' . __METHOD__;
 	}
    
	public function dispatchLoopShutdown(){
		echo '<br>6. ' . __METHOD__;
	}
}