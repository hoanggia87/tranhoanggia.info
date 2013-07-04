<?php
class Default_Form_PasswordValidate{
	
	//Mang chua cac cau thong bao error
	private 	$_messagesError = null;
	
	//Mang chua lieu sau khi validate	
	protected 	$_arrData;
	
	//Nhan cac gia tri tham so truy vao
	public function __construct($arrParam = array(),$option = 'add')
    {
    	//kiem tra pass cu
    	if(md5($arrParam['user_name'].$arrParam['old_password'])!=$arrParam['password']){
    		$this->_messagesError[] = 'password: current password not exactly';
    	}
    	
    	
    	//kiem tra do dai, cua pass moi
    	if(trim($arrParam['new_password']) != ''){
    		    		
    			$flag = true;
    		
    	}
    	if($flag == true){
	    	$validator = new Zend_Validate_StringLength(6,32);
	    	if(!$validator->isValid($arrParam['new_password']))
	    	{
	    		$messages = $validator->getMessages();		
	    		if(count($messages)>0){
	    			foreach($messages as $key => $vaue){
	    				$this->_messagesError[] = 'new password: ' . $vaue;
	    			}
	    		}
	    		$arrParam['new_password'] = '';    		
	    	}
    	}
    	//kiem tra password va go lai password xem co bang nhau ko
    	if($arrParam['new_password'] != $arrParam['re_new_password']){
    		$this->_messagesError[] = 'password and repassword not equal';
    		$arrParam['new_password'] = '';
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
		
		
		} 
		
		return $this->_arrData;
	}
	
	
}