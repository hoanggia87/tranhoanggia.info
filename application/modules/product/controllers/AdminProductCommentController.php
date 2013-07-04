<?php
class Product_AdminProductCommentController extends Zendvn_Controller_Action{
	
	
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
			$ssFilter->col 			= 'ncm.id';
			$ssFilter->order 		= 'DESC';
			
		}
		$this->_arrParam['ssFilter']['keywords'] 	= $ssFilter->keywords;
		$this->_arrParam['ssFilter']['col'] 		= $ssFilter->col;
		$this->_arrParam['ssFilter']['order'] 		= $ssFilter->order;
		$this->_arrParam['ssFilter']['item_id'] 	= $ssFilter->item_id;
				
		//set lai pagging
		$this->_paginator['itemCountPerPage']=$ssFilter->pagging;
		$this->_arrParam['ssFilter']['pagging']=$ssFilter->pagging;
		
		//phan trang		
		$this->_paginator['currentPage'] = $this->_request->getParam('page',1);
		$this->_arrParam['paginator'] = $this->_paginator;
		
		
		//Truyen ra view
		$this->view->arrParam = $this->_arrParam;
		$this->view->currentController = $this->_currentController;
		$this->view->actionMain = $this->_actionMain;
		
		//load template
		$template_path = TEMPLATE_PATH . "/admin/system";
		$this->loadTemplate($template_path,'template.ini','template');
		
		//khai bao thu muc upload useravatar	
		$this->_controllerConfig  = array('imagesDir'=> PUBLIC_PATH.'/files/Product/');		
		$this->_arrParam['controllerConfig'] = $this->_controllerConfig;
	}

	public function indexAction(){
		
		$this->view->Title = 'Product :: Comment manager :: List';
		$this->view->headTitle($this->view->Title,true);
		
		$ssFilter = new Zend_Session_Namespace($this->_namespace);
		if(isset($this->_arrParam['item_id']))
			$ssFilter->item_id = $this->_arrParam['item_id'];
		else		
			if(empty($ssFilter->item_id))
				$ssFilter->item_id = 0;
		$this->_arrParam['ssFilter']['item_id'] 	= $ssFilter->item_id;
		
		
		
		$tblProduct = new Product_Model_Productcomment();
		$this->view->Items = $tblProduct->listItem($this->_arrParam, array('task'=>'admin-list'));
				
		$totalItem  = $tblProduct->countItem($this->_arrParam);
		
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
		
		if($this->_arrParam['type'] == 'category'){
			$ssFilter->cat_id 		= $this->_arrParam['cat_id'];				
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
		if($this->_arrParam['type'] == 'order'){
			$ssFilter->col = $this->_arrParam['col'];
			$ssFilter->order = $this->_arrParam['by'];
		}
		if($this->_arrParam['type'] == 'pagging'){
			$ssFilter->pagging = $this->_arrParam['pagging'];			
		}
		if($this->_arrParam['type'] == 'item_id'){
			$ssFilter->item_id = $this->_arrParam['item_id'];
		}
		
		$this->_redirect($this->_actionMain);
		$this->_helper->viewRenderer->setNoRender();
	}
	
	
	public function addAction(){
		
		$this->view->Title = 'Product :: Product manager :: Add';
		$this->view->headTitle($this->view->Title,true);
						
		//get listCat parent
		$tblCategory 		= new Product_Model_Productcomment();		
		$this->view->listCat= $tblCategory->listItem($this->_arrParam,array('task'=>'admin-list-select','root'=>'false'));
			
		if($this->_request->isPost()){
			//$imgc=$this->_arrParam['image_current'];
			$validate = new Product_Form_ProductValidate($this->_arrParam,'add');
			if($validate->checkError() == true){
				//Hien thi thong bao loi
				$this->view->errors = $validate->getMessageError();
				$this->view->Item = $validate->getData();
			}else{
				//Luu vao database
				$tblProduct 		= new Product_Model_Productcomment();	
				$rs				= $validate->getData();			
				$tblProduct->saveItem($rs,array('task'=>'admin-add'));
				$this->view->errors = array('success');
				
			}			
			
		}
	}
	
	public function infoAction(){
		$this->view->Title = 'Product :: Product manager :: Information';
		$this->view->headTitle($this->view->Title,true);
		
		$tblProduct = new Product_Model_Productcomment();
		$this->view->Item = $tblProduct->getItem($this->_arrParam,array('task'=>'admin-info'));
		
	}
	

	public function deleteAction(){
		$this->view->Title = 'Product :: Comment manager :: Delete';
		$this->view->headTitle($this->view->Title,true);
		if($this->_request->isPost()){
			$tblProductComment = new Product_Model_Productcomment();
			$tblProductComment->deleteItem($this->_arrParam,array('task'=>'admin-delete'));
			$this->_redirect($this->_actionMain);
		}
	}

	public function statusAction(){
		$tblProductComment = new Product_Model_Productcomment();		
		$tblProductComment->changeStatus($this->_arrParam);
		$this->_redirect($this->_actionMain . '/page/'.$this->_request->getParam('page',1));		
		$this->_helper->viewRenderer->setNoRender();
	}
	
	public function multiDeleteAction(){
		
		if($this->_request->isPost()){
			$tblProductComment = new Product_Model_Productcomment();
			$tblProductComment->deleteItem($this->_arrParam,array('task'=>'admin-multi-delete'));
			$this->_redirect($this->_actionMain);
		}
		$this->_helper->viewRenderer->setNoRender();
	}
	
	public function sortAction(){
		if($this->_request->isPost()){
			$tblUser = new Product_Model_Productcomment();
			$tblUser->sortItem($this->_arrParam);
			$this->_redirect($this->_actionMain);
		}
		
		$this->_helper->viewRenderer->setNoRender();
	}
	

	
}




