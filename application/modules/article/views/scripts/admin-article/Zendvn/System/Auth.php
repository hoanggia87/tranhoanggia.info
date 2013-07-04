<?php
class Zendvn_System_Auth{
	
	protected $_messageError = null;
	
	public function login($arrParam,$options = null){
		if($arrParam['user_name'] == '')
			return false;
		//1. Goi ket noi voi Zend Db
		$db = Zend_Registry::get('connectDb');
		
		//2.Khoi tao Zend Auth
		$auth = Zend_Auth::getInstance();
		
		//3 
		$authAdapter = new Zend_Auth_Adapter_DbTable($db);
		//Zend_Db_Adapter_Abstract $zendDb = null, $tableName = null, $identityColumn = null,
        //                        $credentialColumn = null, $credentialTreatment = null)
		$authAdapter->setTableName('users')
					->setIdentityColumn('user_name')
					->setCredentialColumn('password');
		$select = $authAdapter->getDbSelect();
		$select->where('status = 1');
		
		//$encode  = new Zendvn_Encode();
		$user_name = $arrParam['user_name'];
		$password = md5($arrParam['user_name'].$arrParam['password']);
		$authAdapter->setIdentity($user_name);
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