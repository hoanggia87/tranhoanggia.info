<?php
    class Zendvn_User_User extends Zend_Db{
        
        const TBL_USER              ='c_user';
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
        public function getCUserByEmail($email)
        {
            
              
            $tblname = $this->getTableByAlphabet($email);
          
             $select = $this->_db->select()
                                ->from($tblname)
                                ->where('email=?',$email,STRING);                               
           $result = $this->_db->fetchAll($select);           
           return $result;                                  
                    
        }
        public function getSimpleUserByID($userID)
        {
            if(!$userID)
            {
                return false;
            }
            if(strpos($userID,'~'))
            {
                $key = substr($userID,0,1);
                $table = $this->getTableByAlphabet($key);
                $user_id = 'user_id'; 
            }
            else
            {
                $table = 'users';
                $user_id = 'id';
            }
            $strSQL = 'SELECT * from '.$table.' WHERE '.$user_id . '= \''.$userID.'\'';
            //return $strSQL;
            $result = $this->_db->fetchAll($strSQL);           
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
           //$this_db 
           $email = $userInfo['email'];
           $tblname = $this->getTableByAlphabet($email);
           $file = Zendvn_File_File::getInstance();
           $maxid = $file->getMaxID('c_user',0);           
           $userInfo['user_id'] = self::buildIDByAlphabet($maxid,$email);

          $rs = $this->_db->insert($tblname,$userInfo);       
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
       
        
    }


?>