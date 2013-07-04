<?php
class Zendvn_Plugin_Permission extends Zend_Controller_Plugin_Abstract{
 
	public function preDispatch(Zend_Controller_Request_Abstract $request){
		//echo '<br>' . __METHOD__;
		
		$linkAction=$this->_request->getModuleName().'/'.
								$this->_request->getControllerName().'/'.
								$this->_request->getActionName();
		
		if($linkAction != 'photo/admin-photo/uploadify'){//fix uploadify *** 						
				$auth = Zend_Auth::getInstance();
				
				$moduleName = $this->_request->getModuleName();
				$controllerName  = $this->_request->getControllerName();
				
				
					//----------START-KIEM TRA QUYEN TRUY CAP VAO ADMIN -------------
					$flagAdmin = false;
					if($controllerName == 'admin'){
						$flagAdmin = true;
					}else{
						$tmp = explode('-',$controllerName);
						if($tmp[0] == 'admin'){
							$flagAdmin = true;
						}
					}
					$flagPage = 'none';
					
					if($flagAdmin == true){
						if($auth->hasIdentity() == false){//neu chua dang nhap thi hien trang login
							$flagPage = 'login';
						}else{//neu dang nhap rui thi kiem tra quyen truy cap
							
							
							
							
							$info = new Zendvn_System_Info();
							$group_acp = $info->getGroupInfo('group_acp');
						if($group_acp != 1){
							$flagPage = 'no-access';
						}else{
							$permission  = $info->getGroupInfo('permission');
							if($permission != 'Full Access'){
								$aclInfo  = $info->getAclInfo();
								
								$acl = new Zendvn_System_Acl($aclInfo);
								$arrParam = $this->_request->getParams();
								if($acl->isAllowed($arrParam) == false){
									$flagPage = 'no-access';
								}
								
							}
							
						}
						}
					}
					/*$ns = new Zend_Session_Namespace('info');
					$nsInfo = $ns->getIterator();
								echo '<pre>';
									print_r($nsInfo);
								echo '</pre>';*/
					//----------END-KIEM TRA QUYEN TRUY CAP VAO ADMIN -------------
				
					if($flagPage != 'none'){
					if($flagPage == 'login'){
						$this->_request->setModuleName('default');
						$this->_request->setControllerName('public');
						$this->_request->setActionName('login');
					}
					
					if($flagPage == 'no-access'){
						$this->_request->setModuleName('default');
						$this->_request->setControllerName('public');
						$this->_request->setActionName('no-access');
					}
				}
		    }
	}
}

