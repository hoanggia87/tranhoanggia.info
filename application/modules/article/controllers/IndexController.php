<?php
class Article_IndexController extends Zendvn_Controller_Action {

	//Mang tham so nhan duoc o moi Action
	protected $_arrParam;
	
	//Duong dan cua Controller
	protected $_currentController;
	
	//Duong dan cua Action chinh
	protected $_actionMain;
	
	//Thong so phan trang
	protected $_paginator = array(
									'itemCountPerPage' => 10,
									'pageRange' => 5,
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

		//khai bao thu muc upload 	
		$this->_controllerConfig  = array('imagesDir'=> PUBLIC_PATH.'/files/article/');		
		$this->_arrParam['controllerConfig'] = $this->_controllerConfig;

		//Truyen ra view
		$this->view->arrParam = $this->_arrParam;
		$this->view->currentController = $this->_currentController;
		$this->view->actionMain = $this->_actionMain;
		
		//phan trang
		$this->_paginator['currentPage'] = $this->_request->getParam('page',1);
		$this->_arrParam['paginator'] = $this->_paginator;		
	}
		
	public function indexAction() {
		$tblArticle=new Article_Model_Article();
		$this->view->listArticle=$tblArticle->listItem($this->_arrParam,array('task'=>'front-list-by-cat'));
		//phan trang
		$totalItem  = $tblArticle->countItem($this->_arrParam,array('task'=>'list-by-cat'));
		
		
		$paginator = new Zendvn_Paginator();
		$this->view->panigator = $paginator->createPaginator($totalItem,$this->_paginator);

		//lấy thông tin user
		$arrUserID=array();
		//lay danh sach id article
		$arrAid=array();
		foreach ($this->view->listArticle as $key => $value) {
			$arrUserID[]=$value['user_id'];
			$arrAid[]=$value['id'];
		}
		//update view
		$tblStat=new Stats_Model_Stats();
		$tblStat->updateView($arrAid);


		$user = Zendvn_User_User::getInstance();
		$arrTemp=$user->getUserByMultiTable($arrUserID,'user_id,full_name,avatar');

		$arrTempInfoUser=array();
		
		foreach ($arrTemp as $key => $value) {
			
			$arrTempInfoUser[$value['user_id']]=$value;

			
			
		}

		
		$this->view->arrUserInfo=$arrTempInfoUser;

	}

	public function detailAction()
	{
		//up date view
		$tblStat=new Stats_Model_Stats();
		$tblStat->updateView($this->_arrParam['id']);   
		
		//get infodetal
		$tblArticle=new Article_Model_Article();
		$arrInfo=$tblArticle->getItem($this->_arrParam,array('task'=>'front-detail'));

		$this->view->detail=$this->view->infoNext=$this->view->infoPrev=array();
		foreach ($arrInfo as $key => $value) {
		    if($key>$this->_arrParam['id'])    
		    {
		        $this->view->infoNext=$value;
		    }
		    elseif($key<$this->_arrParam['id'])
		    {
		        $this->view->infoPrev=$value;
		    }
		    else
		    {
		        $this->view->detail=$value;
		    }
		}

		//get order article cùng từ khóa title
		$this->_arrParam['k']=$this->view->detail['title'];
		$this->view->listOrder=$tblArticle->listItem($this->_arrParam,array('task'=>'front-list-order'));

		//lấy thong tin user
		$arrUserID=array($this->view->detail['user_id']);


		$user = Zendvn_User_User::getInstance();
		$arrTemp=$user->getUserByMultiTable($arrUserID,'user_id,full_name,avatar');
		$this->view->userInfo=$arrTemp[0];
		 //title
    	$this->view->Title = 'ShockVL | '.$this->view->detail['title'].'. Chỉ có tại ShockVL.Com';
    	$this->view->headTitle($this->view->Title,true);
        $this->view->headMeta()->setName('description', 'Bạn đang xem chuyên mục hình ảnh (video, truyện cười,báo hay, tâm sự) trên ShockVL với chủ đề '.$this->view->detail['title']);
		     
        
	}
}
