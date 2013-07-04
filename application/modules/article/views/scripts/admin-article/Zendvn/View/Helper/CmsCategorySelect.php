<?php
class Zendvn_View_Helper_CmsCategorySelect extends Zend_View_Helper_Abstract{
	
		public function cmsCategorySelect($name,$val,$options, $attribs = null){
				
		        $strAttribs = '';
		        if($attribs != null){
		         	foreach($attribs  as $key => $info){
		         		$strAttribs .=  ' ' . $key . '="' . $info . '" ';
		         	}
		         }  
		      	
				$str = '';
				if(count($options)>0){
					foreach ($options as $key => $value){
						if($value['level'] == 0){
							$value['name'] = $value['name'];
						}else if($value['level'] == 1){
							$value['name'] = ' + ' . $value['name'];
						}else{
							$string = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
							$newString = '';
							for($i=1; $i<$value['level'];$i++){
									$newString .= $string;
							}
							$value['name'] = $newString . '|- ' . $value['name'];
						}
						if($val == $value['id']){
							$select = ' selected ';
							$str .= '<option value="' .$value['id'] . '" ' . $select . ' >' . $value['name'] . '</option>';
						}else{
							$str .= '<option value="' .$value['id'] . '" >' . $value['name'] . '</option>';
						}
					}
				}
											
				$xhtml = '<select name="' . $name . '" id="' . $name . '" ' . $strAttribs . '>'
						 . $str . '</select>';
				return $xhtml;
		
			}
	
}