<?php
class Product_AdminProductCategoryController extends Zendvn_Controller_Action{
	
	
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
		$this->_controllerConfig  = array('imagesDir'=> PUBLIC_PATH.'/files/Product/');		
		$this->_arrParam['controllerConfig'] = $this->_controllerConfig;
	}

	public function indexAction(){
		
		$this->view->Title = 'Product :: Category manager :: List';
		$this->view->headTitle($this->view->Title,true);
		
		$tblCategory = new Product_Model_Productcategory();
		$this->view->Items = $tblCategory->listItem($this->_arrParam, array('task'=>'admin-list'));
		$this->view->ItemsSelect = $tblCategory->listItem($this->_arrParam, array('task'=>'admin-list-select','root'=>true));
				
				
		//$totalItem  = $tblCategory->countItem($this->_arrParam);
		
		//$paginator = new Zendvn_Paginator();
		//$this->view->panigator = $paginator->createPaginator($totalItem,$this->_paginator);
		/*echo '<pre>';
			print_r($this->view->Items);
		echo '</pre>';*/
		//$this->_helper->viewRenderer->setNoRender();
		
	}
	

	public function getListAction(){
		
	
		
		$tblCategory = new Product_Model_Productcategory();
		$this->view->Items = $tblCategory->listItem($this->_arrParam, array('task'=>'admin-list'));
				
		//$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
	}
	public function getListSelectAction(){
		
	
		$tblCategory = new Product_Model_Productcategory();
		$this->view->ItemsSelect = $tblCategory->listItem($this->_arrParam, array('task'=>'admin-list-select','root'=>true));
				
		//$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
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
		$tblCategory = new Product_Model_Productcategory();
		
		$arrReturn=array();

		$validate = new Product_Form_ProductCategoryValidate($this->_arrParam);
		
		if($validate->checkError() == true){
			
			$arrReturn['error']=$validate->getMessageError();
			$arrReturn['data']=$validate->getData();
		}else{
			//Luu vao database
			//clear cache
			
            
			/*echo '<pre>';
				print_r($validate->getData());
			echo '</pre>';*/
			$id=$tblCategory->saveItem($validate->getData(),array('task'=>'admin-add'));
			//cap nhat lai category
			//$this->view->listCat= $tblCategory->listItem($this->_arrParam,array('task'=>'admin-list-select','root'=>'true'));
			
			
			$arrReturn['error']=array();
			$arrReturn['data']=$validate->getData();
			$arrReturn['data']['id']=$id;
		}		
		
		echo json_encode($arrReturn);
		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
		
	}
	
	public function infoAction(){
		$this->view->Title = 'Product :: Category manager :: Information';
		$this->view->headTitle($this->view->Title,true);
		$tblCategory = new Product_Model_Productcategory();
		$this->view->Item = $tblCategory->getItem($this->_arrParam,array('task'=>'admin-info'));
		
	}
	
	public function editAction(){
		$this->view->Title = 'Product :: Category manager :: Edit';
		$this->view->headTitle($this->view->Title,true);				
		
		//get item info
		$tblCategory 		= new Product_Model_Productcategory();	
		$this->view->Item 	= $tblCategory->getItem($this->_arrParam,array('task'=>'admin-edit'));
		$this->view->listCat= $tblCategory->listItem($this->_arrParam,array('task'=>'admin-list-select','root'=>'true'));
		


		if($this->view->Item['created_by'] && $this->view->Item['modified_by'])
		{
			//get user info
			$user = Zendvn_User_User::getInstance();
			$arrUserID=array($this->view->Item['created_by'],$this->view->Item['modified_by']);



			$arrTemp=$user->getUserByMultiTable($arrUserID,'user_id,full_name,avatar');

			$arrTempInfoUser=array();
			foreach ($arrTemp as $key => $value) 
			{	
				$arrTempInfoUser[$value['user_id']]=$value;
			}
			$this->view->arrInfoUser=$arrTempInfoUser;

		}
		


		/*
		if($this->_request->isPost()){
			
			$validate = new Product_Form_ProductCategoryValidate($this->_arrParam);
			if($validate->checkError() == true){
				//Hien thi thong bao loi
				$this->view->errors = $validate->getMessageError();
				$this->view->Item = $validate->getData();
			}else{
				//Luu vao database
				//clear cache
				$cache=Zendvn_Cache::getCacheObject('defaultindexindexmenuheader');	
                $cache->remove('block_menuheader');
                $cache=Zendvn_Cache::getCacheObject('cachedatacategoryinfo');
                $cache->remove('controller_cachedatacategoryinfo_'.$this->_arrParam['id']);
                          
                
		
				$tblCategory->saveItem($validate->getData(),array('task'=>'admin-edit'));
				//cap nhat lai category
				$this->view->listCat= $tblCategory->listItem($this->_arrParam,array('task'=>'admin-list-select','root'=>'true'));
				//cap nhat lai gia tri cua item
				$this->view->Item=$this->_arrParam;
				$this->view->errors = array('success');
				
			}		
			
		}
		*/
	}



	public function saveAction()
	{
		$tblCategory = new Product_Model_Productcategory();
		$validate = new Product_Form_ProductCategoryValidate($this->_arrParam);
		if($validate->checkError() == true){
			//Hien thi thong bao loi
			$arrReturn['error']=$validate->getMessageError();
			$arrReturn['data']=$validate->getData();
		}else{
			
			$tblCategory->saveItem($validate->getData(),array('task'=>'admin-edit'));
			

			$arrReturn['error']=array();
			$arrReturn['data']=$validate->getData();
			$arrReturn['data']['id']=$id;
			
		}		

		echo json_encode($arrReturn);
		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
	}

	public function deleteAction(){
	
		$tblCategory = new Product_Model_Productcategory();
		$tblCategory->deleteItem($this->_arrParam,array('task'=>'admin-delete'));

		$arrReturn['error']=array();
		echo json_encode($arrReturn);

		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
	}

	public function statusAction(){		
		$tblCategory = new Product_Model_Productcategory();		
		$tblCategory->changeStatus($this->_arrParam);
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
	}
	

	public function sortAction(){
		
		          
		$tblCategory = new Product_Model_Productcategory();
		$tblCategory->sortItem($this->_arrParam);
		
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
	}

	
}




