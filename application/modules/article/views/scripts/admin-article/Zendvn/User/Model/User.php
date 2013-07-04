<?php
    class User_Model_User{
        
        const TBL_USER              ='c_user';
        const TBL_SUFFIX_CHAR       ='_';
        const CLIENT_TABLE          ='~';
        const ANPHABET			    = "abcdefghijklmnopqrstuvwxyz";
        const OTHER_PREFIX_CHAR     ='_';
        
        protected $_db; 
        
        protected static $_instance = null;
                       
        public function __construct($config)
        {
           $this->_db = Zend_Registry::get('connectDb');           
        } 
        
        public static function getInstance()
        {
            if (null === self::$_instance) {
                self::$_instance = new self();
            }
    
            return self::$_instance;
        }
        public function getCUserByEmail($email)
        {
            
              
            $tblname = $this->getTableByAlphabet($email);
          
             $select = $this->_db->select()
                                ->from($tblname)
                                ->where('email=?',$email,STRING);
           $result = $this->_db->fetchOne($select);
           return $result;                                  
                    
        }
       
        public function createUser($tblname,$userInfo)
        {          
           //$this_db 
           $rs = $this->_db->insert($tblname,$userInfo);
           return $rs;     
        }
       
       
       
       public function getTableSuffixByAlphabet($content)
    	{
    		//
    		if (strpos(self::ANPHABET,strtolower(substr($content,0,1)))===false)
    		{
    			$strPreChar = self::OTHER_PREFIX_CHAR;
    		}
    		else
    		{
    			$strPreChar = strtolower(substr($content,0,1));
    		}
    		
    		$strTableName = self::TBL_SUFFIX_CHAR.$strPreChar;
    		
    		return $strTableName;
    	} 
       
       public function getTableByAlphabet($content)
       {
            $tblSuffix = $this->getTableSuffixByAlphabet($content);
            return self::TBL_USER.$tblSuffix;
       }
       
        
    }


?>