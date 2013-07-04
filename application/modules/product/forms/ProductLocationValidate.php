<?php
class Product_Form_ProductLocationValidate{
	
	//Mang chua cac cau thong bao error
	private 	$_messagesError = null;
	
	//Mang chua lieu sau khi validate	
	protected 	$_arrData;
	
	//Nhan cac gia tri tham so truy vao
	public function __construct($arrParam = array(),$option = 'add')
    {
    	    
    	// CHECK VALUE 'name' kiem tra do dai
    	$validator = new Zend_Validate_StringLength(3,200);
    	if(!$validator->isValid($arrParam['name']))
    	{
    		$messages = $validator->getMessages();	
    	
    		if(count($messages)>0){
    			foreach($messages as $key => $vaue){
    				$this->_messagesError[] = 'Name:' . $vaue;
    			}
    		}
    		$arrParam['name'] = ''; 
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