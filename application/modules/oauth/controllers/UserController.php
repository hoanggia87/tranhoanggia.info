<?php
include( APPLICATION_PATH."/modules/oauth/forms/LoginForm.php");
include(APPLICATION_PATH."/modules/oauth/forms/RegistrationForm.php");
class Oauth_UserController extends Zend_Controller_Action {

    /**
     * Application keys from appkeys.ini
     * 
     * @var Zend_Config 
     */
    protected $_keys;

    public function init() {        
        $this->_keys = Zend_Registry::get('keys');
        
    }

    public function indexAction() {
        // action body
        $this->_helper->layout->disableLayout();
    }

    public function loginAction() {
    	$this->_helper->layout->disableLayout();
        // get an instace of Zend_Auth
        $auth = Zend_Auth::getInstance();
        // check if a user is already logged
        if ($auth->hasIdentity()) {
            $this->_helper->FlashMessenger('It seems you are already logged into the system ');
            return $this->_redirect('/oauth/user/index/index');
        }

        // if the user is not logged, the do the logging
        // $openid_identifier will be set when users 'clicks' on the account provider
        $openid_identifier = $this->getRequest()->getParam('openid_identifier', null);

        // $openid_mode will be set after first query to the openid provider
        $openid_mode = $this->getRequest()->getParam('openid_mode', null);

        // this one will be set by facebook connect
        $code = $this->getRequest()->getParam('code', null);

        // while this one will be set by twitter
        $oauth_token = $this->getRequest()->getParam('oauth_token', null);
    
        $form = new Oauth_Form_LoginForm();
        $this->view->form = $form;
        if($this->getRequest()->isPost()){
            if($form->isValid($_POST)){
                $data = $form->getValues();
	            $auth = new Zendvn_System_Auth();			
				if($auth->login($data)){
				    $info = new Zendvn_System_Info();
				    $info->createInfo();
					$this->_redirect('/oauth/index/index');
				}			
		        else{
		        	$error[] = $auth->getError();
		        	$this->view->errorMessage = "Invalid email or password. Please try again.";
		        }                               
            }
        }
        
        // do the first query to an authentication provider
        if ($openid_identifier) {
            
             $ns = new Zend_Session_Namespace('Provider',true);
        
            if ('https://www.twitter.com' == $openid_identifier) {
                $adapter = $this->_getTwitterAdapter();
            } else if ('https://www.facebook.com' == $openid_identifier) {
                $adapter = $this->_getFacebookAdapter();
                
                // session luu tru dang dang nhap
                 $ns->type = 2;
                // facebook
                
               
            } else {
                // for openid
                $adapter = $this->_getOpenIdAdapter($openid_identifier);

                // specify what to grab from the provider and what extension to use
                // for this purpose
                $toFetch = $this->_keys->openid->tofetch->toArray();
                
                // for google and yahoo use AtributeExchange Extension
                if ('https://www.google.com/accounts/o8/id' == $openid_identifier || 'http://me.yahoo.com/' == $openid_identifier) {
                    if('https://www.google.com/accounts/o8/id' == $openid_identifier)
                    {
                        //gmail
                        $ns->type = 1;    
                    }
                    else
                    {
                        //yahoo
                        $ns->type = 3;
                    }
                    
                    
                    $ext = $this->_getOpenIdExt('ax', $toFetch);
                } else {
                    $ext = $this->_getOpenIdExt('sreg', $toFetch);
                }

                $adapter->setExtensions($ext);
            }

            // here a user is redirect to the provider for loging
            $result = $auth->authenticate($adapter);
        
      
        
            $this->_helper->FlashMessenger('Redirection faild');
            return $this->_redirect('/oauth/index/index');
            
            
            
        } else if ($openid_mode || $code || $oauth_token) {
            // this will be exectued after provider redirected the user back to us

            if ($code) {

                // for facebook
                $adapter = $this->_getFacebookAdapter();

            } else if ($oauth_token) {
                // for twitter
                $adapter = $this->_getTwitterAdapter()->setQueryData($_GET);
            } else {
                // for openid                
                $adapter = $this->_getOpenIdAdapter(null);

                // specify what to grab from the provider and what extension to use
                // for this purpose
                $ext = null;
                
                $toFetch = $this->_keys->openid->tofetch->toArray();     
          
                // for google and yahoo use AtributeExchange Extension
                if (isset($_GET['openid_ns_ext1']) || isset($_GET['openid_ns_ax'])) {                    
                    $ext = $this->_getOpenIdExt('ax', $toFetch);
                } else if (isset($_GET['openid_ns_sreg'])) {
                    $ext = $this->_getOpenIdExt('sreg', $toFetch);
                }

                if ($ext) {
                    $ext->parseResponse($_GET);
                    $adapter->setExtensions($ext);
                }
            }
         
		 	$result = $auth->authenticate($adapter);
	   
            if ($result->isValid()) {
                $toStore = array('identity' => $auth->getIdentity());

                if ($ext) {
        
                    // for openId
                    $toStore['properties'] = $ext->getProperties();
                    $toStore['properties']['provider'] = 'provider' ;
                } else if ($code) {                
                    // for facebook
                    $msgs = $result->getMessages();
                    
                    $ns = new Zend_Session_Namespace('Provider',true);  
                    $ns->token = $msgs['token'];
                      
                    $client = new Zend_Http_Client();
            		$client->setUri('https://graph.facebook.com/me/picture');
            		$client->setParameterGet('access_token',$msgs['token']);            		
           			$client->setConfig(array('maxredirects'=>0));
        			$response = $client->request()->getHeader('Location');                   
        			$client->setConfig(array('maxredirects'=>2));
                    $msgs['user']['provider'] = 'provider' ;  
                    $msgs['user']['picture'] = $response;                          		
                    $toStore['properties'] = (array) $msgs['user'];                    
                } else if ($oauth_token) {
                    // for twitter
                    $identity = $result->getIdentity();
                    // get user info
                    $twitterUserData = (array) $adapter->verifyCredentials();
                    $toStore = array('identity' => $identity['user_id']);
                    if (isset($twitterUserData['status'])) {
                        $twitterUserData['status'] = (array) $twitterUserData['status'];
                    }
                    $toStore['properties'] = $twitterUserData;
                }
                
                $auth->getStorage()->write($toStore);

                $this->_helper->FlashMessenger('Successful authentication');
                return $this->_redirect('/oauth/index/index');
            } else {
                $this->_helper->FlashMessenger('Failed authentication');
                $this->_helper->FlashMessenger($result->getMessages());
                return $this->_redirect('/oauth/index/index');
            }
        }
        
        
    }
    
    public function signupAction()
    {
     
    	$users = Zendvn_User_User::getInstance();
        $form = new Oauth_Form_RegistrationForm();
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
                $this->_redirect('oauth/user/login');
            }
        }
    } 

    public function logoutAction() {
    	$this->_helper->layout->disableLayout();
        $auth = Zend_Auth::getInstance();
        $auth->clearIdentity();
        $this->_helper->FlashMessenger('You were logged out');
        Zend_Session:: namespaceUnset('Provider');
        extract($this->_keys->facebook->toArray());
        $facebook = new Zendvn_Facebook_Facebook(array('appId'  => $appid,'secret' => $secret));
        $facebook->destroySession();
        return $this->_redirect('/oauth/user/login');
    }

    /**
     * Get My_Auth_Adapter_Facebook adapter
     *
     * @return My_Auth_Adapter_Facebook
     */
    protected function _getFacebookAdapter() {
        extract($this->_keys->facebook->toArray());
        return new My_Auth_Adapter_Facebook($appid, $secret, $redirecturi, $scope);
    }

    /**
     * Get My_Auth_Adapter_Oauth_Twitter adapter
     *
     * @return My_Auth_Adapter_Oauth_Twitter
     */
    protected function _getTwitterAdapter() {
        extract($this->_keys->twitter->toArray());
        return new My_Auth_Adapter_Oauth_Twitter(array(), $appid, $secret, $redirecturi);
    }

    /**
     * Get Zend_Auth_Adapter_OpenId adapter
     *
     * @param string $openid_identifier
     * @return Zend_Auth_Adapter_OpenId
     */
    protected function _getOpenIdAdapter($openid_identifier = null) {
        $adapter = new Zend_Auth_Adapter_OpenId($openid_identifier);
        $dir = APPLICATION_PATH . '/../tmp';

        if (!file_exists($dir)) {
            if (!mkdir($dir)) {
                throw new Zend_Exception("Cannot create $dir to store tmp auth data.");
            }
        }
        $adapter->setStorage(new Zend_OpenId_Consumer_Storage_File($dir));

        return $adapter;
    }

    /**
     * Get Zend_OpenId_Extension. Sreg or Ax. 
     * 
     * @param string $extType Possible values: 'sreg' or 'ax'
     * @param array $propertiesToRequest
     * @return Zend_OpenId_Extension|null
     */
    protected function _getOpenIdExt($extType, array $propertiesToRequest) {

        $ext = null;

        if ('ax' == $extType) {
            $ext = new My_OpenId_Extension_AttributeExchange($propertiesToRequest);
        } elseif ('sreg' == $extType) {
            $ext = new Zend_OpenId_Extension_Sreg($propertiesToRequest);
        }

        return $ext;
    }

}

