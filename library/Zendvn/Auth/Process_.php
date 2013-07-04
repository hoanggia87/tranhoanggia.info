<?php
class Zendvn_Auth_Process{
	
	protected $_flagAccess = false;
	
	public function __construct($account,$password){
			$auth = Zend_Auth::getInstance ();
			
			//2 
			$db = Zend_Registry::get('connectDb');
			
			//3
			$authAdapter = new Zend_Auth_Adapter_DbTable ( $db );
			$authAdapter->setTableName('users')
						->setIdentityColumn('user_name')
						->setCredentialColumn('password');
						
			$password= md5($account . $password);
			
			$authAdapter->setIdentity($account);
			$authAdapter->setCredential($password);
			
			$select = $authAdapter->getDbSelect();
			$select->where('status = 1');
			
			$result = $auth->authenticate ( $authAdapter );
			
			$flag = false;
			if($result->isValid ()){
				
				$omitColumns = array('password');
				$data = $authAdapter->getResultRowObject(null,$omitColumns);
				$auth->getStorage()->write( $data );
				$flag = true;
			}
			
			$this->_flagAccess = $flag;
	}
	
	public function login(){
		return $this->_flagAccess;
	}
	
	public function logout(){
		$auth  = Zend_Auth::getInstance(); 
		$this->_flagAccess = false;
		$auth->clearIdentity();
	}
}