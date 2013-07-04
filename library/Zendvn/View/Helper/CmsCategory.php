<?php
class Zendvn_View_Helper_CmsCategory extends Zend_View_Helper_Abstract{
	
	function formatList($arrSource, $parents = 0, $level = 1, &$newArray ){
		if(count($arrSource)>0){
			foreach($arrSource as $key => $value){
				if($value['parents'] == $parents){
					$value['level'] = $level;					
					$newArray[] = $value;
					$newParent = $value['id'];		
					unset($arrSource[$key]);		
					$this->formatList($arrSource, $newParent, $level + 1, $newArray );
				}
			}
		}
	}
	
	function formSelect($name,$val,$options, $attribs = null){		
		
        $strAttribs = '';
        if($attribs != null){
         	foreach($attribs  as $key => $info){
         		$strAttribs .=  ' ' . $key . '="' . $info . '" ';
         	}
         }  
      	
		$str = '';
		if(count($options)>0){
			foreach ($options as $key => $value){
				
				//insert status
				$status=' (ẩn) ';
				if($value['status']==1)
					$status=' (hiện) ';
				
				if($value['level'] == 0){
					$value['name'] = $value['name'].' - '.$status;
				}else if($value['level'] == 1){
					$value['name'] = ' + ' . $value['name'].' - '.$status;
				}else{
					$string = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
					$newString = '';
					for($i=1; $i<$value['level'];$i++){
							$newString .= $string;
					}
					$value['name'] = $newString . '|- ' . $value['name'].' - '.$status;
				}
				//check selected
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

	
	function createMenuHeader($menu,$parents_id,&$newMenu,$objView,$class='')	
    {
    	if(count($menu)>0)
        {            
            //echo '@'.count($menu).'@<br>'; 
            $rw=new Zendvn_View_Helper_CmsRewriteLink();
            
    		
            $ft=1;
    		foreach($menu as $key => $val)
            {
    			if($val['parents'] == $parents_id )
                {				
                    if($ft==1)
                    {
                        $classC='';
                        if($newMenu=='')
                        {
                            $classC='class="'.$class.'"';    
                        }
                        
                        $newMenu .= '<ul '.$classC.'>';
                        $ft=0;
                    }
                    $info=$val;
                    $info['cat_id']=$info['id'];
                    $info['cat_name']=$rw->noSign($info['name']);
                    if($info['level']==1)
                    {
                      
                        $link	= $objView->serverUrl($objView->url($info,'product-index-list'));                
                    }
                    else
                    {
                        $info['cat_name_1']=$rw->noSign($info['cat_name_1']);
                        $link	= $objView->serverUrl($objView->url($info,'product-index-list-1'));
                        
                    }
                    
                    
    				$newMenu .= '<li>
    					<a href="'.$link.'">' . $val['name'] . '</a>';								
    				unset($menu[$key]);
    				$this->createMenuHeader($menu,$val['id'],$newMenu,$objView);
    				$newMenu .= '</li>';	
    			}
    		}
            if($ft==0)
            {
                $newMenu .= '</ul>';
            }
	   }
    }
	function createMenuCategory($menu,$parents_id,&$newMenu,$objView,$class='')	
    {
    	if(count($menu)>0)
        {            
            //echo '@'.count($menu).'@<br>'; 
            $rw=new Zendvn_View_Helper_CmsRewriteLink();
            
    		
            $ft=1;
    		foreach($menu as $key => $val)
            {
    			if($val['parents'] == $parents_id )
                {				
                    if($ft==1)
                    {
                        $classC='';
                        if($newMenu=='')
                        {
                            $classC='class="'.$class.'"';    
                        }
                        
                        $newMenu .= '<ul '.$classC.'>';
                        $ft=0;
                    }
                    $info=$val;
                    $info['cat_id']=$info['id'];
                    $info['cat_name']=$rw->noSign($info['name']);
                    if($info['level']==1)
                    {
                      
                        $link	= $objView->serverUrl($objView->url($info,'product-index-list'));                
                    }
                    else
                    {
                        $info['cat_name_1']=$rw->noSign($info['cat_name_1']);
                        $link	= $objView->serverUrl($objView->url($info,'product-index-list-1'));
                        
                    }
                    
                    
    				$newMenu .= '<li>
    					<a href="'.$link.'"><span></span>' . $val['name'] . '</a>';								
    				unset($menu[$key]);
    				$this->createMenuCategory($menu,$val['id'],$newMenu,$objView);
    				$newMenu .= '</li>';	
    			}
    		}
            if($ft==0)
            {
                $newMenu .= '</ul>';
            }
	   }
    }
	
	
	
}