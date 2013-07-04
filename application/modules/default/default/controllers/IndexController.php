<?php
class IndexController extends Zendvn_Controller_Action {

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
		
	public function indexAction() 
    {	
        $this->view->Title = 'HoatieuOnline | Danh bạ website';
		$this->view->headTitle($this->view->Title,true);
        $this->view->headMeta()->setName('description', 'Danh bạ website');
        $tblWeblink = new Weblink_Model_Weblink();
        //Lay danh sach hot site
        $arrListHotSite = $tblWeblink -> listItem(array(),array('task' => 'front-hotstie'));
        $this->view->arrListHotSite = $arrListHotSite;
        //Lay danh sach category
        $tblWeblinkCategory = new Weblink_Model_Weblinkcategory();
        $arrListCategory = $tblWeblinkCategory -> listItem(array(),array('task'=>'front-list-menu'));
        $arrInfoLink = array();
        $arrListWebLink = $tblWeblink -> listItem(array(),array('task'=>'front-homesite'));
        foreach($arrListWebLink as $lkey => $lITems)
        {
            $arrInfoLink[$lITems['cat_id']][$lkey] = $lITems;
        }
        if($arrListCategory)
        {
            $arrInfoCat = array();
            $arrNameCat = array();
            foreach($arrListCategory as $key => $iTems)
            {
                if(!$iTems['parents'])
                {
                    
                    $arrInfoCat[$iTems['id']] = array();
                    $arrNameCat[$iTems['id']] = $iTems['name'];
                }
                else
                {
                    if(count($arrInfoCat[$iTems['parents']]) == 0)
                    {
                        $arrInfoCat[$iTems['parents']] = $arrInfoLink[$iTems['id']];
                    }
                    else
                    {
                        if(count($arrInfoLink[$iTems['id']])>0)
                        {
                            //echo 'test<br>';
                            $tmpArr= array();
                            $tmpArr = array_merge($arrInfoCat[$iTems['parents']],$arrInfoLink[$iTems['id']]);
                            $arrInfoCat[$iTems['parents']] = $tmpArr; 
                        }
                    }                  
                }
            }
            /*echo '<pre>';
            var_dump($arrInfoCat);
            echo "</pre>";*/
            //$arrListHotSite = $tblWeblink -> listItem(array(),array('task' => 'front-hotstie'));
        }
        $this->view->arrInfoCat = $arrInfoCat;
        $this->view->arrNameCat = $arrNameCat;
	                
	}
	public function detailAction() 
    {	
        $tblWeblinkCategory = new Weblink_Model_Weblinkcategory();
        $tblWeblink = new Weblink_Model_Weblink();
        $this->view->Title = 'HoatieuOnline | Danh bạ website';
		$this->view->headTitle($this->view->Title,true);
        $this->view->headMeta()->setName('description', 'Danh bạ website');
        $arrInfoCat = $tblWeblinkCategory -> listItem($this->_arrParam,array('task'=>'front-info-cat'));
        $this->view->arrInfoCat = $arrInfoCat;
        $arrListCategory = $tblWeblinkCategory -> listItem($this->_arrParam,array('task'=>'front-list-by-cat'));
        /*echo '<pre>';
        var_dump($arrInfoCat);
        echo "</pre>";*/
        $this->view->arrListCategory = $arrListCategory;
        $strListSubCat ='';
        if($arrListCategory)
        {
            foreach($arrListCategory as $key => $iTems)
            {
                $strListSubCat .= '\''.$iTems['id'].'\',';
            }
        }
        $strListSubCat = substr($strListSubCat,0,strlen($strListSubCat)-1);
        $this->_arrParam['list-sub'] = $strListSubCat;
        $arrListLink = $tblWeblink->listItem($this->_arrParam,array('task'=>'front-list-sub'));
        $arrCustomLink = array();
        if($arrListLink)
        {
            foreach($arrListLink as $key => $iTems)
            {
                if(!is_array($arrCustomLink[$iTems['cat_id']]))
                {
                    $arrCustomLink[$iTems['cat_id']] = array();
                    array_push($arrCustomLink[$iTems['cat_id']],$iTems);
                }
                else
                {
                    array_push($arrCustomLink[$iTems['cat_id']],$iTems);
                }
            }
        }
        $this->view->arrCustomLink = $arrCustomLink;
        /*echo '<pre>';
        var_dump($arrListLink);
        echo "</pre>";*/
	                
	}
	
		
}
