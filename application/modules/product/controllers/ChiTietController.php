<?php
class Product_ChiTietController extends Zendvn_Controller_Action {
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
	
	
	public function init(){
		//parent::init();
		
		//Mang tham so nhan duoc o moi Action
		$this->_arrParam = $this->_request->getParams();
		
		//Duong dan cua Controller
		$this->_currentController = '/' . $this->_arrParam['module'] 
									 . '/' . $this->_arrParam['controller'];
		
		//Duong dan cua Action chinh		
		$this->_actionMain = '/' . $this->_arrParam['module'] 
							 . '/' . $this->_arrParam['controller']	. '/index';
		$template_path = TEMPLATE_PATH . "/public/themuasam";
		$this->loadTemplate($template_path,'template.ini','template');
		
		//Truyen ra view
		$this->view->arrParam = $this->_arrParam;
		$this->view->currentController = $this->_currentController;
		$this->view->actionMain = $this->_actionMain;
		
		//phan trang
		$this->_paginator['currentPage'] = $this->_request->getParam('page',1);
		$this->_arrParam['paginator'] = $this->_paginator;
	}
		
	public function indexAction() {	
	   //lấy thông tin chi tiết của sản phẩm
       $tblProduct=new Product_Model_Product();
       $this->view->proDetail=$tblProduct->getItem($this->_arrParam,array('task'=>'front-detail'));
       $this->_arrParam['cat_id']=$this->view->proDetail['cat_id'];
       
       $this->view->proOrder=$tblProduct->listItem($this->_arrParam,array('task'=>'front-order'));
       //lấy các ảnh slideshow
       $tblProductphoto=new Product_Model_Productphoto();
       $this->view->listDetailphoto=$tblProductphoto->listItem($this->_arrParam,array('task'=>'list-by-proid'));
      
	    //title
		$this->view->Title = 'Unicity Việt Nam - Happy Life Project | Trang chủ';
		$this->view->headTitle($this->view->Title,true);
        
		
	}
    
	public function chiTietAction() {	
	    //title
		$this->view->Title = 'Unicity Việt Nam - Happy Life Project | Trang chủ';
		$this->view->headTitle($this->view->Title,true);
        
		//$this->_helper->viewRenderer->setNoRender();
		//$this->_helper->layout->disableLayout();
	}
	
}
