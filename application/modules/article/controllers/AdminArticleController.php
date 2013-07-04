<?php
class Article_AdminArticleController extends Zendvn_Controller_Action{
	
	
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
		$this->_controllerConfig  = array('imagesDir'=> PUBLIC_PATH.'/files/article/');		
		$this->_arrParam['controllerConfig'] = $this->_controllerConfig;
         
	}
	
	public function indexAction(){
	  
	
		/****facebook*****/
		$page_id = '161793493989786';
		$facebook = new Zendvn_Facebook_Api(array(
		  'appId'  => '513864142013652',
		  'secret' => '37b7f1635cd80556c1c5dd8c7a406029',
		));

		// Get User ID
		$user = $facebook->getUser();

		if ($user) {
		  try {

		    $page_info = $facebook->api("/$page_id?fields=access_token");
			$this->view->access_token=$page_info['access_token'];
		    if( !empty($page_info['access_token']) ) {
		    	//post to wall
		        if($this->_arrParam['act']=='post')
		        {


	        		switch ($arrParam['type']) 
					{
			            case 'image':
			                $fbImage=PUBLIC_URL.'/files/article/images/'.$this->_arrParam['image'];
			                break;    
			            case 'video':
			                $fbImage=$this->_arrParam['image'];
			                break;
		        	}	


		        	//create link
		        	$rw=new Zendvn_View_Helper_CmsRewriteLink();
					$info['title']=$rw->noSign($this->_arrParam['title']);
					$info['id']=$this->_arrParam['id'];
					$linkDetail   = $this->view->serverUrl($this->view->url($info,'article-index-detail'));




		        			
			        $args=array(
			        			'access_token'  => $page_info['access_token'],
			        			'name' => $this->_arrParam['title'], 
								'caption' => 'shockvl.com', 
								'picture'=>$fbImage,
								'description'=>'Còn rất nhiều bài Shock từ '.DOMAIN_NAME.' :D . '.SLOGAN,
								'link'=>$linkDetail
					);

					//print_r($facebook->api("/$page_id"));
					//echo $post_id = $facebook->api("/$page_id/feed","post",$args);
			    	//$this->_helper->viewRenderer->setNoRender();
					//$this->_helper->layout->disableLayout();
			    	//return;	
		    
			        $post_id = $facebook->api("/$page_id/feed","post",$args);
			        echo json_encode(array('status'=> 1,'post_id'=>$post_id));	
			        $this->_helper->viewRenderer->setNoRender();
					$this->_helper->layout->disableLayout();
					return;
		        }
		        
		    } else {
		        $permissions = $facebook->api("/me/permissions");
		        if( !array_key_exists('publish_stream', $permissions['data'][0]) ||
		           !array_key_exists('manage_pages', $permissions['data'][0])) {
		                // We don't have one of the permissions
		                // Alert the admin or ask for the permission!
		        		
		                header( "Location: " . $facebook->getLoginUrl(array("scope" => "publish_stream, manage_pages")) );
		        		$this->_helper->viewRenderer->setNoRender();
						$this->_helper->layout->disableLayout();
		        		//return array('status'=> 0,'urlLogin'=>$facebook->getLoginUrl(array("scope" => "publish_stream, manage_pages")));
		        }
		    }
		  } catch (FacebookApiException $e) {
		    //error_log($e);
		    $user = null;
		  }
		}


		// Login or logout url will be needed depending on current user state.
		if ($user) {
		  
		} else {
		  	header( "Location: " . $facebook->getLoginUrl(array("scope" => "publish_stream, manage_pages")) );
    		$this->_helper->viewRenderer->setNoRender();
			$this->_helper->layout->disableLayout();
		}
		
		/****facebook*****/


		$this->view->Title = 'Article :: Article manager :: List';
		$this->view->headTitle($this->view->Title,true);
		
		//list website
		$objWebsite=new Article_Model_Website();
		$this->view->listWebs=$objWebsite->listItem($this->_arrParam,array('task'=>'admin-list'));

		/*
		$link='http://www.ovui.vn/legend';
		$link='http://www.haivl.com';
		$link='http://chatvl.com/hot';
		$respone  = Zendvn_Http_Request::getpageSource($link,'Mozilla/5.0 (Windows NT 6.2; rv:20.0) Gecko/20100101 Firefox/20.0');
        //$respone = $client->request('GET')->getBody();     
        //$a = new Zendvn_Parser_ParserList24h($respone);
        $parserClass = "Zendvn_Parser_ParserListovui";
        $parserClass = "Zendvn_Parser_ParserListhaivl";
        $parserClass = "Zendvn_Parser_ParserListchatvl";
		

	    $obj = new $parserClass($respone,'chatvl');
		$this->view->listItems=$obj->getList();
	    
     	*/
	}
	
	public function getListWebsiteCategoryAction(){
		//list website
		$objWebsiteCategory=new Article_Model_Websitecategory();
		$this->view->listWebCategory=$objWebsiteCategory->listItem($this->_arrParam,array('task'=>'admin-list'));

		//echo '<pre>';
		//print_r($this->view->listWebs);
		//echo '</pre>';

		//$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
	}

	public function getListAction(){
		//$link=$this->_arrParam['link'];
		
		$objWebsiteCategory=new Article_Model_Websitecategory();
		$webCatDetail=$objWebsiteCategory->getItem($this->_arrParam,array('task'=>'admin-edit'));



		$this->view->link=$webCatDetail['link'];



		if(!$this->_arrParam['page'])
			$this->_arrParam['page']=1;
		$this->view->page = $this->_arrParam['page'];
		//$link='http://www.ovui.vn/legend';
		$linkRequest=$this->view->link.'/'.$this->_arrParam['page'];
		

		$respone  = Zendvn_Http_Request::getpageSource($linkRequest,'Mozilla/5.0 (Windows NT 6.2; rv:20.0) Gecko/20100101 Firefox/20.0');

		$parserClass = 'Zendvn_Parser_ParserList'.$this->_arrParam['ppname'];
	    $obj = new $parserClass($respone,$this->_arrParam['ppname']);
	    /*
echo '<pre>';
	    print_r($this->_arrParam);
	    echo '</pre>';
	    echo '<pre>';
	    print_r($obj->getList());
	    echo '</pre>';
*/

	   

	    $this->view->listItems=$obj->getList();


	     //lấy danh sách các link ra kiểm tra xem có cái nào đã add vào chưa
	    $arrGettedURL=array();
	    foreach ($this->view->listItems as $key => $value) {
	    	$arrGettedURL[]=$value['url'];    
	    } 
	    
		$tblArticle=new Article_Model_Article();
		$this->view->listLinkExist=$tblArticle->listItem($arrGettedURL,array('task'=>'get-list-url-exist'));


	    
    	//$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();	
	    
	    //$this->_helper->viewRenderer->setNoRender();
		//$this->_helper->layout->disableLayout();
		//return;
	}

	public function getDetailAction(){
		$link=$this->_arrParam['link'];

		$respone  = Zendvn_Http_Request::getpageSource($link,'Mozilla/5.0 (Windows NT 6.2; rv:20.0) Gecko/20100101 Firefox/20.0');

		$parserClass = 'Zendvn_Parser_ParserDetail'.$this->_arrParam['ppname'];
	    $obj = new $parserClass($respone,$this->_arrParam['ppname']);
	    $this->view->listItems=$obj->getList();
	}
	
	public function postToFanpage($page_id = '161793493989786',$arrParam=null)
	{
		/**
		 * Edit the Page ID you are targeting
		 * And the message for your fans!
		 */
		//$page_id = '161793493989786';
		//$message = "http://www.shockvl.com/tuong-lai-tuoi-dep--157.html";


		/**
		 * This code is just a snippet of the example.php script
		 * from the PHP-SDK <http://github.com/facebook/php-sdk/blob/master/examples/example.php>
		 */
		//require '../src/facebook.php';

		// Create our Application instance (replace this with your appId and secret).

		$facebook = new Zendvn_Facebook_Api(array(
		  'appId'  => '513864142013652',
		  'secret' => '37b7f1635cd80556c1c5dd8c7a406029',
		));

		// Get User ID
		$user = $facebook->getUser();

		if ($user) {
		  try {
		    $page_info = $facebook->api("/$page_id?fields=access_token");

		    if( !empty($page_info['access_token']) ) {
		        
		        $args = array(
		            'access_token'  => $page_info['access_token']		            
		        );
				
				$args=array_merge($args,$arrParam);
		        $post_id = $facebook->api("/$page_id/feed","post",$args);
		        return array('status'=> 2,'post_id'=>$post_id);
		    } else {
		        $permissions = $facebook->api("/me/permissions");
		        if( !array_key_exists('publish_stream', $permissions['data'][0]) ||
		           !array_key_exists('manage_pages', $permissions['data'][0])) {
		                // We don't have one of the permissions
		                // Alert the admin or ask for the permission!
		                //header( "Location: " . $facebook->getLoginUrl(array("scope" => "publish_stream, manage_pages")) );
		        		return array('status'=> 0,'urlLogin'=>$facebook->getLoginUrl(array("scope" => "publish_stream, manage_pages")));
		        }
		    }
		  } catch (FacebookApiException $e) {
		    //error_log($e);
		    $user = null;
		  }
		}


		// Login or logout url will be needed depending on current user state.
		if ($user) {
		  $logoutUrl = $facebook->getLogoutUrl();
		  return array('status'=> 1,'urlLogout'=>$logoutUrl,'access_token'=>$page_info['access_token']);
		} else {
		  $loginUrl = $facebook->getLoginUrl(array('scope'=>'manage_pages,publish_stream'));		  
		  return array('status'=> 0,'urlLogin'=>$loginUrl);
		}
		// ... rest of your code

		

		//$this->_helper->viewRenderer->setNoRender();
		//$this->_helper->layout->disableLayout();
	}
	public function saveDetailAction(){
		$link=$this->_arrParam['link'];
		$page_id=$this->_arrParam['page_id'];
		$title=$this->_arrParam['title'];
		
		$respone  = Zendvn_Http_Request::getpageSource($link,'Mozilla/5.0 (Windows NT 6.2; rv:20.0) Gecko/20100101 Firefox/20.0');
		$parserClass = 'Zendvn_Parser_ParserDetail'.$this->_arrParam['ppname'];
	    
		$obj = new $parserClass($respone,$this->_arrParam['ppname']);
	    $arrArticleInfo=$obj->getArticle();

	    //copy hình sang host của mình nếu là image
	    if($arrArticleInfo['type']=='image')
	    {
	    	$url= $arrArticleInfo['image'];
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
			
	    }
	    if($arrArticleInfo['type']=='video')
	    {
	    	$fn=$arrArticleInfo['image'];
	    }

	   
	    $arrParam=array(
	    		'page_id'=>$page_id,
	    		'link'=>trim($link),
	    		'status'=>1,	    		
	    		'title'=>trim($title),
	    		'description'=>$arrArticleInfo['description'],
	    		'image'=>$fn,
	    		'video'=>$arrArticleInfo['video'],
	    		'type'=>$arrArticleInfo['type'],
	    		'is_hot'=>$this->_arrParam['ishot'],
	    		'is_adult'=>$this->_arrParam['isadult'],
	    		'cat_id'=>1// mặc định của phần này là 1
	    	);
	    $tblArticle=new Article_Model_Article();
		$id= $tblArticle->saveItem($arrParam, array('task'=>'admin-add'));
		if($id)
		{
			//echo json_encode(array('status'=>1,'id'=>$id,'fbpost'=>$arrResultPost)) ;
			//create link
        	$rw=new Zendvn_View_Helper_CmsRewriteLink();
			$info['title']=$rw->noSign($title);
			$info['id']=$id;
			$linkDetail   = $this->view->serverUrl($this->view->url($info,'article-index-detail'));


			switch ($arrArticleInfo['type']) 
			{
	            case 'image':
	                $fbImage=PUBLIC_URL.'/files/article/images/'.$fn;
	                break;    
	            case 'video':
	                $fbImage=$fn;
	                break;
        	}	
			echo json_encode(array('status'=>1,
				'id'=>$id,
				'title'=>$title,
				'image'=>$fbImage,
				'type'=>$arrArticleInfo['type'],
				'link'=>$linkDetail
				)) ;
		}
		else
		{
			echo json_encode(array('status'=>0,'id'=>0)) ;	
		}
		
	    $this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
	}
}




