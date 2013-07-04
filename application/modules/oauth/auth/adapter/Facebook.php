<?php


class My_Auth_Adapter_Facebook implements Zend_Auth_Adapter_Interface
{
    
     private $_appId = null;

    /**
     * The application secret
     *
     * @var string
     */
    private $_secret = null;

    /**
     * The authentication scope (advanced options) requested
     *
     * @var string
     */
    private $_scope = null;

    /**
     * The redirect uri
     *
     * @var string
     */
    private $_redirectUri = null;
    
    
    public  $_facebook = null;
    
    public function __construct($appId, $secret, $redirectUri, $scope)
    {
        $this->_appId = $appId;
        $this->_secret = $secret;
        $this->_scope = $scope;
        $this->_redirectUri   = $redirectUri;
        
        $this->_facebook = new Zendvn_Facebook_Facebook(array(
          'appId'  => $appId,
          'secret' => $secret,
        ));
            
    }
    
    
    public function authenticate()
    {
    	// Get the request object.
    	//$frontController = Zend_Controller_Front::getInstance();
    	//$request = $frontController->getRequest();

    	// First check to see wether we're processing a redirect response.
    	//$code = $request->getParam('code');
        //$code = $this->_facebook->getCode();
        
        $user = $this->_facebook->getUser();
      
    	if (!$user)
    	{
	    	// Create the initial redirect
	    	$loginUri = $this->_facebook->getLoginUrl(array('redirect_uri'=>$this->_redirectUri,'scope'=>$this->_scope));
           echo $loginUri;
	    	header('Location: ' . $loginUri );
    	}
    	else
    	{
    		// Looks like we have a code. Let's get ourselves an access token
	    	/*$client = new Zend_Http_Client( My_Auth_Adapter_Facebook::TOKEN_URI );
	    	$client->setParameterGet('client_id', $this->_appId);
	    	$client->setParameterGet('client_secret', $this->_secret);
	    	$client->setParameterGet('code', $code);
	    	$client->setParameterGet('redirect_uri', $this->_redirectUri);

	    	$result = $client->request('GET');
	    	$params = array();
	    	parse_str($result->getBody(), $params);
            
            $access_token = $this->_facebook->getAccessTokenFromCode($code,$this->_redirectUri);
            
	    	// REtrieve the user info
	    	$client = new Zend_Http_Client(My_Auth_Adapter_Facebook::USER_URI );
	    	$client->setParameterGet('client_id', $this->_appId);
	    	$client->setParameterGet('access_token', $params['access_token']);
	    	$result = $client->request('GET');
	    	$user = json_decode($result->getBody());
    */
            $userprofile  = $this->_facebook->api('/me');
            $accessToken = $this->_facebook->getAccessToken();
            return new Zend_Auth_Result( Zend_Auth_Result::SUCCESS, $userprofile->id, array('user'=>$userprofile, 'token'=>$accessToken) );
    	}

        return new Zend_Auth_Result( Zend_Auth_Result::FAILURE, null, 'Error while attempting to redirect.' );
    }
    
    
    
}