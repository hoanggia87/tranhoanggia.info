<?php
class Content_AdminContentController extends Zendvn_Controller_Action{
	
	
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
    protected $_pathContent = '/content/';
	
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
		$this->_controllerConfig  = array('imagesDir'=> PUBLIC_PATH.'/files/news/');		
		$this->_arrParam['controllerConfig'] = $this->_controllerConfig;
	}
    public function aboutAction()
    {
        $cache=Zendvn_Cache::getCacheObject('contents');
        $about = $cache->load('about');
        $this->view->Item = array();
        if($about)
        {
            $this->view->Item['about'] = $about;
        }
        $this->view->arrParam= $this->_arrParam;
        if($this->_request->isPost()){
			
			$validate = new Content_Form_ContentValidate($this->_arrParam);
			if($validate->checkError() == true){
				//Hien thi thong bao loi
				$this->view->errors = $validate->getMessageError();
				$this->view->Item = $validate->getData();
			}
            else
            {
                $this->view->Item = $this->_arrParam = $validate->getData();
                $this->_arrParam['about'] = $this->view->cmsConvertDbToView($this->_arrParam['about']);
                $cache->save($this->_arrParam['about']);
				$this->view->errors = array('success');
				
			}		
			
		}
    }
    public function noiquyAction()
    {
        $cache=Zendvn_Cache::getCacheObject('contents');
        $about = $cache->load('noiquy');
        $this->view->Item = array();
        if($about)
        {
            $this->view->Item['noiquy'] = $about;
        }
        $this->view->arrParam= $this->_arrParam;
        if($this->_request->isPost()){
			
			$validate = new Content_Form_ContentValidate($this->_arrParam);
			if($validate->checkError() == true){
				//Hien thi thong bao loi
				$this->view->errors = $validate->getMessageError();
				$this->view->Item = $validate->getData();
			}
            else
            {
                $this->view->Item = $this->_arrParam = $validate->getData();
                $this->_arrParam['noiquy'] = $this->view->cmsConvertDbToView($this->_arrParam['noiquy']);
                $cache->save($this->_arrParam['noiquy']);
				$this->view->errors = array('success');
				
			}		
			
		}
    }
    
    public function lienheAction()
    {
        $cache=Zendvn_Cache::getCacheObject('contents');
        $about = $cache->load('lienhe');
        $this->view->Item = array();
        if($about)
        {
            $this->view->Item['lienhe'] = $about;
        }
        $this->view->arrParam= $this->_arrParam;
        if($this->_request->isPost()){
			
			$validate = new Content_Form_ContentValidate($this->_arrParam);
			if($validate->checkError() == true){
				//Hien thi thong bao loi
				$this->view->errors = $validate->getMessageError();
				$this->view->Item = $validate->getData();
			}
            else
            {
                $this->view->Item = $this->_arrParam = $validate->getData();
                $this->_arrParam['lienhe'] = $this->view->cmsConvertDbToView($this->_arrParam['lienhe']);
                $cache->save($this->_arrParam['lienhe']);
				$this->view->errors = array('success');
				
			}		
			
		}
    }
    public function hoidapAction()
    {
        $cache=Zendvn_Cache::getCacheObject('contents');
        $about = $cache->load('hoidap');
        $this->view->Item = array();
        if($about)
        {
            $this->view->Item['hoidap'] = $about;
        }
        $this->view->arrParam= $this->_arrParam;
        if($this->_request->isPost()){
			
			$validate = new Content_Form_ContentValidate($this->_arrParam);
			if($validate->checkError() == true){
				//Hien thi thong bao loi
				$this->view->errors = $validate->getMessageError();
				$this->view->Item = $validate->getData();
			}
            else
            {
                $this->view->Item = $this->_arrParam = $validate->getData();
                $this->_arrParam['hoidap'] = $this->view->cmsConvertDbToView($this->_arrParam['hoidap']);
                $cache->save($this->_arrParam['hoidap']);
				$this->view->errors = array('success');
				
			}		
			
		}
    }
}




