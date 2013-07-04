<?php
class Service_TruyenhinhController extends Zendvn_Controller_Action {

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
	    $this->view->Title = 'Hoa Tiêu Online | Danh bạ Website Việt Nam - Lịch chiếu truyền hình';
		$this->view->headTitle($this->view->Title,true);
        $this->view->headMeta()->setName('description', 'Tiện ích xem kênh truyền hình có thể giúp bạn xem nhanh chóng nhiều kênh truyền hình và 30 ngày trong tháng.');
		$service=new Service_Model_Truyenhinh();
		$cache=Zendvn_Cache::getCacheObject('cacheforever');
        $date = $this->_arrParam['date'];
        $chanel = $this->_arrParam['chanel'];
        $first_day_of_week = date('d-m-Y', strtotime('Last Monday', time()));
        $arrDate = explode('-',$first_day_of_week);
        $this->_arrParam['date'] = (int)$arrDate[0];
        $this->_arrParam['month'] = (int)$arrDate[1];
        $this->_arrParam['year'] = (int)$arrDate[2];
        $key = str_replace('-','',$first_day_of_week);
		if ( ($this->view->truyenhinh = $cache->load('truyenhinh_'.$key)) === false ) {
			
			$this->view->truyenhinh=$service->getTruyenhinh($this->_arrParam);
			$cache->save($this->view->truyenhinh);
		}
        $this->view->listChanel = $service->getListChanel();
        /*echo "<pre>";
        var_dump($this->view->truyenhinh);
        echo "</pre>";*/
	}	
	public function gettruyenhinhAction() {
		
		$service=new Service_Model_Truyenhinh();
		$cache=Zendvn_Cache::getCacheObject('cacheforever');
        $date = $this->_arrParam['date'];
        $chanel = $this->_arrParam['chanel'];
        $first_day_of_week = date('d-m-Y', strtotime('Last Monday', time()));
        $arrDate = explode('-',$first_day_of_week);
        $this->_arrParam['date'] = (int)$arrDate[0];
        $this->_arrParam['month'] = (int)$arrDate[1];
        $this->_arrParam['year'] = (int)$arrDate[2];
        $key = str_replace('-','',$first_day_of_week);
		if ( ($this->view->truyenhinh = $cache->load('truyenhinh_'.$key)) === false ) {
			
			$this->view->truyenhinh=$service->getTruyenhinh($this->_arrParam);
            if($this->view->truyenhinh)
            {
			     $cache->save($this->view->truyenhinh);
            }
		}
        /*$arrInfo = array('rs'=>1,'info'=>$this->view->truyenhinh);
        $this->_helper->json($arrInfo);
        $this->view->truyenhinh=$service->getTruyenhinh($this->_arrParam);*/ 
		echo "<pre>";
        var_dump($this->view->truyenhinh);
        echo "</pre>";
		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
	}
	
}
