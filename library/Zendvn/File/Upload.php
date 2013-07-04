<?php
class Zendvn_File_Upload{
	
	/*
	 * param string | Ten tap file field upload 
	 * param string | Duong dan den thu muc upload
	 * param array	| Option of Upload
	 * param string | Ten moi cua file upload
	 */
	public function upload($fileName,$uploadDir,$typeRename='',$refix='',$overwrite=false){
		try{
			$upload = new Zend_File_Transfer_Adapter_Http();		
			$upload->setDestination($uploadDir,$fileName);
			$fileInfo = $upload->getFileInfo();

		    $newFileName=$fileInfo[$fileName]['name'];
			if($typeRename)
			{
				$newFileName=self::re_name($fileInfo[$fileName]['name'],$typeRename,$refix);
				//$upload->receive($fileName);
			}

			$isLoop=0;
			$fileRN='';
			if(!$overwrite)
			{
				while(file_exists($uploadDir . $newFileName))
				{
					$isLoop++;
					//$fileRN=self::re_name($fileInfo[$fileName]['name'],'loopName','_'.$countFix);


					//kiem tra su ton tai cua file, neu chua hop le thi van thuc hien				
					$newFileName = self::re_name($fileInfo[$fileName]['name'],'loopName','_'.$countFix);
					$countFix++;
				}
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

			//upload file
			$upload->addFilter('Rename', 
									array('target'=> $uploadDir . $newFileName,
									  'overwrite' => true
									  ));	
			$upload->receive($fileName);

			

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
    public function re_name($fileName, $type='', $refix='')
	{
		$fileRename='';
		//lấy phần mở rông của file
		$ext = self::getExt($fileName);
		//lay phan name
		$name=substr($fileName, 0, strlen($fileName)-strlen($ext)-1);//trừ thêm dấu .
		//common_functions::writeLog('File ext: '.$ext.' - File name: '.$name,0,'test_upload.php');
		switch($type)
		{
			case 'dateTime':	
				//tao 5 tên file theo time tính bằng giây
				$fileRename=time().'.'.$ext;
				break;
			case 'loopName';
				
				//tao lai ten file
				$fileRename=$name.$refix.'.'.$ext;
				break;
			case 'optionName':
				$fileRename=$refix.'.'.$ext;
			break;	
			default:
				$fileRename=$fileName;
				break;
		}
		return $fileRename;
	}
	/**
	 * Lấy phần mở rộng của file
	 *
	 * @param string $fileName tên file
	 * @return string trả về phần mở rộng của file
	 *
	 */
	public function getExt($fileName)
	{
		preg_match("/\.([^\.]+)$/", $fileName, $matches); 
		return $matches[1];
	}
	public function getName($fileName)
	{
		preg_match("/\.([^\.]+)$/", $fileName, $matches); 
		return substr($fileName, 0, strlen($fileName)-strlen($matches[1])-1);//trừ thêm dấu .
	}
	/**
	 * hàm kiểm tra file có hợp lệ hay không
	 *
	 * @param string $controlName tên control mà dưới client post lên
	 * @param array $type những điều kiện để kiểm tra file (file type) 
	 *										('audio/mp3','audio/mp4','image/jpg','image/png') kiểu mime
	 * @param array $size những điều kiện để kiểm tra file (file size)	102402030
	 * @return bool hợp lệ: true | không hợp lệ: false
	 *
	 */
	public function checkFile($controlName, $maxSize=0, $type='')
	{		
		
		$ftype	= $_FILES[$controlName]["type"];
		$fsize	= $_FILES[$controlName]["size"];
		$fname	= $_FILES[$controlName]["name"];
		$fext	= strtolower(self::getExt($fname));
		
		//nếu file upload bị lỗi
		if($_FILES[$controlName]["error"] > 0)
		{
			return false;
		}
		
		if($type)
		{
			
			//kiểm tra phần mở rộng lấy từ tên file
			if(!in_array($this->_mime[$fext], $type))
			{
				return false;
			}

			
		}
		
		if($maxSize != 0)
		{
			//nếu size vượt quá giới hạn thì return false
			if($fsize>$maxSize)
			{
				return false;	
			}
		}
		return true;
	}
}