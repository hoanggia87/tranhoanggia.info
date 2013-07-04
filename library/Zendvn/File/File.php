<?php
    
    class Zendvn_File_File
    {
        const APP_DATA = '';
        const APP_EXTENSION = '.app';
        const XML_EXTENSION = '.xml';
        const APP_LOCK_EXTENSION ='.lock';
        const FOLDER_FILES_MAXID ='/maxid/';
        
        protected static $_instance;
        protected $_filePath;
        protected $_maxidPath;
        //const   FILES_PATH = URL_APPLICATION.'/files/';
        public function __construct()
        {
            $this->_filePath = FILES_PATH;
            $this->_maxidPath = FILES_PATH.self::FOLDER_FILES_MAXID;
        }
        
        public static function getInstance()
        {
            if (null === self::$_instance) {
                self::$_instance = new self();
            }
    
            return self::$_instance;
        }
        public function getVar($fileName)
	    {
    		$iCount = 0;
    		$iMax = 150;
    		$iMaxLenCompareString = 20;
    		
    		$strFileName =  $fileName . self::APP_EXTENSION;
    		if (!self::checkExists($strFileName))
    		{	
    			return false;
    		}
    		
    		do
    		{
    			$strValue = file_get_contents($strFileName);
    			$strValue1 = file_get_contents($strFileName);
    			
    			$lenValue = strlen($strValue);
    			$lenValue1 = strlen($strValue1);
    			
    			// TODO: DoNguyen - nên d?o ngu?c s? hay hon: (!$strValue[0]) || (strlen($strValue) != strlen($strValue1))
    			if ((!$strValue[0]) || ($lenValue != $lenValue1) || ($lenValue<=$iMaxLenCompareString && $strValue != $strValue1))
    			{
    				$iCount++;
    				continue;
    			}
    			else
    			{
    			     $value = unserialize($strValue);											
    				return $value;
    			}
    			
    			
    		} while ($iCount <= $iMax);
    		
    
    		return null;
    		
    	}
     public function setVar($fileName, $value)
	 {
		$iCount = 0;
		$iMax = 150;
        
        $strFileName =$fileName . self::APP_EXTENSION;
   		$filePath = pathinfo($fileName);
        $fileName = $filePath['filename'];
        $filePath = $filePath['dirname'].'/';
        $lockFolder = $filePath.'temp/';
                
		if(!is_dir($lockFolder))
        {
            mkdir($lockFolder);
        }
	
		$strFileLock = $lockFolder . $fileName . self::APP_LOCK_EXTENSION;
		//serializ du lieu
		$strValue = serialize($value);
	
		do
		{
			$isLocked = fopen($strFileLock, 'x');
			if (!$isLocked)
			{
				$iCount++;			
				continue;
			}
			else
			{
				file_put_contents($strFileName, $strValue);
				fclose($isLocked);
				unlink($strFileLock);							
				return true;
			}
		} while ($iCount <= $iMax);
			
		if ($iCount > $iMax)
		{
			// Tru?ng h?p cúp di?n, m? l?i server, file lock dã có s?n, khi loop quá iMax l?n thì xóa file .lock di
			unlink($strFileLock);			
		}
		
		return false;
	 }
     
     public function getMaxID($strTableName,$readonly=1)
	 {
		$strFileName = $this->_maxidPath.$strTableName;
		//echo $strFileName;
		if(!file_exists($strFileName.'.app'))
		{
			// file not exist
          //  echo 'file not exist'; 
			$arrResult = 0;
			$result = self::setVar($strFileName, $arrResult);
			if ($result == false)
			{
				return false;	
			}
		}
		$arrResult = self::getVar($strFileName);
		if ($readonly)
		{
			return $arrResult;	
		}
		$firstId = $arrResult+1;
		
		$iswrite = self::setVar($strFileName,$firstId);
		if($iswrite)
		{
			return $firstId;
		}
		
		return false;
		
	 }
     
 
     
     private function checkExists($filePath)
  	 {
		clearstatcache();
		return is_file($filePath);
	 }   
        
    }
    
    

?>