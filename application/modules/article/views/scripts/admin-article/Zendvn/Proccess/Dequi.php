<?php
class Zendvn_Proccess_Dequi{
	
	public function dequi($arrSource, $parents = 0, $level = 1, &$newArray,$neededCols = array('id','name','parents') ){
		if(count($arrSource)>0){
			foreach($arrSource as $key => $value){
				if($value['parents'] == $parents){
					foreach ($neededCols as $key1){					
						$needArray[$key1] = $value[$key1];					
					}
					$needArray['level'] = $level;
				
					$newArray[] = $needArray;
					$newParent = $value['id'];		
					unset($arrSource[$key]);		
					$this->dequi($arrSource, $newParent, $level + 1, $newArray,$neededCols );
				}
			}
		}
	}
}