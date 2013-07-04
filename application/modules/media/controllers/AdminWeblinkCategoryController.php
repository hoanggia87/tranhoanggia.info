<?php
class Weblink_AdminWeblinkCategoryController extends Zendvn_Controller_Action{
	
	
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

							 
		$this->_paginator['currentPage'] = $this->_request->getParam('page',1);
		$this->_arrParam['paginator'] = $this->_paginator;
		
				
		//Truyen ra view
		$this->view->arrParam = $this->_arrParam;
		$this->view->currentController = $this->_currentController;
		$this->view->actionMain = $this->_actionMain;
		
		$template_path = TEMPLATE_PATH . "/admin/system";
		$this->loadTemplate($template_path,'template.ini','template');
		
		//khai bao thu muc upload useravatar	
		$this->_controllerConfig  = array('imagesDir'=> PUBLIC_PATH.'/files/news/');		
		$this->_arrParam['controllerConfig'] = $this->_controllerConfig;
	}

	public function indexAction(){
		
		$this->view->Title = 'Web\'s link :: Category manager :: List';
		$this->view->headTitle($this->view->Title,true);
		
		$tblCategory = new Weblink_Model_Weblinkcategory();
		$this->view->Items = $tblCategory->listItem($this->_arrParam, array('task'=>'admin-list'));
				
		$totalItem  = $tblCategory->countItem($this->_arrParam);
		
		//$paginator = new Zendvn_Paginator();
		//$this->view->panigator = $paginator->createPaginator($totalItem,$this->_paginator);
		/*echo '<pre>';
			print_r($this->view->Items);
		echo '</pre>';*/
		//$this->_helper->viewRenderer->setNoRender();
		
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
		
		if($this->_arrParam['type'] == 'group'){
			$ssFilter->group_id = $this->_arrParam['group_id'];
		}
		
		if($this->_arrParam['type'] == 'order'){
			$ssFilter->col = $this->_arrParam['col'];
			$ssFilter->order = $this->_arrParam['by'];
		}
	
		$this->_redirect($this->_actionMain);
		$this->_helper->viewRenderer->setNoRender();
	}
	
	
	public function addAction(){
		
	$this->view->Title = 'Web\'s link :: Category manager :: Add';
		$this->view->headTitle($this->view->Title,true);				
		
		//get listCat parent
		$tblCategory 		= new Weblink_Model_Weblinkcategory();		
		$this->view->listCat= $tblCategory->listItem($this->_arrParam,array('task'=>'admin-list-select','root'=>'true'));
		
		if($this->_request->isPost()){
			
			$validate = new Weblink_Form_WeblinkCategoryValidate($this->_arrParam);
			if($validate->checkError() == true){
				//Hien thi thong bao loi
				$this->view->errors = $validate->getMessageError();
				$this->view->Item = $validate->getData();
			}else{
				//Luu vao database
				
				/*echo '<pre>';
					print_r($validate->getData());
				echo '</pre>';*/
				$tblCategory->saveItem($validate->getData(),array('task'=>'admin-add'));
				//cap nhat lai category
				$this->view->listCat= $tblCategory->listItem($this->_arrParam,array('task'=>'admin-list-select','root'=>'true'));
				
				$this->view->errors = array('success');
				
			}		
			
		}
		
	}
	
	public function infoAction(){
		$this->view->Title = 'Web\'s link :: Category manager :: Information';
		$this->view->headTitle($this->view->Title,true);
		$tblCategory = new Weblink_Model_Weblinkcategory();
		$this->view->Item = $tblCategory->getItem($this->_arrParam,array('task'=>'admin-info'));
		
	}
	
	public function editAction(){
		$this->view->Title = 'Web\'s link :: Category manager :: Edit';
		$this->view->headTitle($this->view->Title,true);				
		
		//get item info
		$tblCategory 		= new Weblink_Model_Weblinkcategory();	
		$this->view->Item 	= $tblCategory->getItem($this->_arrParam,array('task'=>'admin-edit'));
		$this->view->listCat= $tblCategory->listItem($this->_arrParam,array('task'=>'admin-list-select','root'=>'true'));
		
		if($this->_request->isPost()){
			
			$validate = new Weblink_Form_WeblinkCategoryValidate($this->_arrParam);
			if($validate->checkError() == true){
				//Hien thi thong bao loi
				$this->view->errors = $validate->getMessageError();
				$this->view->Item = $validate->getData();
			}else{
				//Luu vao database
				
				/*echo '<pre>';
					print_r($validate->getData());
				echo '</pre>';*/
				$tblCategory->saveItem($validate->getData(),array('task'=>'admin-edit'));
				//cap nhat lai category
				$this->view->listCat= $tblCategory->listItem($this->_arrParam,array('task'=>'admin-list-select','root'=>'true'));
				
				$this->view->errors = array('success');
				$this->view->Item=$this->_arrParam;
			}		
			
		}
	}

	public function deleteAction(){
		$this->view->Title = 'Web\'s link :: Category manager :: Delete';
		$this->view->headTitle($this->view->Title,true);
		if($this->_request->isPost()){
			$tblCategory = new Weblink_Model_Weblinkcategory();
			$tblCategory->deleteItem($this->_arrParam,array('task'=>'admin-delete'));
			$this->_redirect($this->_actionMain);
		}
	}

	public function statusAction(){
		$tblCategory = new Weblink_Model_Weblinkcategory();		
		$tblCategory->changeStatus($this->_arrParam);
		$this->_redirect($this->_actionMain);		
		$this->_helper->viewRenderer->setNoRender();
	}
	
	public function multiDeleteAction(){
		
		if($this->_request->isPost()){
			$tblCategory = new Weblink_Model_Weblinkcategory();
			$tblCategory->deleteItem($this->_arrParam,array('task'=>'admin-multi-delete'));
			$this->_redirect($this->_actionMain);
		}
		$this->_helper->viewRenderer->setNoRender();
	}
	
	public function sortAction(){
		if($this->_request->isPost()){
			$tblCategory = new Weblink_Model_Weblinkcategory();
			$tblCategory->sortItem($this->_arrParam);
			$this->_redirect($this->_actionMain);
		}
		
		$this->_helper->viewRenderer->setNoRender();
	}
	public function testAction(){
		
	}

	
}




