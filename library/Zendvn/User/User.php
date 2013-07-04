<?php
    class Zendvn_User_User extends Zend_Db{
        
        const TBL_USER              ='user';
        const TBL_SUFFIX_CHAR       ='_';
        const CLIENT_TABLE          ='~';
        const ANPHABET			    = "abcdefghijklmnopqrstuvwxyz";
        const OTHER_PREFIX_CHAR     ='_';
        
        protected $_db; 
        
        protected static $_instance = null;
                       
        public function __construct()
        {
           $this->_db = Zend_Registry::get('connectDb');    
           $this->_db->query("SET NAMES 'utf8'");
		   $this->_db->query("SET CHARACTER SET 'utf8'");
        } 
        
        
        public static function getInstance()
        {
            if (null === self::$_instance) {
                self::$_instance = new self();
            }
    
            return self::$_instance;
        }
        
        function checkUnique($email)
	    {	      
	        $result = $this->getCUserByEmail($email);
	        if($result){
	            return true;
	        }
	        return false;
	    }
     
        public function getCUserByEmail($email)
        {
            
           try{
           	$tblname = $this->getTableByAlphabet($email);
          
            $select = $this->_db->select()
                                ->from($tblname)
                                ->where('email=?',$email,STRING);                               
            $result = $this->_db->fetchAll($select);
           }catch(Exception $e)
           {
           	 
           }   
                       
           return $result;                                  
                    
        }
        public function getSimpleUserByID($userID)
        {
            if(!$userID)
            {
                return false;
            }
           
            $key = substr($userID,0,1);
            $table = $this->getTableByAlphabet($key);
            $user_id = 'user_id'; 

            $strSQL = 'SELECT * from '.$table.' WHERE '.$user_id . '= \''.$userID.'\'';
            //return $strSQL;
            $result = $this->_db->fetchRow($strSQL);           
            return $result;
            
        }
        public function getUserByMultiTable($userList,$selectField='')
        {
            if (!is_array($userList))
    		{
    			$userList = explode(",",$userList);	
    		}
    	
    		foreach ($userList as $key)
    		{
    			$arrDocInTable[substr($key,0,1)] .= "'$key',";
    		}
            foreach ($arrDocInTable as $iDoc)
    		{
    			$key = substr($iDoc,1,1);
    			$arrDocInTable[$key] = substr($iDoc,0,strlen($iDoc)-1);
                if (substr($iDoc,2,1)==self::CLIENT_TABLE)
    			{
    				$strSQL .= "(SELECT * FROM ".$this->getTableByAlphabet($key)." WHERE `user_id` IN (" . $arrDocInTable[$key] . ")) UNION ALL ";		
    			}    		
    		}
            if ($strSQL!="")
    		{
    			$strSQL = substr($strSQL,0,strlen($strSQL)-11);
    		}
    		if ($selectField=="")
    		{
    			$strSQL ="(select * from (".$strSQL.") as t_docs)";
    		}
    		else
    		{
    			$strSQL ="(select $selectField from (".$strSQL.") as t_docs)";
    		}
            
            $result = $this->_db->fetchAll($strSQL);           
            return $result;
        }
       
        public function createUser($userInfo)
        {  
        	$email = $userInfo['email'];
            $tblname = $this->getTableByAlphabet($email);
            $file = Zendvn_File_File::getInstance();
            $maxid = $file->getMaxID('c_user',0);           
            $userInfo['user_id'] = self::buildIDByAlphabet($maxid,$email);
            $userInfo['password'] = md5($userInfo['user_id'].md5($userInfo['password']));
            $userInfo['register_date'] = date('Y-m-d H:i:s');
           //$remote = new Zend_Http_PhpEnvironment_RemoteAddress();
            $userInfo['register_ip'] = $_SERVER['REMOTE_ADDR'];
                    
          try{          	  		    
            $rs = $this->_db->insert($tblname,$userInfo);
          }catch(Exception $e){          	
          	if($e->getCode() == 42) // base table doesn't exist
			{
				
				$tableName = self::getTableSuffixByAlphabet($email);
				$strTableCreate = "CREATE TABLE `user$tableName` (
								  `user_id` char(12) NOT NULL,
								  `full_name` text NOT NULL,
								  `avatar` text DEFAULT NULL,
								  `password` varchar(45) NOT NULL,
								  `email` varchar(45) NOT NULL,
								  `birthday` date DEFAULT NULL,
								  `register_date` datetime DEFAULT NULL,
								  `register_ip` varchar(20) DEFAULT NULL,
								  `visited_date` datetime DEFAULT NULL,
								  `visited_ip` varchar(20) DEFAULT NULL,
								  `active_date` datetime DEFAULT NULL,
								  `status` tinyint(4) NOT NULL DEFAULT '0',
                                  `type` tinyint(4) NOT NULL DEFAULT '0',
								  `sign` text,
								  `group_id` int(11) NOT NULL,
								  PRIMARY KEY (`user_id`)
								) ENGINE=MyISAM AUTO_INCREMENT=55 DEFAULT CHARSET=utf8;";
				$rs = $this->_db->query($strTableCreate);
				if($rs)
				{
					 $rs = $this->_db->insert($tblname,$userInfo);
				}
			}
          }
                  
           return $rs;     
        }
 
        
       public function buildIDByAlphabet($maxId,$content)
    	{
    		if ($content==null)
    		{
    			$prefix = "";
    		}
    		else
    		{
    			$prefix = strtolower(substr($content,0,1));
    			if (strpos(self::ANPHABET, $prefix)===false)
    			{
    				$prefix = self::OTHER_PREFIX_CHAR;
    			}
    		}
    		
    		return $prefix.self::CLIENT_TABLE.$maxId;
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
       
       public function generate_password($length = 20){
    	  $chars =  'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'.
    	            '0123456789';
    	  $str = '';
    	  $max = strlen($chars) - 1;
    
    	  for ($i=0; $i < $length; $i++)
    	    $str .= $chars[rand(0, $max)];
    	  return $str;
	   } 
    }


?>