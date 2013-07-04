<?php
class AdminFileLogController extends Zendvn_Controller_Action{
	
	
	//Mang tham so nhan duoc o moi Action
	protected $_arrParam;
	
	//Duong dan cua Controller
	protected $_currentController;
	
	//Duong dan cua Action chinh
	protected $_actionMain;
	
	//Thong so phan trang
	protected $_paginator = array(
									'itemCountPerPage' => 20,
									'pageRange' => 3,
								  ); 
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
		//$ssFilter->unsetAll();
		if(empty($ssFilter->col)){
			$ssFilter->keywords 	= '';
			$ssFilter->col 			= 'n.id';
			$ssFilter->order 		= 'DESC';
			
		}
		$this->_arrParam['ssFilter']['keywords'] 	= $ssFilter->keywords;
		$this->_arrParam['ssFilter']['col'] 		= $ssFilter->col;
		$this->_arrParam['ssFilter']['order'] 		= $ssFilter->order;						
		$this->_arrParam['ssFilter']['date_from'] 	= $ssFilter->date_from;
		$this->_arrParam['ssFilter']['date_to'] 	= $ssFilter->date_to;
		
		//set lai pagging
		if(!empty($ssFilter->pagging)){
			$this->_paginator['itemCountPerPage']=$ssFilter->pagging;
			$this->_arrParam['ssFilter']['pagging']=$ssFilter->pagging;
		}
		
		//phan trang		
		$this->_paginator['currentPage'] = $this->_request->getParam('page',1);
		$this->_arrParam['paginator'] = $this->_paginator;
		
		
		//Truyen ra view
		$this->view->arrParam = $this->_arrParam;
		$this->view->currentController = $this->_currentController;
		$this->view->actionMain = $this->_actionMain;
		
		$template_path = TEMPLATE_PATH . "/admin/system";
		$this->loadTemplate($template_path,'template.ini','template');
		
		
	}

	public function indexAction(){
		
		$this->view->Title = 'FileLog :: FileLog manager :: List';
		$this->view->headTitle($this->view->Title,true);
		
		$tblFileLog = new Default_Model_Filelog();
		$this->view->Items = $tblFileLog->listItem($this->_arrParam, array('task'=>'admin-list'));
				
		$totalItem  = $tblFileLog->countItem($this->_arrParam);
		
		$paginator = new Zendvn_Paginator();
		$this->view->panigator = $paginator->createPaginator($totalItem,$this->_paginator);
		
	}
	
	public function filterAction(){
		$ssFilter = new Zend_Session_Namespace($this->_namespace);
		
		if($this->_arrParam['type'] == 'search'){
			if($this->_arrParam['key'] == 1){
				$ssFilter->keywords = trim($this->_arrParam['keywords']);
			}else{
				$ssFilter->keywords = '';
			}
		}
		
		if($this->_arrParam['type'] == 'order'){
			$ssFilter->col = $this->_arrParam['col'];
			$ssFilter->order = $this->_arrParam['by'];
		}
		
		if($this->_arrParam['type'] == 'date'){			
				
			if($this->_arrParam['key'] == 1){
				$ssFilter->date_from 	= $this->_arrParam['date_from'];
				$ssFilter->date_to 		= $this->_arrParam['date_to'];	
			}else{
				$ssFilter->date_from 	= '';
				$ssFilter->date_to 		= '';
			}	
		}
		
		if($this->_arrParam['type'] == 'pagging'){
			$ssFilter->pagging = $this->_arrParam['pagging'];
			
		}
		$this->_redirect($this->_actionMain);
		$this->_helper->viewRenderer->setNoRender();
	}
	
	
	public function infoAction(){
		$this->view->Title = 'FileLog :: FileLog manager :: Information';
		$this->view->headTitle($this->view->Title,true);
		
		$tblFileLog = new Default_Model_Filelog();
		$this->view->Item = $tblFileLog->getItem($this->_arrParam,array('task'=>'admin-info'));
		
	}
	
	public function deleteAction(){
		$this->view->Title = 'FileLog :: FileLog manager :: Delete';
		$this->view->headTitle($this->view->Title,true);
		if($this->_request->isPost()){
			$tblFileLog = new Default_Model_Filelog();
			//$tblFileLog->deleteItem($this->_arrParam,array('task'=>'admin-delete'));
			$this->_redirect($this->_actionMain);
		}
	}

	public function statusAction(){
		$tblFileLog = new Default_Model_Filelog();		
		$tblFileLog->changeStatus($this->_arrParam);
		$this->_redirect($this->_actionMain . '/page/'.$this->_request->getParam('page',1));	
		$this->_helper->viewRenderer->setNoRender();
	}
	
	public function multiDeleteAction(){
		
		if($this->_request->isPost()){
			$tblFileLog = new Default_Model_Filelog();
			$tblFileLog->deleteItem($this->_arrParam,array('task'=>'admin-multi-delete'));
			$this->_redirect($this->_actionMain);
		}
		$this->_helper->viewRenderer->setNoRender();
	}
	
	public function sortAction(){
		if($this->_request->isPost()){
			$tblUser = new Default_Model_Filelog();
			$tblUser->sortItem($this->_arrParam);
			$this->_redirect($this->_actionMain);
		}
		
		$this->_helper->viewRenderer->setNoRender();
	}
	

	
}




