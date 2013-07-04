<?php
class Product_DetailController extends Zendvn_Controller_Action {
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
	
		//Truyen ra view
		$this->view->arrParam = $this->_arrParam;
		$this->view->currentController = $this->_currentController;
		$this->view->actionMain = $this->_actionMain;
		
		//phan trang
		$this->_paginator['currentPage'] = $this->_request->getParam('page',1);
		$this->_arrParam['paginator'] = $this->_paginator;
        
        
	}
		
	
	  
    public function index1Action() {   
        $this->view->headLink()->appendStylesheet($this->view->cssUrl . '/product.css','screen');
	   //lấy thông tin chi tiết của sản phẩm
       $tblProduct=new Product_Model_Product();
       $this->view->proDetail=$tblProduct->getItem($this->_arrParam,array('task'=>'front-detail'));
       
       //lấy các ảnh slideshow
       $tblProductphoto=new Product_Model_Productphoto();
       $this->view->listDetailphoto=$tblProductphoto->listItem($this->_arrParam,array('task'=>'list-by-proid','type'=>1));
	   
        //title
        $ttName=$this->view->cmsConvertDbToView($this->view->proDetail['name']);
        $ttSummary=$this->view->cmsConvertDbToView(strip_tags($this->view->proDetail['summary']));
		$this->view->Title = $ttName.' | '.str_replace('"','',$ttSummary);
		$this->view->headTitle($this->view->Title,true);
        $this->view->headMeta()->setName('description', $ttName.' '.str_replace('"','',$ttSummary));
        $this->view->headLink()->headLink(array('rel' => 'image_src','href' => PUBLIC_URL.'/files/products/image/'.$this->view->proDetail['image']),'PREPEND');

		
	}
    
    	
	public function indexAction() {	  

		$this->view->headScript()->appendFile($this->view->jsUrl . '/jquery-1.9.1.js','text/javascript');
		$this->view->headScript()->appendFile($this->view->jsUrl . '/jquery-ui-1.10.1.custom.js','text/javascript');
		$this->view->headScript()->appendFile($this->view->jsUrl . '/jquery.mousewheel-3.0.6.pack.js','text/javascript');
		$this->view->headScript()->appendFile($this->view->jsUrl . '/jquery.fancybox.js','text/javascript');
		$this->view->headScript()->appendFile($this->view->jsUrl . '/jquery.fancybox-media.js','text/javascript');
		$this->view->headScript()->appendFile($this->view->jsUrl . '/jquery.fancybox-buttons.js','text/javascript');
		$this->view->headScript()->appendFile($this->view->jsUrl . '/jquery.fancybox-thumbs.js','text/javascript');
		$this->view->headScript()->appendFile($this->view->jsUrl . '/main.js','text/javascript');


        $cache=Zendvn_Cache::getCacheObject('productdetailparam');
		if ( ($this->view->proDetail = $cache->load('controller_cache_product_detail_'.$this->_arrParam['id'])) === false ) {
			//lấy thông tin chi tiết của sản phẩm
	        $tblProduct=new Product_Model_Product();
	        $this->view->proDetail=$tblProduct->getItem($this->_arrParam,array('task'=>'front-detail'));
			$cache->save($this->view->proDetail);
		}
	    
		if ( ($this->view->listProducts = $cache->load('controller_cache_product_order_'.$this->_arrParam['id'])) === false ) {
			//lấy thông tin chi tiết của sản phẩm
	        $tblProduct=new Product_Model_Product();
	        //get order product		
			$this->view->listProducts=$tblProduct->listItem($this->_arrParam,array('task'=>'list-product-the-same'));
			$cache->save($this->view->listProducts);
		}

		$cache=Zendvn_Cache::getCacheObject('cachedatacategoryinfo');
		if ( ($this->view->categoryDetail = $cache->load('controller_cachedatacategoryinfo_'.$this->_arrParam['cat_id'])) === false ) {
			//info cat
	        $tblCategory	=	new Product_Model_Productcategory();
			$this->view->categoryDetail=$tblCategory->getItem(array('id'=>$this->_arrParam['cat_id']),array('task'=>'admin-edit'));
        
			$cache->save($this->view->categoryDetail);
			
		}

        
		

       //lấy các ảnh slideshow
       //$tblProductphoto=new Product_Model_Productphoto();
       //$this->view->listDetailphoto=$tblProductphoto->listItem($this->_arrParam,array('task'=>'list-by-proid','type'=>1));
	   
        //title
        $ttName=$this->view->cmsConvertDbToView($this->view->proDetail['name']);
        $ttSummary=$this->view->cmsConvertDbToView(strip_tags($this->view->proDetail['summary']));
		$this->view->Title = $ttName.' | '.str_replace('"','',$ttSummary).' vòng tay hand made đẹp giá cực rẽ';
		$this->view->headTitle($this->view->Title,true);
        $this->view->headMeta()->setName('description', $ttName.' '.str_replace('"','',$ttSummary));
        $this->view->headLink()->headLink(array('rel' => 'image_src','href' => PUBLIC_URL.'/files/products/image/'.$this->view->proDetail['image']),'PREPEND');

		
        
       	
	   }

	
}
