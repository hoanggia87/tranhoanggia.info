<?php
class Article_PostController extends Zendvn_Controller_Action {

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
	public function imageAction()
    {
        $auth = Zend_Auth::getInstance();
        $this->view->infoAuth = $auth->getIdentity();
        $this->view->arrParam = $this->_arrParam;
        if($this->_request->isPost())
        {
			
			$validate = new Article_Form_PostValidate($this->_arrParam,'image');
			if($validate->checkError() == true){
				//Hien thi thong bao loi
				$this->view->errors = $validate->getMessageError();
				$this->view->Item = $validate->getData();
			}else{
                $rs				= $validate->getData();
                $tblPost 	= new Article_Model_Post();	
                //copy hình sang host của mình nếu là image
        	    if($this->_arrParam['t']=='link')
        	    {
        	    	$url= $this->_arrParam['url'];
        			$temp=explode("/", $url);
        	  
        	    	$tempurl=end($temp);
        	    	$fileName=Zendvn_File_Upload::getName($tempurl);
        	    	$fileExt=Zendvn_File_Upload::getExt($tempurl);
        			
        			$fn=$fileName.'.'.$fileExt;
        			$myUrl=FILES_PATH.'/article/images/';
        			$if=1;
        	    	while(file_exists($myUrl.$fn))// nếu file này đã tồn tại thì mình đổi tên
        	    		$fn=$fileName.'_'.($if++).'.'.$fileExt;	    		
        		    //copy hình đó trên mạng về
        		    copy($url, $myUrl.$fn);	
        
        		    //lấy kích thước của hình
        		    $size=getimagesize($myUrl.$fn);
        			
        		    //xử lý chữ ký dưới footer của hình
        		    //tạo hình
        			//$fn=$shareKey.'.png';
        			
        		    if(strtolower($fileExt)=='png')
        			{
        				$extimage='png';  
        			}
        			elseif (strtolower ($fileExt)=='gif') {
        				$extimage='gif';   
        			}
        			else//jgp
        			{
        				$extimage='jpeg';  
        			}
        
        			//nếu là hình gif thì ko gắn cái này vào
        			//$this->_arrParam['iseditwm']= 'add';
        			if($extimage!='gif')
        			{
        
        				$functioncreate='imagecreatefrom'.$extimage;
        				$img_bg = $functioncreate($myUrl.$fn);
        
        				$posSizeY=$size[1];
        				if($this->_arrParam['iseditwm']=='add')
        				{
        					$posSizeY=$size[1]+30;
        					$img_bg_bg = imagecreatetruecolor($size[0], $posSizeY);
        					imagecopymerge($img_bg_bg, $img_bg,0, 0, 0, 0, $size[0], $size[1], 100);
        					$img_bg=$img_bg_bg;
        				}
        				
        				$font = FILES_PATH.'/fonts/UVNNguyenDu.TTF';
        				$color_text = imagecolorallocate($img_bg,102,102,102);
        				$white = imagecolorallocate($img_bg, 204, 204, 204);
        
        			
        
        				// Draw a white rectangle
        				imagefilledrectangle($img_bg, 0, ($posSizeY-30), $size[0], $posSizeY, $white);
        				imagettftext($img_bg,18,0,10,($posSizeY-7),$color_text,$font,SLOGAN);
        
        				$functioncreate='image'.$extimage;			
        				$functioncreate($img_bg,$myUrl.$fn);  
        				
        				
        				imagedestroy($img_bg);
        	
        			}
        			$rs['image'] = $fn;
        	    }
				$postID = $tblPost->saveItem($rs,array('task'=>'user-add-image'));
				$this->view->errors = array('<p class="form-message error" style="background: #0d6934;">Bạn đã chia sẻ ảnh thành công. Chúc mừng bạn!</p>');
                //$this->view->errors = array($fn);	
			}		
			
		}
    }
    public function videoAction()
    {
        $tblPost 	= new Article_Model_Post();	
        $auth = Zend_Auth::getInstance();
        $this->view->infoAuth = $auth->getIdentity();
        $this->view->arrParam = $this->_arrParam;
        if($this->_request->isPost())
        {
			
			$validate = new Article_Form_PostValidate($this->_arrParam,'video');
			if($validate->checkError() == true){
				//Hien thi thong bao loi
				$this->view->errors = $validate->getMessageError();
				$this->view->Item = $validate->getData();
			}else{
                $rs				= $validate->getData();
                $videoID = $tblPost->youtube_id_from_url($this->_arrParam['url']);
                if($videoID)
                {
                    $rs['video'] = 'http://www.youtube.com/embed/'.$videoID;
                    $rs['image'] = 'https://i3.ytimg.com/vi/'.$videoID.'/hqdefault.jpg';
                    $postID = $tblPost->saveItem($rs,array('task'=>'user-add-video'));
                    $this->view->errors = array('<p class="form-message error" style="background: #0d6934;">Bạn đã chia sẻ video thành công. Chúc mừng bạn!</p>');
                }
                else
                {
                    $this->view->errors = array('<p class="form-message error"">Không tồn tại link Video, vui lòng kiểm tra lại!</p>');
                }
			}		
			
		}
    }
    public function truyencuoiAction()
    {
        $tblPost 	= new Article_Model_Post();	
        $auth = Zend_Auth::getInstance();
        $this->view->infoAuth = $auth->getIdentity();
        $this->view->arrParam = $this->_arrParam;
        if($this->_request->isPost())
        {
			
			$validate = new Article_Form_PostValidate($this->_arrParam,'truyencuoi');
			if($validate->checkError() == true){
				//Hien thi thong bao loi
				$this->view->errors = $validate->getMessageError();
				$this->view->Item = $validate->getData();
			}else{
                $rs				= $validate->getData();
                $postID = $tblPost->saveItem($rs,array('task'=>'user-add-truyencuoi'));
                $this->view->errors = array('<p class="form-message error" style="background: #0d6934;">Bạn đã chia sẻ truyện cười thành công. Chúc mừng bạn!</p>');
			}		
			
		}
    }
    public function baohayAction()
    {
        $tblPost 	= new Article_Model_Post();	
        $auth = Zend_Auth::getInstance();
        $this->view->infoAuth = $auth->getIdentity();
        $this->view->arrParam = $this->_arrParam;
        if($this->_request->isPost())
        {
			
			$validate = new Article_Form_PostValidate($this->_arrParam,'baohay');
			if($validate->checkError() == true){
				//Hien thi thong bao loi
				$this->view->errors = $validate->getMessageError();
				$this->view->Item = $validate->getData();
			}else{
                $rs				= $validate->getData();
                $postID = $tblPost->saveItem($rs,array('task'=>'user-add-baohay'));
                $this->view->errors = array('<p class="form-message error" style="background: #0d6934;">Bạn đã chia sẻ báo hay thành công. Chúc mừng bạn!</p>');
			}		
			
		}
    }
    public function tamsuAction()
    {
        $tblPost 	= new Article_Model_Post();	
        $auth = Zend_Auth::getInstance();
        $this->view->infoAuth = $auth->getIdentity();
        $this->view->arrParam = $this->_arrParam;
        if($this->_request->isPost())
        {
			
			$validate = new Article_Form_PostValidate($this->_arrParam,'tamsu');
			if($validate->checkError() == true){
				//Hien thi thong bao loi
				$this->view->errors = $validate->getMessageError();
				$this->view->Item = $validate->getData();
			}else{
                $rs				= $validate->getData();
                $postID = $tblPost->saveItem($rs,array('task'=>'user-add-tamsu'));
                $this->view->errors = array('<p class="form-message error" style="background: #0d6934;">Bạn đã chia sẻ tâm sự thành công. Chúc mừng bạn!</p>');
			}		
			
		}
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
		foreach ($this->view->listArticle as $key => $value) {
			$arrUserID[]=$value['user_id'];
		}
		$user = Zendvn_User_User::getInstance();
		$arrTemp=$user->getUserByMultiTable($arrUserID,'user_id,full_name,avatar');

		
		foreach ($arrTemp as $key => $value) {
			$this->view->arrUserInfo[$value['user_id']]=$value;
		}

	}

	public function detailAction()
	{
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

		
	}
}
