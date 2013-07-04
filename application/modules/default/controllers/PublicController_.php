<?php
class PublicController extends Zendvn_Controller_Action{
	

	//Mang tham so nhan duoc o moi Action
	protected $_arrParam;
	
	//Duong dan cua Controller
	protected $_currentController;
	
	//Duong dan cua Action chinh
	protected $_actionMain;
	
	
	protected $_namespace;
	public function init(){
		//Mang tham so nhan duoc o moi Action
		$this->_arrParam = $this->_request->getParams();
		
		//Duong dan cua Controller
		$this->_currentController = '/' . $this->_arrParam['module'] 
									 . '/' . $this->_arrParam['controller'];
		
		//Duong dan cua Action chinh		
		$this->_actionMain = '/' . $this->_arrParam['module'] 
							 . '/' . $this->_arrParam['controller']	. '/index';	

		
		//Truyen ra view
		$this->view->arrParam = $this->_arrParam;
		$this->view->currentController = $this->_currentController;
		$this->view->actionMain = $this->_actionMain;	
	}
	
	/*public function errorAction(){
	   
        //$template_path = TEMPLATE_PATH . "/admin/system";
		//$this->loadTemplate($template_path,'template.ini','template');
        
        
        $template_path = TEMPLATE_PATH . "/public/gac";
		$this->loadTemplate($template_path,'template.ini','template');
                
		$this->view->Title = 'Message: Error!';
		$this->view->headTitle($this->view->Title,true);
        $this->view->errorMessage="Chức năng này đang trong giai đoạn hoàn thiện.";
		//$error[] = 'Chuc nang nay khong ton tai.';
		//$this->view->errors = $error;
		//echo 'Chuc nang nay khong ton tai';
        
        
		//$this->_helper->viewRenderer->setNoRender();
		//$this->_helper->layout->disableLayout();
	}
	*/
	public function errorAction()
    {
       // $template_path = TEMPLATE_PATH . "/admin/system";
		//$this->loadTemplate($template_path,'template.ini','template');
        
       
                
		$this->view->Title = 'Message: Error!';
		$this->view->headTitle($this->view->Title,true);
        
        $errors = $this->_getParam('error_handler');
        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:        
                // 404 error -- controller or action not found
                $this->getResponse()->setHttpResponseCode(404);
                $this->view->errorMessage = 'Page not found';
                break;
            default:
                // application error
                $this->getResponse()->setHttpResponseCode(500);
                $this->view->errorMessage = 'Application error';
                break;
        }

        $logger = Zend_Registry::get('logger');
		$params = $errors->request->getParams();
		 
		$paramsString = "array (\n";
		foreach($params as $key=>$value) {
		$paramsString .= "'".$key."' => '".$value."'\n";
		}
		$paramsString .= ")";
		 
		$logger->crit("ERROR = ".$this->view->message."\n"
		    ."MESSAGE = ".$errors->exception->getMessage()."\n"
		    ."STACK TRACE = \n".$errors->exception->getTraceAsString()."\n"
		    ."REQUEST PARAMS = ".$paramsString);
        
        // conditionally display exceptions
        if ($this->getInvokeArg('displayExceptions') == true) {
            $this->view->exception = $errors->exception;
        }
        
       // $this->view->request   = $errors->request;
    }
	
	
	
	
	
	public function noAccessAction(){
		$this->view->Title = 'No Access';
		$this->view->headTitle($this->view->Title,true);
		$error[] = 'Ban khong quyen truy cap vao chuc nang nay.';
		$this->view->errors = $error;
		//$this->_helper->viewRenderer('error');
		/*echo 'Ban khong co quyen truy cap vao chuc nang nay';
		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();*/
	}
	
	public function loginAction(){
		
		//load template login
		$template_path = TEMPLATE_PATH . "/admin/system";
		$sectionConfig = 'adminlogin';		
		$fileConfig='template.ini';
		$this->loadTemplate($template_path,$fileConfig, $sectionConfig);
	
		
		if($this->_request->isPost()){			
			//kiem tra tai khoan				
			$auth = new Zendvn_System_Auth();
		
			if($auth->login($this->_arrParam)){
				$info = new Zendvn_System_Info();
				$info->createInfo();				
					
				//$this->_redirect('default/admin/index');
				$linkRediret	=	$this->_arrParam['module'] . '/' . 
									$this->_arrParam['controller'] . '/' . 
									$this->_arrParam['action'];
				if($linkRediret == 'default/public/login')
					$this->_redirect('default/admin/index');
				else
					$this->_redirect($linkRediret);
			}			
	        else{
	        	$error[] = $auth->getError();
	        	$this->view->error =  "Tài khoản hoặc mật khẩu không đúng";
	        }
        }		
		
	}
	
	public function logoutAction(){
		
		$auth = new Zendvn_System_Auth();
		$auth->logout();
		
		$info = new Zendvn_System_Info();
		$info->destroyInfo();
		

		$this->_redirect('default/public/login');
		$this->_helper->viewRenderer->setNoRender();
		//$this->_helper->viewRenderer('error');
	}
	public function changePasswordAction(){
		$this->view->Title = 'Main :: Change password';
		$this->view->headTitle($this->view->Title,true);				
		
		
		if($this->_request->isPost()){
			$info 		=	new Zendvn_System_Info();
			$infoUser	=	$info->getInfo();
			
			
			$this->_arrParam['id']	=	$infoUser['member']['id'];
			
			$tablUser	=	new Default_Model_Users();
	    	$userInfo	=	$tablUser->getItem($this->_arrParam,array('task'=>'admin-edit'));
	    	
	    	$this->_arrParam['user_name'] 	= $userInfo['user_name'];
	    	$this->_arrParam['password'] 	= $userInfo['password'];
	    	
	    	
			$validate = new Default_Form_PasswordValidate($this->_arrParam);
			if($validate->checkError() == true){
				//echo 'loi';
				//Hien thi thong bao loi
				$this->view->errors = $validate->getMessageError();
				
			}else{
				//echo 'ok';
				//Luu vao database
				$tblUser  = new  Default_Model_Users();
				
				$tblUser->saveItem($validate->getData(),array('task'=>'admin-changepassword'));
				$this->view->errors = array('success');

			}			
			
		}
	}
}




