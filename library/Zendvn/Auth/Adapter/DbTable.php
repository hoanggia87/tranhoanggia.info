<?php
class Zendvn_Auth_Adapter_DbTable extends Zend_Auth_Adapter_DbTable
{
	public function __construct($email)
	{			
		$db = Zend_Registry::get('connectDb');
		$user = Zendvn_User_User::getInstance();
		$tablename = $user->getTableByAlphabet($email);
		parent::__construct($db);		
		$this
					->setTableName($tablename)
					->setIdentityColumn('email')
                    ->setCredentialColumn('password')
                    ->setCredentialTreatment('MD5(CONCAT(user_id,MD5(?)))');
     
	}
}