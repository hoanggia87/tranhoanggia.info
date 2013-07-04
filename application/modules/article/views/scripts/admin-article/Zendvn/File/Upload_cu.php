<?php
class Zendvn_File_Upload{
	
	/*
	 * param string | Ten tap file field upload 
	 * param string | Duong dan den thu muc upload
	 * param array	| Option of Upload
	 * param string | Ten moi cua file upload
	 */
	public function upload($fileName,$uploadDir,$option = null, $prefix = 'pic_'){
		try{
			$upload = new Zend_File_Transfer_Adapter_Http();		
			$upload->setDestination($uploadDir,$fileName);
			$fileInfo = $upload->getFileInfo();
		    
		    
			if($option == null){
				$newFileName = $fileInfo[$fileName]['name'];
				$upload->receive($fileName);
			}
			
			if($option['task'] == 'rename'){
				$newFileName = $fileInfo[$fileName]['name'];
				preg_match("/\.([^\.]+)$/", $newFileName, $matches); 
				$fileExtension = $matches[1];
				//Tao ten tap tin moi
				
				$countFix=0;
				//$newFileName = $prefix .$countFix. time() . '.' . $fileExtension;			
				do{
					//kiem tra su ton tai cua file, neu chua hop le thi van thuc hien				
					$newFileName = $prefix . $countFix . time() .  '.' . $fileExtension;
					$countFix++;
				}while(file_exists($uploadDir . $newFileName));
				//upload file
				$upload->addFilter('Rename', 
										array('target'=> $uploadDir . $newFileName,
										  'overwrite' => true
										  ));	
				$upload->receive($fileName);
			}
	    	return $newFileName;
		}catch(Zend_Exception $e){print_r($e);}
		
	}
	public function format(&$files){
			$names = array( 'name' => 1, 'type' => 1, 'tmp_name' => 1, 'error' => 1, 'size' => 1);
		
		    foreach ($files as $key => $part) {
		        // only deal with valid keys and multiple files
		        $key = (string) $key;
		        if (isset($names[$key]) && is_array($part)) {
		            foreach ($part as $position => $value) {
		                $files[$position][$key] = $value;
		            }
		            // remove old key reference
		            unset($files[$key]);
		        }
		    }
	}
    
    public function delUploadedFiles($basePath,$arrDir,$arrFile,$except=null)
    {
        try{
            foreach($arrDir as $dir)
        	{
            $path   =  $basePath.$dir;
            foreach($arrFile as $file)
            {
                if(!in_array($file,$except))
                {
                    $fullPath   = $path.$file;
                    unlink($fullPath);    
                }                
            }
        }
        }catch(exception $e){print_r($e);}
        
        
        
    }
}