<?php
class Default_Form_UserValidate{
	
	//Mang chua cac cau thong bao error
	private 	$_messagesError = null;
	
	//Mang chua lieu sau khi validate	
	protected 	$_arrData;
	
	//Nhan cac gia tri tham so truy vao
	public function __construct($arrParam = array(),$option = 'add')
    {
    	// CHECK VALUE 'values'
    	$validator = new Zend_Validate_StringLength(3,32);
    	if(!$validator->isValid($arrParam['user_name']))
    	{
    		$messages = $validator->getMessages();	
    	
    		if(count($messages)>0){
    			foreach($messages as $key => $vaue){
    				$this->_messagesError[] = 'Username:' . $vaue;
    			}
    		}
    	}else{
    		// CHECK VALUE 'user_name'
    		$validator = new Zend_Validate_Alnum(true);
    		if(!$validator->isValid($arrParam['user_name']))
    		{
    			$messages = $validator->getMessages();		
    			if(count($messages)>0){
    				foreach($messages as $key => $vaue){
    					$this->_messagesError[] = 'Username: ' . $vaue;
    				}
    			}
    			$arrParam['user_name'] = ''; 
    		}
    		if($option == 'add')
    		{
	    		$tblUser = new Default_Model_Users();
				$rs=$tblUser->checkAccount($arrParam['user_name']);
	    		if($rs!=0){		
	    			$this->_messagesError[] = 'Username: account is available';    				
	    		
	    			$arrParam['user_name'] = ''; 
	    		}
    		}
    	}
    
    	//Kiem tra file upload
    	$upload = new Zend_File_Transfer_Adapter_Http();
    	$fileInfo = $upload->getFileInfo();
    	$fileName = $fileInfo['user_avatar']['name'];
    
    	if($fileName != ''){
	    	// Thiet lap dieu kien cho tap tin dc phep upload
			//$upload->addValidator('Size',false,'2kb','picture');	
			$upload->addValidator('Size',false,array('min'=>'2kb','max'=>'10000kb'),'user_avatar')	
					//->addValidator('Extension',false,'zip','picture')	
					->addValidator('Extension',false,array('gif','png','jpg'),'user_avatar');
	    	if(!$upload->isValid('user_avatar')){		
				$messages = $upload->getMessages();
				if(count($messages)>0){
	    				foreach($messages as $key => $vaue){
	    					$this->_messagesError[] = 'User avatar: ' . $vaue;
	    				}
	    		}
			}else{
				$arrParam['user_avatar'] = $fileName; 
			}	

    	}
    	
    	// CHECK VALUE 'password'
    	
    	if(trim($arrParam['password']) == ''){
    		if($option == 'add'){    		
    			$flag = true;
    		}
    	}else{
    		$flag = true;
    	}
    	if($flag == true){
	    	$validator = new Zend_Validate_StringLength(6,32);
	    	if(!$validator->isValid($arrParam['password']))
	    	{
	    		$messages = $validator->getMessages();		
	    		if(count($messages)>0){
	    			foreach($messages as $key => $vaue){
	    				$this->_messagesError[] = 'Password: ' . $vaue;
	    			}
	    		}
	    		$arrParam['password'] = '';    		
	    	}
    	}
    	
    	// CHECK EMAIL ADDRESS = 'email'
    	$validator = new Zend_Validate_EmailAddress();
    	if(!$validator->isValid($arrParam['email']))
    	{
    		$messages = $validator->getMessages();	
    		if(count($messages)>0){
    			foreach($messages as $key => $vaue){
    				$this->_messagesError[] = 'Email: ' . $vaue;
    			}
    		}
    		$arrParam['email'] = '';
    	}
    	
    // CHECK VALUE 'values'
    	$validator = new Zend_Validate_StringLength(3,32);
    	if(!$validator->isValid($arrParam['first_name']))
    	{
    		$messages = $validator->getMessages();	
    	
    		if(count($messages)>0){
    			foreach($messages as $key => $vaue){
    				$this->_messagesError[] = 'First name:' . $vaue;
    			}
    		}
    	}else{
    		// CHECK VALUE 'first_name'
    		$validator = new Zend_Validate_Alnum(true);
    		if(!$validator->isValid($arrParam['first_name']))
    		{
    			$messages = $validator->getMessages();		
    			if(count($messages)>0){
    				foreach($messages as $key => $vaue){
    					$this->_messagesError[] = 'First name: ' . $vaue;
    				}
    			}
    			$arrParam['first_name'] = ''; 
    		}
    	}
    	
    // CHECK VALUE 'values'
    	$validator = new Zend_Validate_StringLength(3,32);
    	if(!$validator->isValid($arrParam['last_name']))
    	{
    		$messages = $validator->getMessages();	
    	
    		if(count($messages)>0){
    			foreach($messages as $key => $vaue){
    				$this->_messagesError[] = 'Last Name:' . $vaue;
    			}
    		}
    	}else{
    		// CHECK VALUE 'last_name'
    		$validator = new Zend_Validate_Alnum(true);
    		if(!$validator->isValid($arrParam['last_name']))
    		{
    			$messages = $validator->getMessages();		
    			if(count($messages)>0){
    				foreach($messages as $key => $vaue){
    					$this->_messagesError[] = 'Last Name: ' . $vaue;
    				}
    			}
    			$arrParam['last_name'] = ''; 
    		}
    	}
    
    	$validator = new Zend_Validate_Date();
    	$validator->setFormat('yyyy-mm-dd');
    	if(!$validator->isValid($arrParam['birthday'])){
    		$messages = $validator->getMessages();		
    		if(count($messages)>0){
    			foreach($messages as $key => $vaue){
    				$this->_messagesError[] = 'Birthday ' . $vaue;
    			}
    		}
    		$arrParam['birthday'] = '';
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
		
			if($this->_arrData['user_avatar'] != ''){
				//Upload file
				$upload = new Zendvn_File_Upload();
				$uploadDir = $this->_arrData['controllerConfig']['imagesDir'];
				
				$this->_arrData['user_avatar'] = $upload->upload('user_avatar',$uploadDir,array('task'=>'rename'));
				
				
				//Resize
				$img = Zendvn_File_Image::create($uploadDir . $this->_arrData['user_avatar']);
				$img->resize(80,80);
				$img->save($uploadDir . 'thumb/' . $this->_arrData['user_avatar']);
				if($this->_arrData['user_avatar_current'] != ''){
					@unlink($uploadDir .$this->_arrData['user_avatar_current']);
					@unlink($uploadDir . 'thumb/' . $this->_arrData['user_avatar_current']);
				}
			}
			else{
				if(isset($this->_arrData['user_avatar_current'])){
					$this->_arrData['user_avatar'] = $this->_arrData['user_avatar_current'];
				}
			}
		} 
		
		return $this->_arrData;
	}
	
	
}