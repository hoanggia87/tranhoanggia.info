<?php
class Product_Form_ProductValidate{
	
	//Mang chua cac cau thong bao error
	private 	$_messagesError = null;
	
	//Mang chua lieu sau khi validate	
	protected 	$_arrData;
	
	//Nhan cac gia tri tham so truy vao
	public function __construct($arrParam = array(),$option = 'add')
    {
        if($option=='edit')
        {
        	
        }
        
    	//Kiem tra file upload
    	$upload = new Zend_File_Transfer_Adapter_Http();
    	$fileInfo = $upload->getFileInfo();
    	$fileName = $fileInfo['image']['name'];
    	
    	if($fileName != ''){
    	   	// Thiet lap dieu kien cho tap tin dc phep upload
			//$upload->addValidator('Size',false,'2kb','picture');	
			$upload->addValidator('Size',false,array('min'=>'2kb','max'=>'9000kb'),'image')	
					//->addValidator('Extension',false,'zip','picture')	
					->addValidator('Extension',false,array('gif','png','jpg'),'image');
	    	if(!$upload->isValid('image')){		
				$messages = $upload->getMessages();
				if(count($messages)>0){
	    				foreach($messages as $key => $vaue){
	    					$this->_messagesError[] = 'Ảnh sản phẩm nhỏ: ' . $vaue;
	    				}
	    		}
			}else{
				$arrParam['image'] = $fileName; 
			}	  
    	}
        
        //kiem tra nhiều file upload image1_x_
        $j=0;
        while($fileInfo['image1_'.$j.'_']['name']!='')
        {
            $fileName = $fileInfo['image1_'.$j.'_']['name'];        	
        	if($fileName != ''){
        	   	// Thiet lap dieu kien cho tap tin dc phep upload
    			//$upload->addValidator('Size',false,'2kb','picture');	
    			$upload->addValidator('Size',false,array('min'=>'2kb','max'=>'9000kb'),'image1_'.$j.'_')	
    					//->addValidator('Extension',false,'zip','picture')	
    					->addValidator('Extension',false,array('gif','png','jpg'),'image1_'.$j.'_');
    	    	if(!$upload->isValid('image1_'.$j.'_')){		
    				$messages = $upload->getMessages();
    				if(count($messages)>0){
    	    				foreach($messages as $key => $vaue){
    	    					$this->_messagesError[] = 'Ảnh sản phẩm nhỏ: ' . $vaue;
    	    				}
    	    		}
    			}else{
    				$arrParam['image1_'.$j.'_'] = $fileName; 
    			}	  
        	}
            
            $j++;
        }
            
        
        

/*
		//Kiem tra file upload image 1
		$upload = new Zend_File_Transfer_Adapter_Http();
		$fileInfo = $upload->getFileInfo();
		$fileName = $fileInfo['image1']['name'];
		
		if($fileName != ''){
			// Thiet lap dieu kien cho tap tin dc phep upload
			//$upload->addValidator('Size',false,'2kb','picture');	
			$upload->addValidator('Size',false,array('min'=>'2kb','max'=>'9000kb'),'image1')	
				//->addValidator('Extension',false,'zip','picture')	
				->addValidator('Extension',false,array('gif','png','jpg'),'image1');
			if(!$upload->isValid('image1')){		
				$messages = $upload->getMessages();
				if(count($messages)>0){
					foreach($messages as $key => $vaue){
						$this->_messagesError[] = 'Ảnh sản phẩm lớn: ' . $vaue;
					}
				}
			}else{
				$arrParam['image1'] = $fileName; 
			}	  
		}
*/


		//Kiem tra file upload map_image
		//$upload = new Zend_File_Transfer_Adapter_Http();
		//$fileInfo = $upload->getFileInfo();
		$fileName = $fileInfo['map_image']['name'];
		
		if($fileName != ''){
			// Thiet lap dieu kien cho tap tin dc phep upload
			//$upload->addValidator('Size',false,'2kb','picture');	
			$upload->addValidator('Size',false,array('min'=>'2kb','max'=>'9000kb'),'map_image')	
				//->addValidator('Extension',false,'zip','picture')	
				->addValidator('Extension',false,array('gif','png','jpg'),'map_image');
			if(!$upload->isValid('map_image')){		
				$messages = $upload->getMessages();
				if(count($messages)>0){
					foreach($messages as $key => $vaue){
						$this->_messagesError[] = 'Map Image: ' . $vaue;
					}
				}
			}else{
				$arrParam['map_image'] = $fileName; 
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
			
			if($this->_arrData['image'] != ''){
				//Upload file
				$upload = new Zendvn_File_Upload();
				$uploadDir = $this->_arrData['controllerConfig']['Dir'];				
				$this->_arrData['image'] = $upload->upload('image',$uploadDir. 'full/',array('task'=>'rename'),'4teeshop_');
				//Resize
				$linkImage=$uploadDir . 'full/'. $this->_arrData['image'];
				$img = Zendvn_File_Image::create($linkImage);
				//$img->resize(180,180);
				//$img->save($uploadDir . '180x180/' . $this->_arrData['image']);
				//$img->resize(100,100);
				//$img->save($uploadDir . '100x100/' . $this->_arrData['image']);
				$img->resize(400,400);
				$img->save($uploadDir . 'image1/' . $this->_arrData['image']);
				$img->resize(267,267);
				$img->save($uploadDir . 'image/' . $this->_arrData['image']);
				
				//crop image
				
				
				if($this->_arrData['image_current'] != ''){
					@unlink($uploadDir . 'full/' .$this->_arrData['image_current']);
					//@unlink($uploadDir . '100x100/' . $this->_arrData['image_current']);
					//@unlink($uploadDir . '180x180/' . $this->_arrData['image_current']);
					@unlink($uploadDir . 'image/' . $this->_arrData['image_current']);
					@unlink($uploadDir . 'image1/' . $this->_arrData['image_current']);
				}				
			}else{
				if(isset($this->_arrData['image_current'])){
					$this->_arrData['image'] = $this->_arrData['image_current'];
				}
			}
			
            
            //upload for slide
			$j=0;
            while($this->_arrData['image1_'.$j.'_']!='')
            {
                if($this->_arrData['image1_'.$j.'_'] != ''){
    				//Upload file
    				$upload = new Zendvn_File_Upload();
    				$uploadDir = $this->_arrData['controllerConfig']['Dir'];				
    				$this->_arrData['image1_'.$j.'_'] = $upload->upload('image1_'.$j.'_',$uploadDir. 'full/',array('task'=>'rename'),'4teeshop_');
    				//Resize
    				$linkImage=$uploadDir . 'full/'. $this->_arrData['image1_'.$j.'_'];
    				$img = Zendvn_File_Image::create($linkImage);
    				//$img->resize(180,180);
    				//$img->save($uploadDir . '180x180/' . $this->_arrData['image']);
    				//$img->resize(100,100);
    				//$img->save($uploadDir . '100x100/' . $this->_arrData['image']);
    				$img->resize(610,330);
    				$img->save($uploadDir . 'slideshow/' . $this->_arrData['image1_'.$j.'_']);
    				//crop image
    				
    				
    				if($this->_arrData['image1_'.$j.'_current'] != ''){
    					@unlink($uploadDir . 'full/' .$this->_arrData['image1_'.$j.'_current']);
    					//@unlink($uploadDir . '100x100/' . $this->_arrData['image_current']);
    					//@unlink($uploadDir . '180x180/' . $this->_arrData['image_current']);
    					@unlink($uploadDir . 'slideshow/' . $this->_arrData['image1_'.$j.'_current']);
    				}				
    			}else{
    				if(isset($this->_arrData['image1_'.$j.'_current'])){
    					$this->_arrData['image1_'.$j.'_'] = $this->_arrData['image1_'.$j.'_current'];
    				}
    			}
                
                $j++;
			}
			
			if($this->_arrData['map_image'] != ''){
				//Upload file
				$upload = new Zendvn_File_Upload();
				$uploadDir = $this->_arrData['controllerConfig']['Dir'];				
				$this->_arrData['map_image'] = $upload->upload('map_image',$uploadDir. 'full/',array('task'=>'rename'),'4teeshop_');
				//Resize
				$linkImage=$uploadDir . 'full/'. $this->_arrData['map_image'];
				$img = Zendvn_File_Image::create($linkImage);
				//$img->resize(180,180);
				//$img->save($uploadDir . '180x180/' . $this->_arrData['map_image']);
				//$img->resize(100,100);
				//$img->save($uploadDir . '100x100/' . $this->_arrData['map_image']);
				$img->resize(264,264);
				$img->save($uploadDir . 'map/' . $this->_arrData['map_image']);
				//crop image
				
				
				if($this->_arrData['map_image_current'] != ''){
					@unlink($uploadDir . 'full/' .$this->_arrData['map_image_current']);
					//@unlink($uploadDir . '100x100/' . $this->_arrData['map_image_current']);
					//@unlink($uploadDir . '180x180/' . $this->_arrData['map_image_current']);
					@unlink($uploadDir . 'map/' . $this->_arrData['map_image_current']);
				}				
			}else{
				if(isset($this->_arrData['map_image_current'])){
					$this->_arrData['map_image'] = $this->_arrData['map_image_current'];
				}
			}
			
			
			
		} 
		return $this->_arrData;
	}
	
}