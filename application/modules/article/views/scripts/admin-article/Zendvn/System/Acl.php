<?php
class Zendvn_System_Acl{
	
	protected $_acl;
	
	protected $_role;
	
	public function __construct($aclInfo = null, $options = null){
		//echo '<br>' . __METHOD__;
		
		if(!empty($aclInfo)){
			$acl = new Zend_Acl();
			$this->_role = $aclInfo['role'];
			$acl->addRole(new Zend_Acl_Role($this->_role));
			
			$groupPrivileges = $aclInfo['privileges'];
			$acl->allow($this->_role,null, $groupPrivileges);
			$this->_acl = $acl;
		}
		
		
	}
	
	public function isAllowed($arrParam = null){
		$ns = new Zend_Session_Namespace('info');
		$nsInfo = $ns->getIterator();
		$privilege = $arrParam['module'] . '_' . $arrParam['controller'] . '_' . $arrParam['action'];
		
		$flagAccess = false;
		if($this->_acl->isAllowed($this->_role,null,$privilege)){
			$flagAccess = true;
		}
		
		return $flagAccess;
		
	}
	
	public function createPrivilegeArray($opstions = null){
		//echo '<br>' . __METHOD__;
		$ns = new Zend_Session_Namespace('info');
		$nsInfo = $ns->getIterator();
		$info = $nsInfo['member'];
		$group_id = $info['group_id'];
				
		$db = Zend_Registry::get('connectDb');
		$select = $db->select()
					 ->from('privileges as p')
					 ->join('user_group_privileges as gp','gp.privilege_id = p.id')
					 ->where('status = 1')
					 ->where('group_id = ?',$group_id,INTEGER);
		$result = $db->fetchAll($select);			
		if(!empty($result)){
			$arrPrivilages = array();
			foreach ($result as $key){
				$arrPrivilages[] = $key['module'] . '_' . $key['controller'] . '_' . $key['action'];
			}
		}		
		$ns->acl['privileges']  = $arrPrivilages;
		
	}
	
	public function createRole($opstions = null){
		$ns = new Zend_Session_Namespace('info');
		$nsInfo = $ns->getIterator();
		$info = $nsInfo['group'];
		$group_name  = $info['group_name'];
		$ns->acl['role'] = $group_name;
		
	}
	
	
}