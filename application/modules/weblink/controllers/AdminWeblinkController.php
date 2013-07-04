<?php
class Weblink_AdminWeblinkController extends Zendvn_Controller_Action{
	
	
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
	
	public function init(){
		//Mang tham so nhan duoc o moi Action
		$this->_arrParam = $this->_request->getParams();
		
		//Duong dan cua Controller
		$this->_currentController = '/' . $this->_arrParam['module'] 
									 . '/' . $this->_arrParam['controller'];
		
		//Duong dan cua Action chinh		
		$this->_actionMain = '/' . $this->_arrParam['module'] 
							 . '/' . $this->_arrParam['controller']	. '/index';	

							 
		
		
		//Luu cac du lieu filter vao SESSION
		//Dat ten SESSION
		$this->_namespace = $this->_arrParam['module'] . '-' . $this->_arrParam['controller'];
		$ssFilter = new Zend_Session_Namespace($this->_namespace);
		//$ssFilter->unsetAll();
		if(empty($ssFilter->col)){
			$ssFilter->keywords 	= '';
			$ssFilter->col 			= 'n.id';
			$ssFilter->order 		= 'DESC';
			$ssFilter->group_id		= 0;
		}
		$this->_arrParam['ssFilter']['keywords'] 	= $ssFilter->keywords;
		$this->_arrParam['ssFilter']['col'] 		= $ssFilter->col;
		$this->_arrParam['ssFilter']['order'] 		= $ssFilter->order;
		$this->_arrParam['ssFilter']['cat_id'] 		= $ssFilter->cat_id;
        	
		$this->_arrParam['ssFilter']['date_from'] 	= $ssFilter->date_from;
		$this->_arrParam['ssFilter']['date_to'] 	= $ssFilter->date_to;
		
		//set lai pagging
		if($ssFilter->pagging)
		{
			$this->_paginator['itemCountPerPage']=$ssFilter->pagging;
			$this->_arrParam['ssFilter']['pagging']=$ssFilter->pagging;
			
		}
		
		//phan trang		
		$this->_paginator['currentPage'] = $this->_request->getParam('page',1);
		$this->_arrParam['paginator'] = $this->_paginator;
		
		//Truyen ra view
		$this->view->arrParam = $this->_arrParam;
		$this->view->currentController = $this->_currentController;
		$this->view->actionMain = $this->_actionMain;
		
		$template_path = TEMPLATE_PATH . "/admin/system";
		$this->loadTemplate($template_path,'template.ini','template');
		
		//khai bao thu muc upload 	
		$this->_controllerConfig  = array('imagesDir'=> PUBLIC_PATH.'/files/weblink/');		
		$this->_arrParam['controllerConfig'] = $this->_controllerConfig;
         
	}
	
	public function indexAction(){
	  
		//iploadify
		//$this->view->headLink()->appendStylesheet($this->view->baseUrl("/public/scripts/uploadify/uploadify.css",'screen'));
		//$this->view->headScript()->appendFile($this->view->baseUrl("/public/scripts/uploadify/swfobject.js",'text/javascript'));
		//$this->view->headScript()->appendFile($this->view->baseUrl("/public/scripts/uploadify/jquery.uploadify.v2.1.4.min.js",'text/javascript'));
		//light box
		//$this->view->headLink()->appendStylesheet($this->view->baseUrl(TEMPLATE_URL . "/admin/system/js/jquery-lightbox/styles/jquery.lightbox.min.css",'screen'));
		//$this->view->headScript()->appendFile($this->view->baseUrl(TEMPLATE_URL . "/admin/system/js/jquery-lightbox/scripts/jquery.color.min.js",'text/javascript'));
		//$this->view->headScript()->appendFile($this->view->baseUrl(TEMPLATE_URL . "/admin/system/js/jquery-lightbox/scripts/jquery.lightbox.min.js",'text/javascript'));
		
		
		$this->view->Title = 'Web\'s link :: Web\'s link manager :: List';
		$this->view->headTitle($this->view->Title,true);
		        
		$tblWeblink = new Weblink_Model_Weblink();
		$this->view->Items = $tblWeblink->listItem($this->_arrParam, array('task'=>'admin-list'));
			
		$totalItem  = $tblWeblink->countItem($this->_arrParam);
		
		$paginator = new Zendvn_Paginator();
		$this->view->panigator = $paginator->createPaginator($totalItem,$this->_paginator);
		//get listCat parent
		$tblCategory 		  = new Weblink_Model_Weblinkcategory();		
		$this->view->listCat  = $tblCategory->listItem($this->_arrParam,array('task'=>'admin-list-select','root'=>'true'));
        
     
	}
	public function uploadifyAction(){
		try{
			if (!empty($_FILES)) {
				$uploadDir=$this->_controllerConfig['imagesDir'];
				$upload = new Zendvn_File_Upload();	
				$fnimage	=	 $upload->upload('full_image',$uploadDir.'full_images/',array('task'=>'rename'));
				
				//luu vao csdl				
					$this->_arrParam['title']		=	$_FILES['full_image']['name'];								
					$this->_arrParam['cat_id']		=	$this->_arrParam['cat_id'];                                
					$this->_arrParam['full_image']	=	$fnimage;
	                
	                if(!$this->_arrParam['cat_id'])
	                {
	                    echo json_encode(array('error'=>1,'html'=>'Vui lòng chọn category.'));
	                                    
	                }                
	                else
	                {
	                	if($fnimage)
	                	{
	                		$tblWeblink=new Weblink_Model_Weblink();
		    				$tblWeblink->saveItem($this->_arrParam,array('task'=>'admin-add'));			
		    				
		    				//Resize
		    				$linkImage=$uploadDir . 'full_images/'. $fnimage;
		    				$img = Zendvn_File_Image::create($linkImage);
		    				$img->resize(500,500);
		    				$img->save($uploadDir . 'thumb_images_500x500/' . $fnimage);
		    				//Resize
		    				$linkImage=$uploadDir . 'full_images/'. $fnimage;
		    				$img = Zendvn_File_Image::create($linkImage);
		    				$img->resize(293,145);
							$img->save($uploadDir . 'thumb_images_293x145/' . $fnimage);					
		    				//crop image
		    				$linkImage=$uploadDir . 'thumb_images_500x500/'. $fnimage;
		    				$img = Zendvn_File_Image::create($linkImage);
		    					//lay chieu dai va chieu cao cua anh
		    				$imgSize=getimagesize($linkImage);
		        			$cropImg = $imgSize[0];
		    				if($cropImg>$imgSize[1])
		    					$cropImg=$imgSize[1];				
		    				$img->cropFromCenter($cropImg,$cropImg)
		    					->resize(75,75);
		    				$img->save($uploadDir . 'crop_images/' . $fnimage);
		    				echo $fnimage; 
	                	}	                                         
	                }                                
					
			}	
		}catch(Zend_Exception $e){}	
		$this->getResponse()
     				->setHeader('Connection', 'close');						
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();	
	}
		
	public function filterAction(){
		$ssFilter = new Zend_Session_Namespace($this->_namespace);
		
		if($this->_arrParam['type'] == 'search'){
			if($this->_arrParam['key'] == 1){
				$ssFilter->keywords = trim($this->_arrParam['keywords']);
			}else{
				$ssFilter->keywords = '';
			}
		}
		
		if($this->_arrParam['type'] == 'category'){
			$ssFilter->cat_id 		= $this->_arrParam['cat_id'];				
		}
       
		if($this->_arrParam['type'] == 'date'){			
				
			if($this->_arrParam['key'] == 1){
				$ssFilter->date_from 	= $this->_arrParam['date_from'];
				$ssFilter->date_to 		= $this->_arrParam['date_to'];	
			}else{
				$ssFilter->date_from 	= '';
				$ssFilter->date_to 		= '';
			}	
		}
		if($this->_arrParam['type'] == 'order'){
			$ssFilter->col = $this->_arrParam['col'];
			$ssFilter->order = $this->_arrParam['by'];
		}
		if($this->_arrParam['type'] == 'pagging'){
			$ssFilter->pagging = $this->_arrParam['pagging'];
			
		}
		$this->_redirect($this->_actionMain);
		$this->_helper->viewRenderer->setNoRender();
	}
	
	
	public function addAction(){
		$this->view->Title = 'Web\'s link :: Web\'s link manager :: Add';
		$this->view->headTitle($this->view->Title,true);
        
        $tblCategory 		= new Weblink_Model_Weblinkcategory();
        
        $tblWeblink = new Weblink_Model_Weblink();
        
		if($this->_request->isPost()){
			$validate = new Weblink_Form_WeblinkValidate($this->_arrParam,'add');
			if($validate->checkError() == true){
				//Hien thi thong bao loi
				$this->view->errors = $validate->getMessageError();
				$this->view->Item = $validate->getData();
			}else{
				//Luu vao database
				$rs			= $validate->getData();			
				$tblWeblink->saveItem($rs,array('task'=>'admin-add'));
				$this->view->errors = array('success');
			}
		}
       
		           				
		//get listCat parent    				
		$this->view->listCat= $tblCategory->listItem($this->_arrParam,array('task'=>'admin-list-select','root'=>'false'));
            
	}
	
	public function infoAction(){
		$this->view->Title = 'Web\'s link :: Web\'s link manager :: Information';
		$this->view->headTitle($this->view->Title,true);
		
		$tblWeblink = new Weblink_Model_Weblink();
		$this->view->Item = $tblWeblink->getItem($this->_arrParam,array('task'=>'admin-info'));
		
	}
	
	public function editAction(){
		$this->view->Title = 'Web\'s link :: Web\'s link manager :: Edit';
		$this->view->headTitle($this->view->Title,true);
        
        $tblCategory 		= new Weblink_Model_Weblinkcategory();
        
        $tblWeblink = new Weblink_Model_Weblink();
        
		if($this->_request->isPost()){
			$validate = new Weblink_Form_WeblinkValidate($this->_arrParam,'edit');
			if($validate->checkError() == true){
				//Hien thi thong bao loi
				$this->view->errors = $validate->getMessageError();
				$this->view->Item = $validate->getData();
			}else{
				//Luu vao database
				$rs			= $validate->getData();			
				$tblWeblink->saveItem($rs,array('task'=>'admin-edit'));
			}
		}
       
		$this->view->Item = $tblWeblink->getItem($this->_arrParam,array('task'=>'admin-edit'));
		            				
		//get listCat parent    				
		$this->view->listCat= $tblCategory->listItem($this->_arrParam,array('task'=>'admin-list-select','root'=>'false'));
            				
		
	}
    
   

	public function deleteAction(){
		$this->view->Title = 'Web\'s link :: Web\'s link manager :: Delete';
		$this->view->headTitle($this->view->Title,true);
		if($this->_request->isPost()){
			$tblWeblink = new Weblink_Model_Weblink();
			$tblWeblink->deleteItem($this->_arrParam,array('task'=>'admin-delete'));
			$this->_redirect($this->_actionMain);
		}
	}

	public function statusAction(){
		$tblWeblink = new Weblink_Model_Weblink();		
		$tblWeblink->changeStatus($this->_arrParam);
		$this->_redirect($this->_actionMain . '/page/'.$this->_request->getParam('page',1));	
		$this->_helper->viewRenderer->setNoRender();
	}
	
	public function ishomeAction(){
		$tblWeblink = new Weblink_Model_Weblink();		
		$tblWeblink->changeIsHome($this->_arrParam);
		$this->_redirect($this->_actionMain . '/page/'.$this->_request->getParam('page',1));	
		$this->_helper->viewRenderer->setNoRender();
	}
    
    public function ishomemobileAction(){
		$tblWeblink = new Weblink_Model_Weblink();		
		$tblWeblink->changeIsHomeMobile($this->_arrParam);
		$this->_redirect($this->_actionMain . '/page/'.$this->_request->getParam('page',1));	
		$this->_helper->viewRenderer->setNoRender();
	}

	public function ishotAction(){
		$tblWeblink = new Weblink_Model_Weblink();		
		$tblWeblink->changeIsHot($this->_arrParam);
		$this->_redirect($this->_actionMain . '/page/'.$this->_request->getParam('page',1));	
		$this->_helper->viewRenderer->setNoRender();
	}

	public function multiDeleteAction(){
		
		if($this->_request->isPost()){
			$tblWeblink = new Weblink_Model_Weblink();
			$tblWeblink->deleteItem($this->_arrParam,array('task'=>'admin-multi-delete'));
			$this->_redirect($this->_actionMain);
		}
		$this->_helper->viewRenderer->setNoRender();
	}
	
	public function sortAction(){
		if($this->_request->isPost()){
			$tblUser = new Weblink_Model_Weblink();
			$tblUser->sortItem($this->_arrParam);
			$this->_redirect($this->_actionMain);
		}
		
		$this->_helper->viewRenderer->setNoRender();
	}
	
	public function getListDataAction()
	{

  		
            $client = new Zend_Http_Client();
		    $client->setUri('http://laban.vn/cate?catid='.$this->_arrParam['cid']);
		    $client->setConfig(array(
		      'maxredirects' => 2,
		      'timeout'      => 30,   
		   
		    ));        
	        $client->setHeaders(array(
	       		'Host'    => 'laban.vn'
	        
	        ));
	        $respone = $client->request('GET')->getBody();
	        
	        $document=Zendvn_Dom_Process::newDocumentHTML($respone);

	       	//lấy tên category cha
	       	$catinfo['name']=pq('.block_top .info h2')->html();
	       	$catinfo['description']=pq('.block_top .info p')->html();
	       	//lưu thông tin cat cha
          	$objCat=new Weblink_Model_Weblinkcategory();
          	$objItem=new Weblink_Model_Weblink();

          	$idParent=$objCat->saveItem($catinfo,array('task'=>'admin-add'));

	        $arr_dom = array();
	        $cat_dom = pq('div#forum');

	        $arrResult=array();
	        $i=$j=0;
	        foreach($cat_dom as $cat)
	        {

	        	$title = pq($cat)->find('h2')->slice(0,1);
	        	$arrResult[$i]['name']=pq($title)->find('a')->html();
	        	$arrResult[$i]['parents']=$idParent;

	        	$idCat=$objCat->saveItem($arrResult[$i],array('task'=>'admin-add'));

	        	$items = pq($cat)->find('ul a');
	        	
	        	foreach($items as $item)
	        	{
	        		$arrTemp=array();
	        		$arrTemp['title']=pq($item)->find('.info_web h3')->html();
	        		$arrTemp['summary']=pq($item)->find('.info_web p')->html();
	        		$arrTemp['full_image']=preg_replace(array('/background-image:url\(/','/\)/'), array('',''), pq($item)->find('.ico_web img')->attr('style'));
	        		$arrTemp['link']=pq($item)->attr('href');
	        		$arrTemp['cat_id']=$idCat;

	        		if(	!$arrTemp['title'] && 
	        			!$arrTemp['summary'] &&
	        			!$arrTemp['full_image'] &&
	        			!$arrTemp['link'])
	        		{

	        		}
	        		else
	        		{
	        			$arrResult[$i]['item'][$j]=$arrTemp;
	        			$idCategory=$objItem->saveItem($arrTemp,array('task'=>'admin-add'));

	        			copy($arrTemp['full_image'], $this->_controllerConfig['imagesDir'].'logo/logo_'.$idCategory.'.jpg');
	        			$j++;
	        		}

	        		
	        	}
	        	$j=0;
				$i++;
	         	
	        }
	        
	    echo $this->_arrParam['cid'];

		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
	}

	
}




