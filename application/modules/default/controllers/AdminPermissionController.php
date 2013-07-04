<?php
class AdminPermissionController extends Zendvn_Controller_Action{
	
	
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
			$ssFilter->col 			= 'u.privilege_id';
			$ssFilter->order 		= 'DESC';
			$ssFilter->group_id		= 0;
		}
		$this->_arrParam['ssFilter']['keywords'] 	= $ssFilter->keywords;
		$this->_arrParam['ssFilter']['col'] 		= $ssFilter->col;
		$this->_arrParam['ssFilter']['order'] 		= $ssFilter->order;
		$this->_arrParam['ssFilter']['group_id'] 	= $ssFilter->group_id;
		
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
		
		//khai bao thu muc upload Permissionavatar	
		$this->_controllerConfig  = array('imagesDir'=> PUBLIC_PATH.'/files/Permission_avatar/');		
		$this->_arrParam['controllerConfig'] = $this->_controllerConfig;
	}

	public function indexAction(){
		
		$this->view->Title = 'Member :: Permission manager :: List';
		$this->view->headTitle($this->view->Title,true);
		
		$tblPermission = new Default_Model_Permission();
		$this->view->Items = $tblPermission->listItem($this->_arrParam, array('task'=>'admin-list'));
		
		$tblGroup = new Default_Model_UserGroup();
		$this->view->slbGroup = $tblGroup->itemInSelectbox();
		
		$totalItem  = $tblPermission->countItem($this->_arrParam);
		
		$paginator = new Zendvn_Paginator();
		$this->view->panigator = $paginator->createPaginator($totalItem,$this->_paginator);
		
		/*echo '<pre>';
			print_r($totalItem);
		echo '</pre>';
		$this->_helper->viewRenderer->setNoRender();*/
		
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
	
		
	public function infoAction(){
		$this->view->Title = 'Member :: Permission manager :: Information';
		$this->view->headTitle($this->view->Title,true);
		$tblPermission = new Default_Model_Permission();
		$this->view->Item = $tblPermission->getItem($this->_arrParam,array('task'=>'admin-info'));
		
	}
    
	public function addAction(){
		$this->view->Title = 'Member :: Permission manager :: Add new';
		$this->view->headTitle($this->view->Title,true);
		
        //lay danh sach group
        $tblGroup = new Default_Model_UserGroup();
		$this->view->slbGroup = $tblGroup->itemInSelectbox();
        //danh sách permission
        $tblPrivileges = new Default_Model_Privileges();
		$this->view->slbPrivileges = $tblPrivileges->listItem($this->_arrParam,array('task'=>'admin-list-selectbox'));
        
        
		if($this->_request->isPost()){
			
            $tblPermission = new Default_Model_Permission();
			$tblPermission->saveItem($this->_arrParam,array('task'=>'admin-multi-add'));
			$this->_redirect($this->_actionMain);
		}
		
	}
	

	public function deleteAction(){
		$this->view->Title = 'Member :: Permission manager :: Delete';
		$this->view->headTitle($this->view->Title,true);
		if($this->_request->isPost()){
			$tblPermission = new Default_Model_Permission();
			
            //echo 'here';
            $tblPermission->deleteItem($this->_arrParam,array('task'=>'admin-delete'));
			$this->_redirect($this->_actionMain);
		}
	}

	public function statusAction(){
		$tblPermission = new Default_Model_Permission();		
		$tblPermission->changeStatus($this->_arrParam);
		$this->_redirect($this->_actionMain.'/page/'.$this->_arrParam['page']);		
		$this->_helper->viewRenderer->setNoRender();
	}
	
	public function multiDeleteAction(){
		
		if($this->_request->isPost()){
			$tblPermission = new Default_Model_Permission();
			$tblPermission->deleteItem($this->_arrParam,array('task'=>'admin-multi-delete'));
			$this->_redirect($this->_actionMain);
		}
		$this->_helper->viewRenderer->setNoRender();
	}
	
	public function sortAction(){
		if($this->_request->isPost()){
			$tblPermission = new Default_Model_Permission();
			$tblPermission->sortItem($this->_arrParam);
			$this->_redirect($this->_actionMain);
		}
		
		$this->_helper->viewRenderer->setNoRender();
	}
	

	
}




