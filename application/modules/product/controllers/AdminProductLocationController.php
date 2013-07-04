<?php
class Product_AdminProductLocationController extends Zendvn_Controller_Action{
	
	
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
		
		
	}

	public function indexAction(){
		
		$this->view->Title = 'Product :: Location manager :: List';
		$this->view->headTitle($this->view->Title,true);
		
		$tblLocation = new Product_Model_Productlocation();
		$this->view->Items = $tblLocation->listItem($this->_arrParam, array('task'=>'admin-list'));
				
		$totalItem  = $tblLocation->countItem($this->_arrParam);
		
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
		
		$this->view->Title = 'Product :: Location manager :: Add';
		$this->view->headTitle($this->view->Title,true);				
		
		//get listCat parent
		$tblLocation 		= new Product_Model_Productlocation();		
		$this->view->listCat= $tblLocation->listItem($this->_arrParam,array('task'=>'admin-list-select','root'=>'true'));
		
		if($this->_request->isPost()){
			
			$validate = new Product_Form_ProductLocationValidate($this->_arrParam);
			if($validate->checkError() == true){
				//Hien thi thong bao loi
				$this->view->errors = $validate->getMessageError();
				$this->view->Item = $validate->getData();
			}else{
				//Luu vao database
				
				/*echo '<pre>';
					print_r($validate->getData());
				echo '</pre>';*/
				$tblLocation->saveItem($validate->getData(),array('task'=>'admin-add'));
				//cap nhat lai Location
				$this->view->listCat= $tblLocation->listItem($this->_arrParam,array('task'=>'admin-list-select','root'=>'true'));
				
				$this->view->errors = array('success');
				
			}		
			
		}
		
	}
	
	public function infoAction(){
		$this->view->Title = 'Product :: Location manager :: Information';
		$this->view->headTitle($this->view->Title,true);
		$tblLocation = new Product_Model_Productlocation();
		$this->view->Item = $tblLocation->getItem($this->_arrParam,array('task'=>'admin-info'));
		
	}
	
	public function editAction(){
		$this->view->Title = 'Product :: Location manager :: Edit';
		$this->view->headTitle($this->view->Title,true);				
		
		//get item info
		$tblLocation 		= new Product_Model_Productlocation();	
		$this->view->Item 	= $tblLocation->getItem($this->_arrParam,array('task'=>'admin-edit'));
		$this->view->listCat= $tblLocation->listItem($this->_arrParam,array('task'=>'admin-list-select','root'=>'true'));
		
		if($this->_request->isPost()){
			
			$validate = new Product_Form_ProductCategoryValidate($this->_arrParam);
			if($validate->checkError() == true){
				//Hien thi thong bao loi
				$this->view->errors = $validate->getMessageError();
				$this->view->Item = $validate->getData();
			}else{
				//Luu vao database
				
				/*echo '<pre>';
					print_r($validate->getData());
				echo '</pre>';*/
				$tblLocation->saveItem($validate->getData(),array('task'=>'admin-edit'));
				//cap nhat lai category
				$this->view->listCat= $tblLocation->listItem($this->_arrParam,array('task'=>'admin-list-select','root'=>'true'));
				//cap nhat lai gia tri cua item
				$this->view->Item=$this->_arrParam;
				$this->view->errors = array('success');
				
			}		
			
		}
	}

	public function deleteAction(){
		$this->view->Title = 'Product :: Location manager :: Delete';
		$this->view->headTitle($this->view->Title,true);
		if($this->_request->isPost()){
			$tblLocation = new Product_Model_Productlocation();
			$tblLocation->deleteItem($this->_arrParam,array('task'=>'admin-delete'));
			$this->_redirect($this->_actionMain);
		}
	}

	public function statusAction(){
		$tblLocation = new Product_Model_Productlocation();		
		$tblLocation->changeStatus($this->_arrParam);
		$this->_redirect($this->_actionMain);		
		$this->_helper->viewRenderer->setNoRender();
	}
	
	public function multiDeleteAction(){
		
		if($this->_request->isPost()){
			$tblLocation = new Product_Model_Productlocation();
			$tblLocation->deleteItem($this->_arrParam,array('task'=>'admin-multi-delete'));
			$this->_redirect($this->_actionMain);
		}
		$this->_helper->viewRenderer->setNoRender();
	}
	
	public function sortAction(){
		if($this->_request->isPost()){
			$tblLocation = new Product_Model_Productlocation();
			$tblLocation->sortItem($this->_arrParam);
			$this->_redirect($this->_actionMain);
		}
		
		$this->_helper->viewRenderer->setNoRender();
	}
	public function testAction(){
		
	}

	
}




