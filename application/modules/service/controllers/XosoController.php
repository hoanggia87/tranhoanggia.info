<?php
class Service_XosoController extends Zendvn_Controller_Action {

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
	    $this->view->Title = 'Hoa Tiêu Online | Danh bạ Website Việt Nam - Tiện ích kết quả sổ xố.';
		$this->view->headTitle($this->view->Title,true);
        $this->view->headMeta()->setName('description', 'Tiện ích kết quả sổ xố có thể giúp bạn nhanh chóng xem 7 kì liên tục và tất cả các tỉnh thành.');
		$service=new Service_Model_Xoso();
		$cache=Zendvn_Cache::getCacheObject('cacheforever');
		if ( ($this->view->xoso = $cache->load('xoso_'.date('Ymd'))) === false ) {
			
			$this->view->xoso=$service->getXoso();
			$cache->save($this->view->xoso);
		}

		
		$this->view->listLocation=$service->getListLocation();
		/*echo "<pre>";
        var_dump($this->view->xoso);
        echo "</pre>";*/
		//$this->_helper->viewRenderer->setNoRender();
		//$this->_helper->layout->disableLayout();
	}
    public function getnewsAction()
    {
        $service=new Service_Model_Xoso();
        $cache=Zendvn_Cache::getCacheObject('cacheforever');
		if ( ($arrXoso = $cache->load('xoso_news_'.date('Ymd'))) === false ) {
			
			$arrXoso=$service->getNews();
			$cache->save($arrXoso);
		}
        if($arrXoso)
        {
            $strNews ='<ul>';
            foreach($arrXoso as $k=>$v)
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
        $this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
    }
	
}
