<?php
class Product_IndexController extends Zendvn_Controller_Action {
	//Mang tham so nhan duoc o moi Action
	protected $_arrParam;
	
	//Duong dan cua Controller
	protected $_currentController;
	
	//Duong dan cua Action chinh
	protected $_actionMain;
	
	//Thong so phan trang
	protected $_paginator = array(
									'itemCountPerPage' => 24,
									'pageRange' => 3,
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
                             
        
        //set lai pagging
		if(!empty($ssFilter->pagging)){
			$this->_paginator['itemCountPerPage']=$ssFilter->pagging;
			$this->_arrParam['ssFilter']['pagging']=$ssFilter->pagging;
		}
        
        $this->_arrParam['ssFilter']['cat_id'] 		= $ssFilter->cat_id;
        
        
		//phan trang
		$this->_paginator['currentPage'] = $this->_request->getParam('page',1);
		$this->_arrParam['paginator'] = $this->_paginator;
        
		//Truyen ra view
		$this->view->arrParam = $this->_arrParam;
		$this->view->currentController = $this->_currentController;
		$this->view->actionMain = $this->_actionMain;
        
  
	}
		
	public function indexAction() {	
       //$this->_helper->viewRenderer->setNoRender();
		//$this->_helper->layout->disableLayout();
	}
   
    
	
    public function listAction() {
        
        
        //lấy thông tin category
        //list location and category
		/*$tblCategory	=	new Product_Model_Productcategory();
		$this->view->categoryDetail=$tblCategory->getItem(array('id'=>$this->_arrParam['cat_id']),array('task'=>'admin-edit'));
        */
        
        //cache mãng thông tin của category
        $cache=Zendvn_Cache::getCacheObject('cachedatacategoryinfo');
        if ( ($this->view->categoryDetail = $cache->load('controller_cachedatacategoryinfo_'.$this->_arrParam['cat_id'])) === false ) {
            $tblCategory    =   new Product_Model_Productcategory();
            $this->view->categoryDetail=$tblCategory->getItem(array('id'=>$this->_arrParam['cat_id']),array('task'=>'admin-edit'));
            $cache->save($this->view->categoryDetail);
        }

        //cache mãng thông tin sản phẩm của category
        $cache=Zendvn_Cache::getCacheObject('cachedatalistbycategory');
        if(isset($this->_arrParam['page']))
            $cpage=$this->_arrParam['page'];
        else
            $cpage=1;
        if ( ($this->view->listProducts = $cache->load('controller_cachedatalistbycategory_'.$this->_arrParam['cat_id'].'_'.$cpage)) === false ) {
            //lấy danh sách sản phẩm
            $tblProduct =   new Product_Model_Product();
            $arrPa=$this->_arrParam;                
            $arrPa['cat_id']=$this->_arrParam['cat_id'];        
            $this->view->listProducts=$tblProduct->listItem($arrPa,array('task'=>'list-by-cat'));
            $cache->save($this->view->listProducts);
        }

        
        //title
    	$this->view->Title = $this->view->categoryDetail['name'].' | 4teeshop chuyên cung cấp các mặt hàng handmade';
    	$this->view->headTitle($this->view->Title,true);
        $this->view->headMeta()->setName('description', '4teeshop chuyên cung cấp các mặt hàng handmade');
        
        
        
        //cache tong san pham
        if ( ($totalItem = $cache->load('controller_cachedatalistbycategory_count_'.$this->_arrParam['cat_id'])) === false ) {
            //lấy danh sách sản phẩm
            $tblProduct =   new Product_Model_Product();
            $totalItem  = $tblProduct->countItem($this->_arrParam,array('task'=>'list-item-by-category'));
            $cache->save($totalItem);
        }
        //echo 'totalItem:'.$totalItem;
        //phan trang
        $paginator = new Zendvn_Paginator();
        $this->view->panigator = $paginator->createPaginator($totalItem,$this->_paginator);
     
        //$this->_helper->viewRenderer->setNoRender();
		//$this->_helper->layout->disableLayout();
    }
    public function searchAction() {
        
        
        //lấy thông tin category
        //list location and category
        //$tblCategory    =   new Product_Model_Productcategory();
        //$this->view->categoryDetail=$tblCategory->getItem(array('id'=>$this->_arrParam['cat_id']),array('task'=>'admin-edit'));
        
        //$cat_id=$this->_arrParam['cat_id'];    
        
        
        
        //lấy danh sách sản phẩm
        $tblProduct =   new Product_Model_Product();
        $arrPa=$this->_arrParam;
                
       
        $this->view->listProducts=$tblProduct->listItem($arrPa,array('task'=>'search'));
        
        //title
        $this->view->Title = $this->_arrParam['k'].'  4teeshop chuyên cung cấp các mặt hàng handmade';
        $this->view->headTitle($this->view->Title,true);
        $this->view->headMeta()->setName('description', '4teeshop chuyên cung cấp các mặt hàng handmade');
        
        //phan trang
        $totalItem  = $tblProduct->countItem($this->_arrParam,array('task'=>'search'));
        $paginator = new Zendvn_Paginator();
        $this->view->panigator = $paginator->createPaginator($totalItem,$this->_paginator);
     
        //$this->_helper->viewRenderer->setNoRender();
        //$this->_helper->layout->disableLayout();
    }
    public function businessAction() {	
       
        //lấy danh sách sản phẩm
        $tblProduct	=	new Product_Model_Product();
		$this->view->listProductByBusiness=$tblProduct->listItem($this->_arrParam,array('task'=>'list-business'));
        
        $this->view->Title = 'Unicity Việt Nam - Happy Life Project | Cung cấp các dòng sản phẩm về dinh dưỡng, sức khỏe, và các dòng thực phẩm bổ sung.'.$this->view->locationCurrent['name'];
		$this->view->headTitle($this->view->Title,true);
        $this->view->headMeta()->setName('description', 'Unicity Việt Nam - Happy Life Project | Cung cấp các dòng sản phẩm về dinh dưỡng, sức khỏe, và các dòng thực phẩm bổ sung.'.$this->view->locationCurrent['name'].'. Unicity Việt Nam - Happy Life Project | Cung cấp các dòng sản phẩm về dinh dưỡng, sức khỏe, và các dòng thực phẩm bổ sung.');

		//$this->_helper->viewRenderer->setNoRender();
		//$this->_helper->layout->disableLayout();
	}
	public function orderAction()
    {        
        $errorCode=0;//mã lỗi
        if($this->_arrParam['c_user_name']&&$this->_arrParam['c_user_phone']&&$this->_arrParam['c_user_address']&&$this->_arrParam['c_user_email']&&$this->_arrParam['listpro'])
        {
            $tblProduct=new Product_Model_Product();
            $tblProductOrder=new Product_Model_Productorder();
            $tblProductOrderDetail=new Product_Model_Productorderdetail();
            $arrP=array(
                'name'=>$this->_arrParam['c_user_name'],
                'phone'=>$this->_arrParam['c_user_phone'],
                'address_1'=>$this->_arrParam['c_user_address'],
                'created'=>date('Y-m-d H:i:s'),
                'created_by'=>0,
                'status'=>0,
                'note'=>$this->_arrParam['c_user_note'],
                'email'=>$this->_arrParam['c_user_email']
            );
            //save thông tin order
            $id=$tblProductOrder->saveItem($arrP,array('task'=>'admin-add'));
            
            $arrIDPro=array();
            foreach($this->_arrParam['listpro'] as $key=>$info)
            {
                $arrIDPro[$key]=$key;
            }
            
            $arrPro=$tblProduct->listItem($arrIDPro,array('task'=>'list-by-arrkey'));
            
            
            $arrOrderDT=array();
            $sum=0;
            $htmlMailer='<table>';
            $htmlMailer.='<tr>';
            $htmlMailer.='<th>Mã sản phẩm</th>';
            $htmlMailer.='<th>Hình ảnh</th>';
            $htmlMailer.='<th>Sản phẩm</th>';
            $htmlMailer.='<th>Đơn giá</th>';
            $htmlMailer.='<th>Số lượng</th>';
            $htmlMailer.='<th>Thành tiền</th>';            
            $htmlMailer.='</tr>';
            foreach($arrPro as $key=>$info)
            {
                $arrOrderDT[$key]['order_id']=$id;
                $arrOrderDT[$key]['pro_id']=$info['id'];
                $arrOrderDT[$key]['price']=$info['price'];
                $arrOrderDT[$key]['quantity']=$this->_arrParam['listpro'][$info['id']];
                
                
                $product_image	= PUBLIC_URL.'/files/products/image/'.$info['image'];
                $summary=$this->view->cmsConvertDbToView(strip_tags($info['summary']));
                $name=$this->view->cmsConvertDbToView($info['name']);
                $price=number_format($info['price']);
                $sum+=$info['price']*$arrOrderDT[$key]['quantity'];
                $tt=number_format($info['price']*$arrOrderDT[$key]['quantity']);
                
                $htmlMailer.='<tr>';
                $htmlMailer.='<td>'.$info['id'].'</td>';
                $htmlMailer.='<td><img src="'.$product_image.'" width="80px"></td>';
                $htmlMailer.='<td>';
                $htmlMailer.='<div><b>'.$name.'</b></div>';
                $htmlMailer.='<div>'.$summary.'</div>';
                $htmlMailer.='</td>';
                $htmlMailer.='<td>'.$price.'</td>';
                $htmlMailer.='<td>'.$arrOrderDT[$key]['quantity'].'</td>';
                $htmlMailer.='<td>'.$tt.'</td>';
                $htmlMailer.='</tr>';
            }
            $htmlMailer.='<tr>';
            $htmlMailer.='<th colspan="4">Mã sản phẩm</th>';
            $htmlMailer.='<th>Tổng tiền</th>';
            $htmlMailer.='<th>'.number_format($sum).'</th>';            
            $htmlMailer.='</tr>';
            $htmlMailer.='</table>';
            $tblProductOrderDetail->saveItem($arrOrderDT,array('task'=>'add'));
            
            $errorCode=0;
        }
        else
        {
            $errorCode=1;//các field trắng
        }
                
        
        $infoOrder=array();
        if($errorCode==0)
        {
            //Gui mail - Mo ra khi dua len server!
            $mail = new Zendvn_Mail_Mail(array('smtpserver'=>'smtp.gmail.com','user'=>'khachhanglienhe2012@gmail.com','pass'=>'khachhanglienhe2012','templateFile'=>'order.mail'),array('smtp'=>1));
            $mail->setFrom('ldnguyen@themuasam.vn');
            $mail->setTo($arrP['email']);
            $mail->setCc('quangphung@sonthanh.co');
            $mail->setCc('contact@sonthanh.co');
            $mail->setCc('info@sonthanh.co');
            $mail->formatMailContent(array($arrP['name']),array($arrP['name'],
                $arrP['email'],$arrP['phone'],$arrP['address_1'],$arrP['note'],$id,$htmlMailer));                   
            $rs = $mail->sendmail();
            
            $infoOrder=array(
                        'c'=>$id,
                        'n'=>$arrP['name'],
                        'p'=>$arrP['phone'],
                        'e'=>$arrP['email'],
                        'a'=>$arrP['address_1'],
                        'no'=>$arrP['note'],
                    );
        }
        $arr2JSON=array(
			'error'=>$errorCode,
			'info'=>$infoOrder
			);	
        echo json_encode($arr2JSON);
        
        
        $this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
    }
}
