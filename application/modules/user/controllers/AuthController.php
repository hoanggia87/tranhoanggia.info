<?php
class User_AuthController extends Zend_Controller_Action {
	
	public function indexAction()
	{
		
	}
	public function loginAction()
    { 
       	$users = Zendvn_User_User::getInstance();
        $form = new User_Form_LoginForm();
        $this->view->form = $form;
        if($this->getRequest()->isPost()){
            if($form->isValid($_POST)){
                $data = $form->getValues();
	            $auth = new Zendvn_System_Auth();			
				if($auth->login($data)){					
					$this->_redirect('user/auth/home');
				}			
		        else{
		        	$error[] = $auth->getError();
		        	$this->view->errorMessage = "Invalid email or password. Please try again.";
		        }                               
            }
        }
    } 
    public function signupAction()
    {
     
    	$users = Zendvn_User_User::getInstance();
        $form = new User_Form_RegistrationForm();
        $this->view->form=$form;
        if($this->getRequest()->isPost()){
            if($form->isValid($_POST)){
                $data = $form->getValues();
                if($data['password'] != $data['confirmPassword']){
                    $this->view->errorMessage = "Password and confirm password don't match.";
                    return;
                }
                if($users->checkUnique($data['email'])){
                    $this->view->errorMessage = "Email already taken. Please choose      another one.";
                    return;
                }
                unset($data['confirmPassword']);
                $users->createUser($data);
                $this->_redirect('user/auth/login');
            }
        }
    } 
    public function logoutAction()
    {
     
    	$storage = new Zend_Auth_Storage_Session();
        $storage->clear();
        $this->_redirect('user/auth/login');
    }
    public function homeAction()
    {
    	 
    	$auth = Zend_Auth::getInstance();
		$infoAuth = $auth->getIdentity();
		print_r($infoAuth);
			
    	$storage = new Zend_Auth_Storage_Session();
        $data = $storage->read();
        if(!$data){
            $this->_redirect('user/auth/login');
        }
        $this->view->email = $data->email; 
    }
}

?>