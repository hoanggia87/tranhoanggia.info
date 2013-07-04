<?php
class AdminPrivilegesController extends Zendvn_Controller_Action{
	
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
		
		if(empty($ssFilter->keywords)){
			$ssFilter->keywords = '';
		}
		$this->_arrParam['ssFilter']['keywords'] = $ssFilter->keywords;
        $this->_arrParam['ssFilter']['col'] 		= $ssFilter->col;
		$this->_arrParam['ssFilter']['order'] 		= $ssFilter->order;
		
		//set lai pagging
		if(!empty($ssFilter->pagging)){
			$this->_paginator['itemCountPerPage']=$ssFilter->pagging;
			$this->_arrParam['ssFilter']['pagging']=$ssFilter->pagging;
		}
		//phan trang		
		$this->_paginator['currentPage'] = $this->_request->getParam('page',1);
		$this->_arrParam['paginator'] = $this->_paginator;
		/*echo '<pre>';
			print_r($this->_paginator);
		echo '</pre>';*/
		//Truyen ra view
		$this->view->arrParam = $this->_arrParam;
		$this->view->currentController = $this->_currentController;
		$this->view->actionMain = $this->_actionMain;
		
		$template_path = TEMPLATE_PATH . "/admin/system";
		$this->loadTemplate($template_path,'template.ini','template');
		
		
	}
	
	public function indexAction(){
		
		
		$this->view->Title = 'Member :: Privileges manager :: List';
		$this->view->headTitle($this->view->Title,true);
		
		$tblPrivileges = new Default_Model_Privileges();
		$this->view->Items = $tblPrivileges->listItem($this->_arrParam, array('task'=>'admin-list'));
		$totalItem  = $tblPrivileges->countItem($this->_arrParam);
		
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
		if($this->_arrParam['type'] == 'pagging'){
			$ssFilter->pagging = $this->_arrParam['pagging'];
			
		}
        if($this->_arrParam['type'] == 'order'){
			$ssFilter->col = $this->_arrParam['col'];
			$ssFilter->order = $this->_arrParam['by'];
		}
	/*	echo '<pre>';
		print_r($ssFilter->getIterator());
		echo '</pre>';
		echo '<pre>';
		print_r($this->_arrParam);
		echo '</pre>';*/
		$this->_redirect($this->_actionMain);
		$this->_helper->viewRenderer->setNoRender();
	}
	
	public function addAction(){
		$this->view->Title = 'Member :: Privileges manager :: Add new';
		$this->view->headTitle($this->view->Title,true);
		
		if($this->_request->isPost()){
			$tblPrivileges = new Default_Model_Privileges();
			$tblPrivileges->saveItem($this->_arrParam,array('task'=>'admin-add'));
			$this->_redirect($this->_actionMain);
		}
		
	}
	
	public function infoAction(){
		$this->view->Title = 'Member :: Privileges manager :: Information';
		$this->view->headTitle($this->view->Title,true);
		$tblPrivileges = new Default_Model_Privileges();
		$this->view->Item = $tblPrivileges->getItem($this->_arrParam,array('task'=>'admin-info'));
		
	}
	
	public function editAction(){
		$this->view->Title = 'Member :: Privileges manager :: Edit';
		$this->view->headTitle($this->view->Title,true);
		$tblPrivileges = new Default_Model_Privileges();
		$this->view->Item = $tblPrivileges->getItem($this->_arrParam,array('task'=>'admin-edit'));
		
		if($this->_request->isPost()){
			$tblPrivileges = new Default_Model_Privileges();
			$tblPrivileges->saveItem($this->_arrParam,array('task'=>'admin-edit'));
			$this->_redirect($this->_actionMain);
		}
	}

	public function deleteAction(){
		$this->view->Title = 'Member :: Privileges manager :: Delete';
		$this->view->headTitle($this->view->Title,true);
		if($this->_request->isPost()){
			$tblPrivileges = new Default_Model_Privileges();
			$tblPrivileges->deleteItem($this->_arrParam,array('task'=>'admin-delete'));
			$this->_redirect($this->_actionMain);
		}
	}

	public function statusAction(){
		$tblPrivileges = new Default_Model_Privileges();		
		$tblPrivileges->changeStatus($this->_arrParam);
		$this->_redirect($this->_actionMain.'/page/'.$this->_arrParam['page']);		
		$this->_helper->viewRenderer->setNoRender();
	}
	
	public function multiDeleteAction(){
		
		if($this->_request->isPost()){
			$tblPrivileges = new Default_Model_Privileges();
			$tblPrivileges->deleteItem($this->_arrParam,array('task'=>'admin-multi-delete'));
			$this->_redirect($this->_actionMain);
		}
		$this->_helper->viewRenderer->setNoRender();
	}
	
	public function sortAction(){
		if($this->_request->isPost()){
			$tblPrivileges = new Default_Model_Privileges();
			$tblPrivileges->sortItem($this->_arrParam);
			$this->_redirect($this->_actionMain);
		}
		
		$this->_helper->viewRenderer->setNoRender();
	}
}




