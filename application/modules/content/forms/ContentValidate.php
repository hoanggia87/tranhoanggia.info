<?php
class Content_Form_ContentValidate{
	
	//Mang chua cac cau thong bao error
	private 	$_messagesError = null;
	
	//Mang chua lieu sau khi validate	
	protected 	$_arrData;
	
	//Nhan cac gia tri tham so truy vao
	public function __construct($arrParam = array(),$option = 'add')
    {
    	    
    	// CHECK VALUE 'name' kiem tra do dai
    	$validator = new Zend_Validate_StringLength(3);
        if($arrParam['action'] == 'about')
        {
        	if(!$validator->isValid($arrParam['about']))
        	{
        		$messages = $validator->getMessages();	
        	
        		if(count($messages)>0){
        			foreach($messages as $key => $value){
        				$this->_messagesError[] = 'About:' . $value;
        			}
        		}
        		//$arrParam['about'] = ''; 
        	}
         }
        if($arrParam['action'] == 'license')
        {
            if(!$validator->isValid($arrParam['license']))
        	{
        		$messages = $validator->getMessages();	
        	
        		if(count($messages)>0){
        			foreach($messages as $key => $value){
        				$this->_messagesError[] = 'License:' . $value;
        			}
        		}
        		//$arrParam['license'] = ''; 
        	}
    	}
        if($arrParam['action'] == 'homepage')
        {
            if(!$validator->isValid($arrParam['hp_chrome']))
        	{
        		$messages = $validator->getMessages();	
        	
        		if(count($messages)>0){
        			foreach($messages as $key => $value){
        				$this->_messagesError[] = 'Homepage:chrome-' . $value;
        			}
        		}
        		//$arrParam['hp_chrome'] = ''; 
        	}
            if(!$validator->isValid($arrParam['hp_firefox']))
        	{
        		$messages = $validator->getMessages();	
        	
        		if(count($messages)>0){
        			foreach($messages as $key => $value){
        				$this->_messagesError[] = 'Homepage:firefox-' . $value;
        			}
        		}
        		//$arrParam['hp_ff'] = ''; 
        	}
            if(!$validator->isValid($arrParam['hp_ie']))
        	{
        		$messages = $validator->getMessages();	
        	
        		if(count($messages)>0){
        			foreach($messages as $key => $value){
        				$this->_messagesError[] = 'Homepage:ie-' . $value;
        			}
        		}
        		//$arrParam['hp_ie'] = ''; 
        	}
            if(!$validator->isValid($arrParam['hp_opera']))
        	{
        		$messages = $validator->getMessages();	
        	
        		if(count($messages)>0){
        			foreach($messages as $key => $value){
        				$this->_messagesError[] = 'Homepage:opera-' . $value;
        			}
        		}
        		//$arrParam['hp_opera'] = ''; 
        	}
            if(!$validator->isValid($arrParam['hp_safari']))
        	{
        		$messages = $validator->getMessages();	
        	
        		if(count($messages)>0){
        			foreach($messages as $key => $value){
        				$this->_messagesError[] = 'Homepage:safari-' . $value;
        			}
        		}
        		//$arrParam['hp_safari'] = ''; 
        	}
    	}
    	if($arrParam['action'] == 'unhomepage')
        {
            if(!$validator->isValid($arrParam['hp_chrome']))
        	{
        		$messages = $validator->getMessages();	
        	
        		if(count($messages)>0){
        			foreach($messages as $key => $value){
        				$this->_messagesError[] = 'Homepage:chrome-' . $value;
        			}
        		}
        		//$arrParam['hp_chrome'] = ''; 
        	}
            if(!$validator->isValid($arrParam['hp_firefox']))
        	{
        		$messages = $validator->getMessages();	
        	
        		if(count($messages)>0){
        			foreach($messages as $key => $value){
        				$this->_messagesError[] = 'Homepage:firefox-' . $value;
        			}
        		}
        		//$arrParam['hp_ff'] = ''; 
        	}
            if(!$validator->isValid($arrParam['hp_ie']))
        	{
        		$messages = $validator->getMessages();	
        	
        		if(count($messages)>0){
        			foreach($messages as $key => $value){
        				$this->_messagesError[] = 'Homepage:ie-' . $value;
        			}
        		}
        		//$arrParam['hp_ie'] = ''; 
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
		
		return $this->_arrData;
	}
	
}