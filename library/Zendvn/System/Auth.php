<?php
class Zendvn_System_Auth{
	
	protected $_messageError = null;
	var $_options;
    public function __construct($options = null)
    {
        $this->_options = $options;
    }
	public function login($arrParam,$options = null){		
		
		//2.Khoi tao Zend Auth
		$auth = Zend_Auth::getInstance();
	
		$password = $arrParam['password'];
		$email  = $arrParam['email']; 
		$authAdapter = new Zendvn_Auth_Adapter_DbTable($email);
		$authAdapter->setIdentity($email);
		if($options['passencode'] != null)
		{
			$authAdapter->setCredentialTreatment('?');	
		}
		$authAdapter->setCredential($password);
			
		//Lay ket qua truy van cua Zend_Auth
		$result = $auth->authenticate($authAdapter);
		
		$flag = false;
		if(!$result->isValid()){
				$error = $result->getMessages();
				$this->_messageError = current($error);
		}else{			
			$omitColumns = array('password');
			$data = $authAdapter->getResultRowObject(null,$omitColumns);	
			$auth->getStorage()->write($data);	
			$flag = true;
		}
		
		return $flag;
	}
	
	public function getError(){
		return $this->_messageError;
	}
	
	public function logout($arrParam = null,$options = null){
		$auth = Zend_Auth::getInstance();
		$auth->clearIdentity();
	}
}