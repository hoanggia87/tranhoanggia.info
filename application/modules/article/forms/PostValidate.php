<?php
class Article_Form_PostValidate{
	
	//Mang chua cac cau thong bao error
	private 	$_messagesError = null;
	
	//Mang chua lieu sau khi validate	
	protected 	$_arrData;
	
	//Nhan cac gia tri tham so truy vao
	public function __construct($arrParam = array(),$option = 'image')
    {
    	    
    	if($option == 'image')
        {
        	// CHECK VALUE 'title' kiem tra do dai
        	$validator = new Zend_Validate_StringLength(3,200);
        	//Kiem tra file upload
        	$upload = new Zend_File_Transfer_Adapter_Http();
        	$fileInfo = $upload->getFileInfo();
            /*echo '<pre>';
            var_dump($fileInfo);
            echo '</pre>';*/
            if($arrParam['t'] != 'link')
            {
            	$fileName = $fileInfo['image']['name'];
            	if($fileName != '')
                {
            	   	// Thiet lap dieu kien cho tap tin dc phep upload
        			//$upload->addValidator('Size',false,'2kb','picture');	
        			$upload->addValidator('Size',false,array('min'=>'2kb','max'=>'9000kb'),'icon')	
        					//->addValidator('Extension',false,'zip','picture')	
        					->addValidator('Extension',false,array('gif','png','jpg'),'image');
        	    	if(!$upload->isValid('image')){		
        				$messages = $upload->getMessages();
        				if(count($messages)>0){
        	    				foreach($messages as $key => $vaue){
        	    					$this->_messagesError[] = '<p class="form-message error">Lỗi: Vui lòng chọn ảnh đúng định dạng.</p>';
        	    				}
        	    		}
        			}else{
        				$arrParam['image'] = $fileName; 
        			}	  
            	}
                else
                {
                    $this->_messagesError[] = '<p class="form-message error">Lỗi: Vui lòng chọn file anh tải lên.</p>';
                }
             }
             else
             {
                $Ufile = $arrParam['url'];
                $file_headers = @get_headers($Ufile);
                if(!$validator->isValid($Ufile))
            	{
            		$messages = $validator->getMessages();	
            	
            		if(count($messages)>0){
            			foreach($messages as $key => $vaue){
            				$this->_messagesError[] = '<p class="form-message error">Lỗi: Vui lòng điền link ảnh</p>';
            			}
            		}
            	}
                elseif($file_headers[0] == 'HTTP/1.1 404 Not Found') 
                {
                    $this->_messagesError[] = '<p class="form-message error">Lỗi: Đường dẫn file ảnh không hợp lệ</p>';
                }
                else
                {
                    $arrEx = array('gif','png','jpg');
                    $temp=explode("/", $Ufile);
                    $tempurl=end($temp);
        	    	$fileName=Zendvn_File_Upload::getName($tempurl);
        	    	$fileExt=Zendvn_File_Upload::getExt($tempurl);
                    $fileExt = strtolower($fileExt);
                    if(!in_array($fileExt,$arrEx))
                    {
                        $this->_messagesError[] = '<p class="form-message error">Lỗi: Định dạng file không hợp lệ</p>';
                    }
                }
             }
        	if(!$validator->isValid($arrParam['title']))
        	{
        		$messages = $validator->getMessages();	
        	
        		if(count($messages)>0){
        			foreach($messages as $key => $vaue){
        				$this->_messagesError[] = '<p class="form-message error">Lỗi: Vui lòng đặt tiêu đề cho bức ảnh.</p>';
        			}
        		}
        	}
         }
         elseif($option == 'video')
         {
            // CHECK VALUE 'title' kiem tra do dai
        	$validator = new Zend_Validate_StringLength(3,200);           
            $Ufile = $arrParam['url'];
            if(!$validator->isValid($Ufile))
        	{
        		$messages = $validator->getMessages();	
        	
        		if(count($messages)>0){
        			foreach($messages as $key => $vaue){
        				$this->_messagesError[] = '<p class="form-message error">Lỗi: Vui lòng điền link Video</p>';
        			}
        		}
        	}
            else
            {
                $youtube = strtolower($Ufile);
                
                if (strpos($youtube,'youtu') === false)
                {
                    $this->_messagesError[] = '<p class="form-message error">Lỗi: Chỉ chấp nhận link Youtube</p>';
                }
            }
        	if(!$validator->isValid($arrParam['title']))
        	{
        		$messages = $validator->getMessages();	
        	
        		if(count($messages)>0){
        			foreach($messages as $key => $vaue){
        				$this->_messagesError[] = '<p class="form-message error">Lỗi: Vui lòng đặt tiêu đề cho Video.</p>';
        			}
        		}
        	}
         }
         elseif($option == 'truyencuoi')
         {
            // CHECK VALUE 'title' kiem tra do dai
        	$validator = new Zend_Validate_StringLength(3,200);           
        	if(!$validator->isValid($arrParam['title']))
        	{
        		$messages = $validator->getMessages();	
        	
        		if(count($messages)>0){
        			foreach($messages as $key => $vaue){
        				$this->_messagesError[] = '<p class="form-message error">Lỗi: Vui lòng đặt tiêu đề cho truyện cười.</p>';
        			}
        		}
        	}
            if(!$validator->isValid($arrParam['content']))
        	{
        		$messages = $validator->getMessages();	
        	
        		if(count($messages)>0){
        			foreach($messages as $key => $vaue){
        				$this->_messagesError[] = '<p class="form-message error">Lỗi: Vui lòng nhập nội dung truyện cười.</p>';
        			}
        		}
        	}
         }
         elseif($option == 'tamsu')
         {
            // CHECK VALUE 'title' kiem tra do dai
        	$validator = new Zend_Validate_StringLength(3,200);           
        	if(!$validator->isValid($arrParam['title']))
        	{
        		$messages = $validator->getMessages();	
        	
        		if(count($messages)>0){
        			foreach($messages as $key => $vaue){
        				$this->_messagesError[] = '<p class="form-message error">Lỗi: Vui lòng đặt tiêu đề cho tâm sự.</p>';
        			}
        		}
        	}
            if(!$validator->isValid($arrParam['content']))
        	{
        		$messages = $validator->getMessages();	
        	
        		if(count($messages)>0){
        			foreach($messages as $key => $vaue){
        				$this->_messagesError[] = '<p class="form-message error">Lỗi: Vui lòng nhập nội dung tâm sự.</p>';
        			}
        		}
        	}
         }
         elseif($option == 'baohay')
         {
            // CHECK VALUE 'title' kiem tra do dai
        	$validator = new Zend_Validate_StringLength(3,200);           
        	if(!$validator->isValid($arrParam['title']))
        	{
        		$messages = $validator->getMessages();	
        	
        		if(count($messages)>0){
        			foreach($messages as $key => $vaue){
        				$this->_messagesError[] = '<p class="form-message error">Lỗi: Vui lòng đặt tiêu đề cho báo hay.</p>';
        			}
        		}
        	}
            if(!$validator->isValid($arrParam['content']))
        	{
        		$messages = $validator->getMessages();	
        	
        		if(count($messages)>0){
        			foreach($messages as $key => $vaue){
        				$this->_messagesError[] = '<p class="form-message error">Lỗi: Vui lòng nhập nội dung báo hay.</p>';
        			}
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
		if($this->checkError() != true)
        {
			if($this->_arrData['image'] != '')
            {
				//Upload file
				$upload = new Zendvn_File_Upload();
				$uploadDir = $this->_arrData['controllerConfig']['imagesDir'];				
				$this->_arrData['image'] = $upload->upload('image',$uploadDir. 'images/',array('task'=>'rename'),'shock_');
				/*if($this->_arrData['icon_current'] != '')
                {
					@unlink($uploadDir . 'images/' .$this->_arrData['icon_current']);
					
				}*/				
			}else{
				/*if(isset($this->_arrData['icon_current'])){
					$this->_arrData['icon'] = $this->_arrData['icon_current'];
				}*/
			}
        }
		return $this->_arrData;
	}
	
}