<?php
class Zendvn_View_Helper_CmsRewriteLink extends Zend_View_Helper_Abstract{
    
    public function cmsRewriteLink($arrParam,$options=null)
    {
        if($options['task']=='product-detail')
        {
            //thoidaivang.themuasam.info/product/index/detail/id/100/name/abc
            //$this->noSign($options['local_name']).'/'.$this->noSign($cat_name).'-'.$cat.'/' . $this->noSign($name) . '-'.$id.'.tms';            
            return $this->noSign($arrParam['name']).'-'.$arrParam['id'].'.html';                                      
        }
        if($options['local-name']) 
        {
/*
 array(
 		array('name'=>'param1'
 				'id'=>123
 			  ) : key là name thì sẽ được noSign
 		array('name'=>'param2'
 				'id'=>123
 			  ) : key là name thì sẽ được noSign
		)
*/
        	
        	$url = '/';                   
        	foreach($arrParam as $offset)
        	{
        		if($offset['name'])
        		{
        			$offset['name'] = $this->noSign($offset['name']);
        		}
        		$url.= $offset['name'].'-'.$offset['id'].'/';	
        	}
        	
        	$url = substr($url,0,-1);
        	if($options['extend'])
        	{        		
        		$url.='.'.$options['extend'];
        	}
        	
        	return ($options['local-name'].$url);
        }
    }
    
    public static function noSign($str) 
	{		
		$result = '';
		$isSpace = 0;
		$arr_unicode = self::getArraycompositeUnicodeToLatin();
	
		$str = mb_strtolower($str,'utf-8');		
		$len = mb_strlen($str,'utf-8');				
		for($i=0;$i<$len;$i++)
		{			
			$char =mb_substr($str, $i, 1,'utf-8');	
			if($char==' '||$char=='-')
			{				
				if($isSpace==0)
				{
					$result.="-";
				}				
				$isSpace = 1;					
			}
			else if(array_key_exists($char,$arr_unicode))
			{	
			     
				$result.=$arr_unicode[$char];				
				$isSpace = 0;
			}			
		}					
		return $result;
		
	}
    public function getArraycompositeUnicodeToLatin()
	{
		return array('a'=>'a','á'=>'a','à'=>'a','â'=>'a','ă'=>'a','ã'=>'a','ấ'=>'a','ầ'=>'a','ắ'=>'a','ằ'=>'a','ẫ'=>'a','ẵ'=>'a',
				'ả'=>'a','ẩ'=>'a','ẳ'=>'a','ạ'=>'a','ậ'=>'a','ặ'=>'a','b'=>'b','c'=>'c','e'=>'e','f'=>'f','g'=>'g','h'=>'h',
				'é'=>'e','è'=>'e','ê'=>'e','ẽ'=>'e','ế'=>'e','ề'=>'e','ễ'=>'e','ẻ'=>'e','ể'=>'e','ẹ'=>'e','ệ'=>'e',
				'i'=>'i','í'=>'i','ì'=>'i','ĩ'=>'i','ỉ'=>'i','ị'=>'i','j'=>'j','k'=>'k','l'=>'l','m'=>'m','n'=>'n','o'=>'o',
				'ó'=>'o','ò'=>'o','ô'=>'o','õ'=>'o','ố'=>'o','ồ'=>'o','ỗ'=>'o','ỏ'=>'o','ơ'=>'o','ổ'=>'o','ọ'=>'o',
				'ớ'=>'o','ờ'=>'o','ỡ'=>'o','ộ'=>'o','ở'=>'o','ợ'=>'o','u'=>'u','q'=>'q','r'=>'r','s'=>'s','t'=>'t','z'=>'z','v'=>'v','p'=>'p',
				'ú'=>'u','ù'=>'u','ũ'=>'u','ủ'=>'u','ư'=>'u','ụ'=>'u','ứ'=>'u','ừ'=>'u','ữ'=>'u','ử'=>'u','ự'=>'u',
				'ý'=>'y','ỳ'=>'y','ỹ'=>'y','ỷ'=>'y','y'=>'y','d'=>'d','-'=>'-','w'=>'w','x'=>'x',
				'đ'=>'d','1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7','8'=>'8','9'=>'9','0'=>'0');
	}
}
?>