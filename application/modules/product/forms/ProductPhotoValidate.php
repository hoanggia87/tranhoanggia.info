<?php
class Product_Form_ProductPhotoValidate{
	
	//Mang chua cac cau thong bao error
	private 	$_messagesError = null;
	
	//Mang chua lieu sau khi validate	
	protected 	$_arrData;
	
	//Nhan cac gia tri tham so truy vao
	public function __construct($arrParam = array(),$option = 'add')
    {
    	    
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
	    					$this->_messagesError[] = 'Full Image: ' . $vaue;
	    				}
	    		}
			}else{
				$arrParam['full_image'] = $fileName; 
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
				$uploadDir = $this->_arrData['controllerConfig']['<?php echo $this->imgUrl ?>Dir'];				
				$this->_arrData['full_image'] = $upload->upload('full_image',$uploadDir. 'full/',array('task'=>'rename'),'themuasam_');
				//Resize
				$linkImage=$uploadDir . 'full/'. $this->_arrData['image'];
				$img = Zendvn_File_Image::create($linkImage);
				//resize				
				$img->resize(640,340);
				$img->save($uploadDir . 'slidesshow/' . $this->_arrData['full_image']);
				
				
				//crop image
				if($this->_arrData['image_current'] != ''){
					@unlink($uploadDir . 'full/' .$this->_arrData['image_current']);
					@unlink($uploadDir . 'slideshow/' . $this->_arrData['image_current']);
					
					
				}				
			}else{
				if(isset($this->_arrData['image_current'])){
					$this->_arrData['full_image'] = $this->_arrData['image_current'];
				}
			}
			
		}
		else{
			$this->_arrData['full_image'] = $this->_arrData['image_current'];
		}
		
		return $this->_arrData;
	}
	
}