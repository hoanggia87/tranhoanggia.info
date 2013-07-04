<?php

class Zendvn_Mail_Mail
{
    protected $_subject;
    public $_content;
    
    protected $_templateFolder;
    protected $_templateFile;
    protected $_transport   =   null;
    
    protected $_objMail = null;
    
    /*
    * $arrParam['templateFile']
    * $arrParam['templateFolder'] =''
    * Neu su dung SMTP: ($option['smtp'] = 1)
    * $arrParam['smtpserver'] =''
    * $arrParam['user'] =''
    * $arrParam['pass'] =''
    * 
    * $option['smtp'] 
    */
    public function __construct($arrParam,$option=array('smtp'=>0))
    {
        if(!$arrParam['templateFolder'])
        {
            $this->_templateFolder = APPLICATION_PATH.'/mails/';
        }
        else
        {
            $this->_templateFolder = $arrParam['templateFolder'];
        }
        $this->_templateFile = $arrParam['templateFile'];
        if($option['smtp'])
        {
            $config = array('ssl' => 'tls',
                'auth' => 'login',
                'username' => $arrParam['user'],
                'password' => $arrParam['pass']);
                
            $this->_transport = new Zend_Mail_Transport_Smtp($arrParam['smtpserver'], $config);
        }
        else
        {
            $this->_transport = new Zend_Mail_Transport_Sendmail($arrParam['DKIM']);
        }
        Zend_Mail::setDefaultTransport($this->_transport);
        $this->_objMail = new Zend_Mail('utf-8');               
    }
    
    
    public function setArgumentValue($strQuery, $arrArguments)
	{
		$intCount = 0;
		
		foreach($arrArguments as $strArgument)
		{
		  //echo  $strArgument.'<br/>';
			$strQuery = str_replace('{' . $intCount++ . '}', ($strArgument), $strQuery);
            
		}
        	//$strQuery = str_replace("\r\n","", $strQuery);
		//echo $strQuery.'<br/>';
		return $strQuery;
        
	}
    
    public function formatMailContent($arrSubjectPara, $arrContentPara)
	{
	   
        if(!file_exists($this->_templateFolder.$this->_templateFile))
        {
            require_once 'Zend/Mail/Exception.php';
            throw new Zend_Mail_Exception(
                'Mail template is not Found in '.$this->_templateFolder.$this->_templateFile);
        }
              
		// Read mail template
		$mailTemplate = file_get_contents($this->_templateFolder.$this->_templateFile);
		
		// Return null if read file fail
		if (!$mailTemplate)
		{
			require_once 'Zend/Mail/Exception.php';
            throw new Zend_Mail_Exception(
                'Mail template is null in '.$this->_templateFolder.$this->_templateFile);
		}
		
		/************ Quy dinh ve format trong template mail ******************
		* - Hang dau tien la SUBJECT cua mail
		* - Tu hang thu 2 tro di moi la noi dung cua mail
		* *********************************************************************/
		
		$posEndFirstLine = strpos($mailTemplate, "\n");
		
		// Get and format subject
		$this->_subject = substr($mailTemplate, 0, $posEndFirstLine);
		if ($arrSubjectPara)
		{		  
			$this->_subject = $this->setArgumentValue($this->_subject,$arrSubjectPara);
		}
		
		// Get and format mail content
		$this->_content = substr($mailTemplate, $posEndFirstLine + 1);
		if ($arrContentPara)
		{
			$this->_content = $this->setArgumentValue($this->_content,$arrContentPara);
		}
		
	}
    
    public function setFrom($email,$name=null)
    {
        $this->_objMail->setFrom($email,$name);
    }

    public function setCc($email,$name=null)
    {
    	$this->_objMail->addCc($email,$name);
    }	
    public function setBc($email,$name = null )
    {
    	$this->_objMail->addBc($email,$name);
    }
	    
    public function setTo($email,$name=null)
    {
            $this->_objMail->addTo($email,$name);
    }
    
    
    public function sendmail()
    {
      $this->_objMail->setSubject($this->_subject); 
      $this->_objMail->setBodyHtml( $this->_content);
      return  $this->_objMail->send();
    }
        
    
}
?>