<?php
class Service_BongdaController extends Zendvn_Controller_Action {

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
	public function indexAction()
    {
        $this->view->Title = 'Hoa Tiêu Online | Danh bạ Website Việt Nam - Lịch thi đấu bóng đá.';
		$this->view->headTitle($this->view->Title,true);
        $this->view->headMeta()->setName('description', 'Tiện ích xem lịch thi đấu bóng đá, bảng xếp hạng và tỉ lệ cược giúp bạn nhanh chóng xem tỉ lệ cược 5 giải lớn (Anh, Italia, Đức, Pháp).');
        $date = date('Y-m-d');;
        $key = str_replace('-','',$date);
        $service=new Service_Model_Bongda();
        $cache=Zendvn_Cache::getCacheObject('cacheforever');
        if ( ($this->view->tilecuoc = $cache->load('tilecuoc_'.$key)) === false ) 
        {			
			$this->view->tilecuoc=$service->getTLC();
			$cache->save($this->view->tilecuoc);
		}
        if ( ($this->view->bxh = $cache->load('bxh_'.$key)) === false ) 
        {			
			$this->view->bxh=$service->getBXH();
			$cache->save($this->view->bxh);
		}
        $this->_arrParam['date'] = $date;
        //$result=$service->getLTD($this->_arrParam);
        if ( ($this->view->ltd = $cache->load('ltd_'.$key)) === false ) 
        {			
			$this->view->ltd=$service->getLTD($this->_arrParam);
			$cache->save($this->view->ltd);
		}
        //$this->view->ltd=$service->getLTD($this->_arrParam);
        //echo $this->_arrParam['d'],'-',$this->_arrParam['m'],'-',$this->_arrParam['y'];
        /**$result=$service->getLTD($this->_arrParam);
        //$result=$service->getBXH();
        echo "<pre>";
        var_dump($result);
        echo "</pre>";*/
        $this->view->country = $service->getCountry();
    }	
	/*public function bangxephangAction() {
		$service=new Service_Model_Bongda();
		$result=$service->getBXH(array(
									'qg'=>$this->_arrParam['qg']
								));
		
	
		echo '<pre>';
		print_r($result);
		echo '</pre>';
		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
	}*/
	public function lichthidauAction() {
	    $key = str_replace('-','',$this->_arrParam['date']);
		$service=new Service_Model_Bongda();
        $cache=Zendvn_Cache::getCacheObject('cacheforever');
		if ( ($ltd = $cache->load('ltd_'.$key)) === false ) 
        {			
			$ltd=$service->getLTD($this->_arrParam);
			$cache->save($ltd);
		}
        $arrReturn = array('rs'=>1,'anh'=>$ltd['1'],'tbn'=>$ltd['2'],'y'=>$ltd[3],'duc'=>$ltd[4],'phap'=>$ltd[5]);
        $this->_helper->json($arrReturn);
		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
	}
	public function getnewsAction()
    {
        $maxNews = 10;
        $service=new Service_Model_Bongda();
        $strInfo=$service->getNews();
        //echo $strInfo;
        $arrListNews = json_decode($strInfo);
        /*echo count($arrListNews);
        echo "<pre>";
        var_dump($arrListNews);
        echo "</pre>";*/
        if($arrListNews)
        {
            $strNews ='<ul>';
            $i = 0;
            foreach($arrListNews as $k=>$v)
            {
                if($i==$maxNews)
                {
                    break;
                }
                $strNews .= '<li><a target="_blank" href="'.$v->url.'">'.$v->title.'</a></li>';
                $i++;
            }   
            $strNews .='</ul>';
        }
        else
        {
            $strNews = 'Không có dữ liệu';
        }
        //echo $strNews;
        $this->_helper->json(array('rs'=>'1','info'=>$strNews));
        $this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
    }
}
