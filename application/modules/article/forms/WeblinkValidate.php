<?php
class Weblink_Form_WeblinkValidate{
	
	//Mang chua cac cau thong bao error
	private 	$_messagesError = null;
	
	//Mang chua lieu sau khi validate	
	protected 	$_arrData;
	
	//Nhan cac gia tri tham so truy vao
	public function __construct($arrParam = array(),$option = 'add')
    {
    	    
    	// CHECK VALUE 'title' kiem tra do dai
    	$validator = new Zend_Validate_StringLength(3,200);
    	if(!$validator->isValid($arrParam['title']))
    	{
    		$messages = $validator->getMessages();	
    	
    		if(count($messages)>0){
    			foreach($messages as $key => $vaue){
    				$this->_messagesError[] = 'Title:' . $vaue;
    			}
    		}
    	}
    	
    	//Kiem tra file upload
    	$upload = new Zend_File_Transfer_Adapter_Http();
    	$fileInfo = $upload->getFileInfo();
    	$fileName = $fileInfo['full_image']['name'];
    	
    	if($fileName != ''){
    	   	// Thiet lap dieu kien cho tap tin dc phep upload
			//$upload->addValidator('Size',false,'2kb','picture');	
			$upload->addValidator('Size',false,array('min'=>'2kb','max'=>'9000kb'),'full_image')	
					//->addValidator('Extension',false,'zip','picture')	
					->addValidator('Extension',false,array('gif','png','jpg'),'full_image');
	    	if(!$upload->isValid('full_image')){		
				$messages = $upload->getMessages();
				if(count($messages)>0){
	    				foreach($messages as $key => $vaue){
	    					$this->_messagesError[] = 'Full_image: ' . $vaue;
	    				}
	    		}
			}else{
				$arrParam['full_image'] = $fileName; 
			}	  
    	}
    	
        $validator  = new Zend_Validate_NotEmpty(Zend_Validate_NotEmpty::INTEGER);
                                                                                                                             
                                                
       if( !$validator->isValid(intval($arrParam['cat_id'])) )
        {             
            $messages = $validator->getMessages();	    	
    		if(count($messages)>0){
    			foreach($messages as $key => $vaue){
    				$this->_messagesError[] = 'Category :' . $vaue;
    			}
    		}
        }
      
      
    	$this->_arrData = $arrParam;
    }
    
    //Ham kiem tra error
	public function checkError(){
		
		if(count($this->_messagesError)>0){
			return true;
		}else{
			return false;
		}
	}

	//Lay nhung thong bao error
	public function getMessageError(){
		return $this->_messagesError;
	}
	//Lay du lieu sau kiem tra
	public function getData(){
		if($this->checkError() != true){
			
			if($this->_arrData['full_image'] != ''){
				//Upload file
				$upload = new Zendvn_File_Upload();
				$uploadDir = $this->_arrData['controllerConfig']['imagesDir'];				
				$this->_arrData['full_image'] = $upload->upload('full_image',$uploadDir. 'logo/',array('task'=>'rename'),'logo_');
				//Resize
				/*$linkImage=$uploadDir . 'icon/'. $this->_arrData['image'];
				
				$img = Zendvn_File_Image::create($linkImage);
				//drop hinh thanh hinh vuong
				$imgSize=getimagesize($linkImage);
    			//cat hinh vuong dua vao chieu dai or chieu cao co do dai ngan hon
				$cropImg = $imgSize[0];// mac dinh gan vao chieu dai 
				if($cropImg>$imgSize[1])//neu chieu cao dai hon thi gan cho chieu cao 
					$cropImg=$imgSize[1];
				$img->cropFromCenter($cropImg,$cropImg)
					->resize(200,200);
				$img->save($uploadDir . '200x200/' . $this->_arrData['image']);
				
				$img->cropFromCenter($cropImg,$cropImg)
					->resize(100,100);
				$img->save($uploadDir . '100x100/' . $this->_arrData['image']);
				*/
				//crop image
				
				
				if($this->_arrData['full_image_current'] != ''){
					@unlink($uploadDir . 'logo/' .$this->_arrData['full_image_current']);
					
				}				
			}else{
				if(isset($this->_arrData['full_image_current'])){
					$this->_arrData['full_image'] = $this->_arrData['full_image_current'];
				}
			}
			
		}
		else{
			$this->_arrData['full_image'] = $this->_arrData['full_image_current'];
		}
		
		return $this->_arrData;
	}
	
}