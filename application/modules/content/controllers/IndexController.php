<?php
class Content_IndexController extends Zendvn_Controller_Action {

	//Mang tham so nhan duoc o moi Action
	protected $_arrParam;
	
	//Duong dan cua Controller
	protected $_currentController;
	
	//Duong dan cua Action chinh
	protected $_actionMain;
	
	//Thong so phan trang
	protected $_paginator = array(
									'itemCountPerPage' => 10,
									'pageRange' => 3,
								  );
	protected $_controllerConfig; 
	
	protected $_namespace;
	protected $_objCaptcha;
	
	public function init(){
		//parent::init();
	
		//Mang tham so nhan duoc o moi Action
		$this->_arrParam = $this->_request->getParams();
		$this->_objCaptcha = new Zendvn_Captcha_Image();
		//Duong dan cua Controller
		$this->_currentController = '/' . $this->_arrParam['module'] 
									 . '/' . $this->_arrParam['controller'];
		
		//Duong dan cua Action chinh		
		$this->_actionMain = '/' . $this->_arrParam['module'] 
							 . '/' . $this->_arrParam['controller']	. '/index';
		//$template_path = TEMPLATE_PATH . "/public/gac";
		//$this->loadTemplate($template_path,'template.ini','template');
		
		//Truyen ra view
		$this->view->arrParam = $this->_arrParam;
		$this->view->currentController = $this->_currentController;
		$this->view->actionMain = $this->_actionMain;
		
		//phan trang
		$this->_paginator['currentPage'] = $this->_request->getParam('page',1);
		$this->_arrParam['paginator'] = $this->_paginator;
	}
		
	public function aboutAction()
    {
        $this->view->Title = 'Shock mà vui | Giới thiệu về website ShockVL.';
		$this->view->headTitle($this->view->Title,true);
        $this->view->headMeta()->setName('description', 'Chuyên trang giải trí, hình ảnh, video vui và shock, các bài báo hay, tâm sự bạn trẻ và những câu chuyện cười');
        $cache=Zendvn_Cache::getCacheObject('contents');
        $about = $cache->load('about');
        //echo $about;
        $this->view->Item = array();
        if($about)
        {
            $this->view->Item['about'] = $about;
        }
    }
    public function noiquyAction()
    {
        $this->view->Title = 'ShockVL | Nội quy đăng bài lên ShockVL';
		$this->view->headTitle($this->view->Title,true);
        $this->view->headMeta()->setName('description', 'Những quy định về nội dung đăng tải, cách tham gia, quản lý bài viết và báo cáo sai phạm của thành viên.');
        $cache=Zendvn_Cache::getCacheObject('contents');
        $about = $cache->load('noiquy');
        $this->view->Item = array();
        if($about)
        {
            $this->view->Item['noiquy'] = $about;
        }
    }
    public function lienheAction()
    {
        $this->view->Title = 'ShockVL | Liên hệ với chúng tôi';
		$this->view->headTitle($this->view->Title,true);
        $this->view->headMeta()->setName('description', 'Liên hệ hợp tác, hỏi đáp, chia sẻ thông tin và góp ý cho ShockVL.');
        $cache=Zendvn_Cache::getCacheObject('contents');
        $about = $cache->load('lienhe');
        $this->view->Item = array();
        if($about)
        {
            $this->view->Item['lienhe'] = $about;
        }
    }
    public function hoidapAction()
    {
        $this->view->Title = 'ShockVL | Những câu hỏi thường gặp';
		$this->view->headTitle($this->view->Title,true);
        $this->view->headMeta()->setName('description', 'Những câu hỏi thường gặp khi tham gia vào cộng đồng ShockVL. Bạn sẽ nhận được những hướng dẫn chi tiết nhất.');
        $cache=Zendvn_Cache::getCacheObject('contents');
        $about = $cache->load('hoidap');
        $this->view->Item = array();
        if($about)
        {
            $this->view->Item['hoidap'] = $about;
        }
    }	
}
