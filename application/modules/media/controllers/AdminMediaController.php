<?php
class Media_AdminMediaController extends Zendvn_Controller_Action{
	
	
	//Mang tham so nhan duoc o moi Action
	protected $_arrParam;
	
	//Duong dan cua Controller
	protected $_currentController;
	
	//Duong dan cua Action chinh
	protected $_actionMain;
	
	protected $_arrImageDefault=array(
			'audio'=>'audio.png',
			'application'=>'doc.png',
			'video'=>'video.png',
			'image'=>''
		);

	//Thong so phan trang
	protected $_paginator = array(
									'itemCountPerPage' => 20,
									'pageRange' => 5,
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
		
				

		//khai bao thu muc upload 	
		$this->_controllerConfig  = array('Dir'=> PUBLIC_PATH.'/files/media/');		
		$this->_arrParam['controllerConfig'] = $this->_controllerConfig;

		//Truyen ra view
		$this->view->arrParam = $this->_arrParam;
		$this->view->currentController = $this->_currentController;
		$this->view->actionMain = $this->_actionMain;
		
		$this->view->arrImageDefault=$this->_arrImageDefault;
         
	}
	
	public function indexAction(){
		$this->view->headLink()->appendStylesheet($this->view->cssUrl . '/uploadify/uploadifive.css','screen');
		$this->view->headScript()->appendFile($this->view->jsUrl . '/uploadify/jquery.uploadifive.min.js','text/javascript');
		$this->view->headScript()->appendFile($this->view->jsUrl . '/jquery.colorbox-min.js','text/javascript');
		
	}
	
	public function uploadAction(){

		
		if($_FILE['Filedata']['error']==0)
		{
			$upload = new Zendvn_File_Upload();
			$uploadDir = $this->_arrParam['controllerConfig']['Dir'];	
						
			$fileName = $upload->upload('Filedata',$uploadDir. '/',array('task'=>'rename'),'royal_');
			//nếu có tên 

			$parents=($this->_arrParam['parents'])?$this->_arrParam['parents']:0;
			if($fileName)
			{
				$tblMedia=new Media_Model_Media();

				$mime=mime_content_type($uploadDir.'/'.$fileName);
				$fT=explode('/', $mime);

				//Thư mục chứa. sẽ xử lý sau
				$folder='';

				$imageUrl='';
				 switch ($fT[0]) {
		            case 'image':
		                $imageUrl=PUBLIC_URL.'/files/media/'.$folder. $fileName;
		                break;
		            
		            default:
		                $imageUrl=$this->view->imgUrl.'/thumbs/'.$this->_arrImageDefault[$fT[0]];
		                
		                break;
		        }

				$arrParam=array(
						'name'=>$fileName,
						'summary'=>'',
						'description'=>'',

						'file_type'=>$fT[0],
						'file_size'=>filesize($uploadDir.'/'.$fileName),
						'file_mime'=>$mime,
						'file_ext'=>$upload->getExt($fileName),
						'height'=>'',
						'status'=>1,
						'image_url'=>$imageUrl,
						'parents'=>$parents
					);

				$arrParam['id']=$tblMedia->saveItem($arrParam, array('task'=>'admin-add'));
				

				echo json_encode($arrParam);
			}	
			else
			{
				echo json_encode(array('error'=>1));
			}
		}
		else
		{
			echo json_encode(array('error'=>1));
		}
		

		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
	}
	public function getListAction(){
		$tblMedia=new Media_Model_Media();
		//lay tong
		$totalItem  = $tblMedia->countItem($this->_arrParam);

		//pagging
		$paginator = new Zendvn_Paginator();
		$this->view->panigator = $paginator->createPaginator($totalItem,$this->_paginator);
		//list item
		$this->view->items=$tblMedia->listItem($this->_arrParam, array('task'=>'admin-list'));

		$this->_helper->layout->disableLayout();
	}
	
}




