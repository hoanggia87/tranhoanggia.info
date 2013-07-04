<?php
class Product_AdminProductController extends Zendvn_Controller_Action{
	
	
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
		
		}
		$this->_arrParam['ssFilter']['keywords'] 	= $ssFilter->keywords;
		$this->_arrParam['ssFilter']['col'] 		= $ssFilter->col;
		$this->_arrParam['ssFilter']['order'] 		= $ssFilter->order;
		$this->_arrParam['ssFilter']['cat_id'] 		= $ssFilter->cat_id;
        $this->_arrParam['ssFilter']['district_id'] = $ssFilter->district_id;		
		$this->_arrParam['ssFilter']['date_from'] 	= $ssFilter->date_from;
		$this->_arrParam['ssFilter']['date_to'] 	= $ssFilter->date_to;
		
		//set lai pagging
		if(!empty($ssFilter->pagging)){
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
		
		//khai bao thu muc upload useravatar	
		$this->_controllerConfig  = array('Dir'=> PUBLIC_PATH.'/files/products/');		
		$this->_arrParam['controllerConfig'] = $this->_controllerConfig;
	}

	public function indexAction(){
		
		$this->view->Title = 'Product :: Product manager :: List';
		$this->view->headTitle($this->view->Title,true);
		
		$tblProduct = new Product_Model_Product();
		$this->view->Items = $tblProduct->listItem($this->_arrParam, array('task'=>'admin-list'));
				
		$totalItem  = $tblProduct->countItem($this->_arrParam);
		
		$paginator = new Zendvn_Paginator();
		$this->view->panigator = $paginator->createPaginator($totalItem,$this->_paginator);
		//get listCat parent
		$tblCategory 		= new Product_Model_Productcategory();		
		$this->view->listCat= $tblCategory->listItem($this->_arrParam,array('task'=>'admin-list-select','root'=>'true'));
		
		//get location
		$tblLocation 		= new Product_Model_Productlocation();				
		$this->view->listLocation= $tblLocation->listItem($this->_arrParam,array('task'=>'admin-list-select','root'=>'true'));
		
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
        if($this->_arrParam['type'] == 'location'){
			$ssFilter->district_id 		= $this->_arrParam['district_id'];				
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
		
        $tblCategory 		= new Product_Model_Productcategory();
        $tblLocation= new Product_Model_Productlocation();		
            
		if($this->_request->isPost()){
			//$imgc=$this->_arrParam['image_current'];
            
			$validate = new Product_Form_ProductValidate($this->_arrParam,'add');
			if($validate->checkError() == true){
				//Hien thi thong bao loi
				$this->view->errors = $validate->getMessageError();
				$this->view->Item = $validate->getData();
			}else{
				//Luu vao database
				$tblProduct 	= new Product_Model_Product();	
				$rs				= $validate->getData();			
				$pro_id=$tblProduct->saveItem($rs,array('task'=>'admin-add'));
				$this->view->errors = array('success');
                
				

               //luu vao bang photoproduct
                $tblProductPhoto 	= new Product_Model_Productphoto();
                $rs['pro_id']=$idPro;
                
                
                $j=0;
                $imgInfo=array();
                while($rs['image1_'.$j.'_'])
                {
                   $newArr=array();
                   
                   $imgInfo['title']=$rs['title'];
                   $imgInfo['full_image']=$rs['image1_'.$j.'_'];
                   $imgInfo['status']=1;
                   $imgInfo['pro_id']=$pro_id;
                   $imgInfo['type']=1;
                   $tblProductPhoto->saveItem($imgInfo,array('task'=>'admin-add'));    
                   $j++;
                }
                
                 
				
			}			
			
		}
        
       	$this->view->Title = 'Product :: Product manager :: Add';
		$this->view->headTitle($this->view->Title,true);
		//get list user
		$tblUser=new Default_Model_Users();
		$this->view->listUser= $tblUser->listItem($this->_arrParam,array('task'=>'admin-list-select'));
		
		//get listCat parent
				
		$this->view->listCat= $tblCategory->listItem($this->_arrParam,array('task'=>'admin-list-select','root'=>'false'));
			
        //L?y danh sách location
        
		$this->view->listLocation= $tblLocation->listItem($this->_arrParam,array('task'=>'admin-list-select','root'=>'false'));
		    
	}
	
	public function infoAction(){
		$this->view->Title = 'Product :: Product manager :: Information';
		$this->view->headTitle($this->view->Title,true);
		
		$tblProduct = new Product_Model_Product();
		$this->view->Item = $tblProduct->getItem($this->_arrParam,array('task'=>'admin-info'));
		
	}
	
	public function editAction(){
		

		$tblCategory = new Product_Model_Productcategory();        		
        $tblPhoto= new Product_Model_Productphoto();
		$tblProduct = new Product_Model_Product();
        
        
		if($this->_request->isPost()){
                      
			//$imgc=$this->_arrParam['image_current'];
			$validate = new Product_Form_ProductValidate($this->_arrParam,'edit');
            
            
			if($validate->checkError() == true){
				//Hien thi thong bao loi
				$this->view->errors = $validate->getMessageError();
				$this->view->Item = $validate->getData();
			}else{

				//Luu vao database
				$rs			= $validate->getData();			
				$tblProduct->saveItem($rs,array('task'=>'admin-edit'));
				$this->view->errors = array('success');
				$this->view->Item	= $rs;
                

				//luu vao bang photoproduct                
                $rs['pro_id']=$idPro;
                $j=0;
                $imgInfo=array();
                $pro_id=$rs['id'];
                while($rs['image1_'.$j.'_'])
                {
                   $newArr=array();
                   
                   $imgInfo['title']=$rs['title'];
                   $imgInfo['full_image']=$rs['image1_'.$j.'_'];
                   $imgInfo['status']=1;
                   $imgInfo['pro_id']=$pro_id;
                   $imgInfo['type']=1;
                   $tblPhoto->saveItem($imgInfo,array('task'=>'admin-add'));    
                   $j++;
                }
			}			
			
		}
        
        $this->view->Title = 'Product :: Product manager :: Edit';
		$this->view->headTitle($this->view->Title,true);
		//get list user
		$tblUser=new Default_Model_Users();
		$this->view->listUser= $tblUser->listItem($this->_arrParam,array('task'=>'admin-list-select'));
		
		//get listCat parent
				
		$this->view->listCat= $tblCategory->listItem($this->_arrParam,array('task'=>'admin-list-select','root'=>'false'));
		
        //l?y danh sách image
        		
		$this->view->listPhoto= $tblPhoto->listItem($this->_arrParam,array('task'=>'list-by-proid','type'=>1));
		        
		//get item info
        	
		$this->view->Item = $tblProduct->getItem($this->_arrParam,array('task'=>'admin-edit'));
		
		
	}

	public function deleteAction(){
		$this->view->Title = 'Product :: Product manager :: Delete';
		$this->view->headTitle($this->view->Title,true);
		if($this->_request->isPost()){
			$tblProduct = new Product_Model_Product();
            if(!$tblProduct->isOwner($tblProduct->getItem($this->_arrParam,array('task'=>'admin-edit')))){//kiểm tra xem có phải của người đó hay ko
                //Hien thi thong bao loi
				
                $this->view->errors=array('Bạn không có quyền chỉnh sửa nội dung');
				
            }
            else
            {
                $tblProduct->deleteItem($this->_arrParam,array('task'=>'admin-delete'));
                
                $this->_redirect($this->_actionMain);    
            }
			

			
		}
	}

	public function statusAction(){
		$tblProduct = new Product_Model_Product();		
		$tblProduct->changeStatus($this->_arrParam);
		$this->_redirect($this->_actionMain . '/page/'.$this->_request->getParam('page',1));	
		$this->_helper->viewRenderer->setNoRender();
	}
	
	public function multiDeleteAction(){
		
		if($this->_request->isPost()){
			$tblProduct = new Product_Model_Product();
            
			$tblProduct->deleteItem($this->_arrParam,array('task'=>'admin-multi-delete'));
			$this->_redirect($this->_actionMain);
		}
		$this->_helper->viewRenderer->setNoRender();
	}
	
	public function sortAction(){
		if($this->_request->isPost()){
			$tblUser = new Product_Model_Product();
			$tblUser->sortItem($this->_arrParam);
			$this->_redirect($this->_actionMain);
		}
		
		$this->_helper->viewRenderer->setNoRender();
	}
    
	public function deletePhotoProductAction(){
	   
       $tblPhotoProduct = new Product_Model_Productphoto();
	   $tblPhotoProduct->deleteItem($this->_arrParam,array('task'=>'admin-photo-product'));
       
       $this->_helper->viewRenderer->setNoRender();
	   $this->_helper->layout->disableLayout();
       
	}

	
}



