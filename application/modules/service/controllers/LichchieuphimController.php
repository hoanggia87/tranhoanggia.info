<?php
class Service_LichchieuphimController extends Zendvn_Controller_Action {

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
        $this->view->Title = 'Hoa Tiêu Online | Danh b? Website Vi?t Nam - Xem L?ch chi?u phim.';
		$this->view->headTitle($this->view->Title,true);
        $this->view->headMeta()->setName('description', 'Ti?n ích xem l?ch chi?u phim có th? giúp b?n nhanh chóng xem l?ch chi?u c?a 5 r?p l?n trên c? nu?c.');
        $service=new Service_Model_Lichchieuphim();
        $this->_arrParam['date'] = date('d-m-Y');
        $key = str_replace('-','',$this->_arrParam['date']);
        $this->view->listLocation = $service -> getListLocation();
        //$this->view->lichchieuphim=$service->getLichchieuphim($this->_arrParam);
        $cache=Zendvn_Cache::getCacheObject('cacheforever');
		if ( ($this->view->lichchieuphim = $cache->load('lichchieuphim_'.$key)) === false ) {
			
			$this->view->lichchieuphim=$service->getLichchieuphim($this->_arrParam);
			$cache->save($this->view->lichchieuphim);
		}
    }	
	public function getlichchieuphimAction() {
		$service=new Service_Model_Lichchieuphim();
        $key = str_replace('-','',$this->_arrParam['date']);
		$cache=Zendvn_Cache::getCacheObject('cacheforever');
		if ( ($this->view->lichchieuphim = $cache->load('lichchieuphim_'.$key)) === false ) {
			
			$this->view->lichchieuphim=$service->getLichchieuphim($this->_arrParam);
			$cache->save($this->view->lichchieuphim);
		}
		/*echo "<pre>";
        var_dump($this->view->lichchieuphim);
        echo "</pre>";*/
        $this->_helper->json(array('rs' => 1, 'hanoi' => $this->view->lichchieuphim['ha-noi'], 'tphcm' => $this->view->lichchieuphim['tphcm'],'danang'=>$this->view->lichchieuphim['da-nang'],'haiphong'=>$this->view->lichchieuphim['hai-phong']));
		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
	}
    public function getnewsAction()
    {
        $maxNews = 8;
        $service=new Service_Model_Lichchieuphim();
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
            $strNews = 'Không có d? li?u';
        }
        $this->_helper->json(array('rs'=>'1','info'=>$strNews));
        $this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
    }
    
	
}
