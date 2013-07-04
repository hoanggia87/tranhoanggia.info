<?php
class IndexController extends Zendvn_Controller_Action {

	//Mang tham so nhan duoc o moi Action
	protected $_arrParam;
	
	//Duong dan cua Controller
	protected $_currentController;
	
	//Duong dan cua Action chinh
	protected $_actionMain;
	
	//Thong so phan trang
	protected $_paginator = array(
									'itemCountPerPage' => 10,
									'pageRange' => 3,
								  );
	protected $_controllerConfig; 
	
	protected $_namespace;
	protected $_objCaptcha;
	
	public function init(){
		//parent::init();
	
		//Mang tham so nhan duoc o moi Action
		$this->_arrParam = $this->_request->getParams();
		$this->_objCaptcha = new Zendvn_Captcha_Image();
		//Duong dan cua Controller
		$this->_currentController = '/' . $this->_arrParam['module'] 
									 . '/' . $this->_arrParam['controller'];
		
		//Duong dan cua Action chinh		
		$this->_actionMain = '/' . $this->_arrParam['module'] 
							 . '/' . $this->_arrParam['controller']	. '/index';
		//$template_path = TEMPLATE_PATH . "/public/gac";
		//$this->loadTemplate($template_path,'template.ini','template');
		
		//Truyen ra view
		$this->view->arrParam = $this->_arrParam;
		$this->view->currentController = $this->_currentController;
		$this->view->actionMain = $this->_actionMain;
		
		//phan trang
		$this->_paginator['currentPage'] = $this->_request->getParam('page',1);
		$this->_arrParam['paginator'] = $this->_paginator;
	}
		
	public function indexAction() 
    {	
       
	                
	}
	
		
}
