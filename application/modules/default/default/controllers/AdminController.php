<?php
class AdminController extends Zendvn_Controller_Action{
	
	//Mang tham so nhan duoc o moi Action
	protected $_arrParam;
	
	//Duong dan cua Controller
	protected $_currentController;
	
	//Duong dan cua Action chinh
	protected $_actionMain;
	
	
	protected $_namespace;
	public function init(){
		//Mang tham so nhan duoc o moi Action
		$this->_arrParam = $this->_request->getParams();
		
		//Duong dan cua Controller
		$this->_currentController = '/' . $this->_arrParam['module'] 
									 . '/' . $this->_arrParam['controller'];
		
		//Duong dan cua Action chinh		
		$this->_actionMain = '/' . $this->_arrParam['module'] 
							 . '/' . $this->_arrParam['controller']	. '/index';	

							 
		
		
		//Luu cac du lieu filter vao SESSION
		//Dat ten SESSION
		$this->_namespace = $this->_arrParam['module'] . '-' . $this->_arrParam['controller'];
		$ssFilter = new Zend_Session_Namespace($this->_namespace);
		
		if(empty($ssFilter->keywords)){
			$ssFilter->keywords = '';
		}
		$this->_arrParam['ssFilter']['keywords'] = $ssFilter->keywords;
		//set lai pagging
		$this->_paginator['itemCountPerPage']=$ssFilter->pagging;
		$this->_arrParam['ssFilter']['pagging']=$ssFilter->pagging;
	
		//Truyen ra view
		$this->view->arrParam = $this->_arrParam;
		$this->view->currentController = $this->_currentController;
		$this->view->actionMain = $this->_actionMain;

	}
	
	public function indexAction(){
		$this->view->headScript()->appendFile($this->view->jsUrl . '/joomla.javascript.js','text/javascript');
		$this->view->headScript()->appendFile($this->view->jsUrl . '/mootools.js','text/javascript');
		$this->view->headScript()->appendFile($this->view->jsUrl . '/menutool.js','text/javascript');
	}
	public function loginAction(){
		//load template login
		$template_path = TEMPLATE_PATH . "/admin/system";
		$sectionConfig = 'adminlogin';		
		$fileConfig='template.ini';
		$this->loadTemplate($template_path,$fileConfig, $sectionConfig);
	
		
		if($this->_request->isPost()){			
			//kiem tra tai khoan				
			$auth  = new Zendvn_Auth_Process($this->_arrParam['user_name'],$this->_arrParam['password']);
		
			if($auth->login() == true)							
				$this->_redirect($this->_actionMain);			
	        else{
	        	$this->view->error =  "Tài khoản hoặc mật khẩu không đúng";
	        }
	        	
	            
        }		
		
	}
	public function logoutAction() {
	    $auth = Zend_Auth::getInstance();
	    $auth->clearIdentity();	    	    
	    $this->_redirect($this->_actionMain);
	}
}