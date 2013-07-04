<?php
class AdminUserController extends Zendvn_Controller_Action{
	
	
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
	protected $_controllerConfig; 
	
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
			$ssFilter->col 			= 'u.id';
			$ssFilter->order 		= 'DESC';
			$ssFilter->group_id		= 0;
		}
		$this->_arrParam['ssFilter']['keywords'] 	= $ssFilter->keywords;
		$this->_arrParam['ssFilter']['col'] 		= $ssFilter->col;
		$this->_arrParam['ssFilter']['order'] 		= $ssFilter->order;
		$this->_arrParam['ssFilter']['group_id'] 	= $ssFilter->group_id;
		
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
		
		$template_path = TEMPLATE_PATH . "/admin/system";
		$this->loadTemplate($template_path,'template.ini','template');
		
		//khai bao thu muc upload useravatar	
		$this->_controllerConfig  = array('imagesDir'=> PUBLIC_PATH.'/files/user_avatar/');		
		$this->_arrParam['controllerConfig'] = $this->_controllerConfig;
	}

	public function indexAction(){
		
		$this->view->Title = 'Member :: User manager :: List';
		$this->view->headTitle($this->view->Title,true);
		
		$tblUser = new Default_Model_Users();
		$this->view->Items = $tblUser->listItem($this->_arrParam, array('task'=>'admin-list'));
		
		$tblGroup = new Default_Model_UserGroup();
		$this->view->slbGroup = $tblGroup->itemInSelectbox();
		
		$totalItem  = $tblUser->countItem($this->_arrParam);
		
		$paginator = new Zendvn_Paginator();
		$this->view->panigator = $paginator->createPaginator($totalItem,$this->_paginator);
		/*echo '<pre>';
			print_r($this->view->slbGroup);
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
		if($this->_arrParam['type'] == 'pagging'){
			$ssFilter->pagging = $this->_arrParam['pagging'];
			
		}
		$this->_redirect($this->_actionMain);
		$this->_helper->viewRenderer->setNoRender();
	}
	
	
	public function addAction(){
		
		$this->view->Title = 'Member :: User manager :: Add new';
		$this->view->headTitle($this->view->Title,true);
		//list group
		$tblGroup = new Default_Model_UserGroup();
		$this->view->slbGroup = $tblGroup->itemInSelectbox();
		if($this->_request->isPost()){
			
			$validate = new Default_Form_UserValidate($this->_arrParam);
			if($validate->checkError() == true){
				//Hien thi thong bao loi
				$this->view->errors = $validate->getMessageError();
				$this->view->Item = $validate->getData();
			}else{
				//Luu vao database
				$tblUser  = new  Default_Model_Users();
				
				$tblUser->saveItem($validate->getData(),array('task'=>'admin-add'));
				$this->view->errors = array('success');
				
				
				//$this->_redirect($this->_actionDefault);
			}			
			
		}
		
	}
	
	public function infoAction(){
		$this->view->Title = 'Member :: User manager :: Information';
		$this->view->headTitle($this->view->Title,true);
		$tblUser = new Default_Model_Users();
		$this->view->Item = $tblUser->getItem($this->_arrParam,array('task'=>'admin-info'));
		
	}
	
	public function editAction(){
		$this->view->Title = 'Member :: User manager :: Edit';
		$this->view->headTitle($this->view->Title,true);				
		//list group
		$tblGroup = new Default_Model_UserGroup();
		$this->view->slbGroup = $tblGroup->itemInSelectbox();
		//get item info
		$tblUser = new Default_Model_Users();	
		$this->view->Item = $tblUser->getItem($this->_arrParam,array('task'=>'admin-edit'));
		
		
		if($this->_request->isPost()){
			
			$validate = new Default_Form_UserValidate($this->_arrParam,'edit');
			if($validate->checkError() == true){
				//Hien thi thong bao loi
				$this->view->errors = $validate->getMessageError();
				$this->view->Item = $validate->getData();
			}else{
				//Luu vao database
				$tblUser  = new  Default_Model_Users();
				/*echo '<pre>';
					print_r($validate->getData());
				echo '</pre>';*/
				$tblUser->saveItem($validate->getData(),array('task'=>'admin-edit'));
				$this->view->errors = array('success');
				$this->view->Item=$this->_arrParam;
			}			
			
		}
	}

	public function deleteAction(){
		$this->view->Title = 'Member :: User manager :: Delete';
		$this->view->headTitle($this->view->Title,true);
		if($this->_request->isPost()){
			$tblUser = new Default_Model_Users();
			$tblUser->deleteItem($this->_arrParam,array('task'=>'admin-delete'));
			$this->_redirect($this->_actionMain);
		}
	}

	public function statusAction(){
		$tblUser = new Default_Model_Users();		
		$tblUser->changeStatus($this->_arrParam);
		$this->_redirect($this->_actionMain.'/page/'.$this->_arrParam['page']);		
		$this->_helper->viewRenderer->setNoRender();
	}
	
	public function multiDeleteAction(){
		
		if($this->_request->isPost()){
			$tblUser = new Default_Model_Users();
			$tblUser->deleteItem($this->_arrParam,array('task'=>'admin-multi-delete'));
			$this->_redirect($this->_actionMain);
		}
		$this->_helper->viewRenderer->setNoRender();
	}
	
	public function sortAction(){
		if($this->_request->isPost()){
			$tblUser = new Default_Model_Users();
			$tblUser->sortItem($this->_arrParam);
			$this->_redirect($this->_actionMain);
		}
		
		$this->_helper->viewRenderer->setNoRender();
	}
	public function testAction(){
		echo '<form name="appForm" method="post" action="">';
		$user_avatar	= $this->view->formFile('user_avatar',array('style'=>'width: 250px'));
		echo $user_avatar;
		echo '<input type="submit" value="test">';
		echo '</form>';
			
		if($this->_request->isPost()){
			echo '<pre>';
				print_r($this->_arrParam);
			echo '</pre>';
			$uploadDir=FILES_PATH.'/';
			$fileName='user_avatar';
			
			$upload = new Zend_File_Transfer_Adapter_Http();		
			$upload->setDestination($uploadDir,$fileName);
			$fileInfo = $upload->getFileInfo();
			$newFileName = $fileInfo[$fileName]['name'];
			$upload->receive($fileName);
			echo '<pre>';
				print_r($upload);
			echo '</pre>';
			
		}
		$this->_helper->viewRenderer->setNoRender();
	}

	
}




