<?php
class Service_ThoitietController extends Zendvn_Controller_Action {

	//Mang tham so nhan duoc o moi Action
	protected $_arrParam;
	
	//Duong dan cua Controller
	protected $_currentController;
	
	//Duong dan cua Action chinh
	protected $_actionMain;
	
	//Thong so phan trang
	protected $_paginator = array(
									'itemCountPerPage' => 20,
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

		//Truyen ra view
		$this->view->arrParam = $this->_arrParam;
		$this->view->currentController = $this->_currentController;
		$this->view->actionMain = $this->_actionMain;
		
		//phan trang
		$this->_paginator['currentPage'] = $this->_request->getParam('page',1);
		$this->_arrParam['paginator'] = $this->_paginator;		
	}
		
	public function indexAction() {
	    $this->view->Title = 'Hoa Tiêu Online | Danh bạ Website Việt Nam - Tiện ích thời tiết.';
		$this->view->headTitle($this->view->Title,true);
        $this->view->headMeta()->setName('description', 'Tiện ích thời tiết Giúp cho bạn có thể xem nhanh thời tiết ở 64 tỉnh thành trên cả nước, dự báo trước thời tiết 2 ngày tới.');
		$service=new Service_Model_Thoitiet();
		$cache=Zendvn_Cache::getCacheObject('cacheforever');
		if ( ($this->view->thoitiet = $cache->load('thoitiet_'.date('Ymd'))) === false ) {
			
			$this->view->thoitiet=$service->getThoitiet();
			$cache->save($this->view->thoitiet);
		}

		
		$this->view->listLocation=$service->getListLocation();
		
		//$this->_helper->viewRenderer->setNoRender();
		//$this->_helper->layout->disableLayout();
	}
    public function getnewsAction()
    {
        $service=new Service_Model_Thoitiet();
        
		$cache=Zendvn_Cache::getCacheObject('cacheforever');
		if ( ($newsthoitiet = $cache->load('thoitiet_news_'.date('Ymd'))) === false ) {
			
			$newsthoitiet=$service->getNewsThoitiet();
			$cache->save($newsthoitiet);
		}
        //$newsthoitiet=$service->getNewsThoitiet();
        //var_dump($newsthoitiet);
        if($newsthoitiet)
        {
            $strNews ='<ul>';
            foreach($newsthoitiet as $k=>$v)
            {
                $strNews .= '<li><a target="_blank" href="'.$v['link'].'">'.$v['content'].'</a></li>';
            }   
            $strNews .='</ul>';
        }
        else
        {
            $strNews = 'Không có dữ liệu';
        }
        $this->_helper->json(array('rs'=>'1','info'=>$strNews));
        //var_dump($this->view->newsthoitiet);
		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
    }
    //http://www.hoatieuonline.vn/service/thoitiet/json-thoitiet/matinh/44
    public function jsonThoitietAction()
    {
    	$service=new Service_Model_Thoitiet();
		$cache=Zendvn_Cache::getCacheObject('cacheforever');
		if ( ($this->view->thoitiet = $cache->load('thoitiet_'.date('Ymd'))) === false ) {
			
			$this->view->thoitiet=$service->getThoitiet();
			$cache->save($this->view->thoitiet);
		}

		
		echo json_encode($this->view->thoitiet[$this->_arrParam['matinh']][0]);

		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
    }
	public function jsonAction()
    {
    	$service=new Service_Model_Thoitiet();
    	echo json_encode($service->getListLocation());

		//$this->view->listLocation=$service->getListLocation();
		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
    }
}
