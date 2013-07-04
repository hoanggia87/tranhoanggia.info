<?php
class Oauth_IndexController extends Zendvn_Controller_Action {


	public function init(){
	   
	}
		
	public function indexAction() {	
	   $this->_helper->layout->disableLayout();
	    $auth = Zend_Auth::getInstance();        
        if ($auth->hasIdentity()) {
            
            $identity = $auth->getIdentity();
          
            $email='';
            if (is_array($identity) && isset($identity['properties']['email'])) {
                 $properties = $identity['properties'];
                 $email= $properties['email'];                 
            // kiem tra du lieu trong c_user
                $auth = new Zendvn_System_Auth();	            
                $user = Zendvn_User_User::getInstance();
                $rs = $user->getCUserByEmail($email);                               
                if(!$rs)
                {
                    $userInfo['email'] = $email;
                    //google
                    if($properties['firstName'])
                    {
                        $full_name = $properties['firstName'].' '.$properties['lastName'];
                    }
                    // facebook
                    if($properties['name'])
                    {
                        $full_name = $properties['name'];
                        $picture  = $properties['picture'];
                        $time = strtotime($properties['birthday']);
                        $userInfo['birthday'] = date('Y-m-d',$time);
                        //$userInfo['birthday'] = $properties['birthday'];                          
                    }
                    //yahoo
                    if($properties['fullname'])
                    {
                         $full_name = $properties['fullname'];
                         $picture = $properties['image'];           
                    }
                    $userInfo['full_name'] = $full_name;
                    $userInfo['avatar'] = $picture;
                    $userInfo['type'] = 0;
                    $userInfo['status'] = 1;
                    $userInfo['password'] = $user->generate_password();
                    
                    $userInfo['active_date'] = date('Y-m-d H:i:s');
                    $user->createUser($userInfo);   
                    $authResult = $auth->login($userInfo);
                   // $userInfo = $user->getCUserByEmail($email);
                   // $userInfo = $userInfo[0];                  
                }
                else
                {                    
                    $userInfo = $rs[0];
                    $authResult = $auth->login($userInfo,array('passencode'=>1));
                }
                	if($authResult){
				      $info = new Zendvn_System_Info();
		              $info->createInfo();					
			     	}
                   
                $auth = Zend_Auth::getInstance();
    		    $infoAuth = $auth->getIdentity();
    		  
                                                      
              //$auth = Zend_Auth::getInstance();
             //$auth->getStorage()->write($userInfo);
                
                                
            }   
            
            $auth = Zend_Auth::getInstance(); 
              print_r($auth->getIdentity());
             $data =  $auth->getIdentity();
             $this->view->email = $data->email;   
             $this->view->identity = $userInfo;
             $this->view->name = $userInfo['full_name'];                    
    
        } else {
            $this->view->identity = null;
        }
	}
    
      	
}
