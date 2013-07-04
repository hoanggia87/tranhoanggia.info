<?php
class Service_TigiaController extends Zendvn_Controller_Action {

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
		$service=new Service_Model_Tigia();
        $this->view->tigiaUSD = $service->getTigiaUSD();
    }
    public function vangAction() 
    {
        $this->view->Title = 'Hoa Tiêu Online | Danh bạ Website Việt Nam - Tiện ích xem tỉ giá vàng.';
		$this->view->headTitle($this->view->Title,true);
        $this->view->headMeta()->setName('description', 'Tiện ích xem tỉ giá usd giúp bạn xem thông tin về tỉ giá usd.');
	}
    public function usdAction() 
    {
        $this->view->Title = 'Hoa Tiêu Online | Danh bạ Website Việt Nam - Tiện ích xem tỉ giá đô.';
		$this->view->headTitle($this->view->Title,true);
        $this->view->headMeta()->setName('description', 'Tiện ích xem tỉ giá vàng giúp bạn xem thông tin về tỉ giá vàng trong nước, tỉ giá vàng quốc tế, biểu đồ vàng trong nước, biểu đồ vàng quốc tế.');
        $service=new Service_Model_Tigia();
        $this->view->tigiaUSD = $service->getTigiaUSD();
	}
	public function getvangAction() {
		$service=new Service_Model_Tigia();
        $key = date('dmY');
        $cache=Zendvn_Cache::getCacheObject('cacheforever');
        if ( ($tigaVang = $cache->load('tigiavang_'.$key)) === false ) {
			
			$tigaVang = $service->getTigiavang();
			$cache->save($tigaVang);
		}
        $arrInfo = array('rs'=>'1','vangtrongnuoc'=>$tigaVang['vang']['trongnuoc'],'vangquocte'=>$tigaVang['vang']['quocte'],'bieudotrongnuoc'=>$tigaVang['bieudo']['trongnuoc'],'bieudoquocte'=>$tigaVang['bieudo']['quocte']);
        $this->_helper->json($arrInfo);
		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
	}
    public function getnewsAction()
    {
        $maxNews = 6;
        $service=new Service_Model_Tigia();
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
