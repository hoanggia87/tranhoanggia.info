<?php
class Zendvn_View_Helper_MyUrl extends Zend_View_Helper_Abstract
{  
    public function myUrl($toAdd = array(),$url=1)  
    {       	
        $requestUri = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
        $query = parse_url($requestUri, PHP_URL_QUERY);     
        if($query == '' && empty($toAdd))  
        {  
            return $url;  
        }  
        else if(empty($toAdd))  
        {          
            return $url . '/?' . $query;  
        }  
        else  
        {  
            $toAdd = (array)$toAdd; 
             
            $query = explode("&", $query);  
  			foreach($query as $q)
  			{
  				$queryPart 					= explode("=",$q);
  				$arrQuery[$queryPart[0]]	= $queryPart[1]; 
  			}            
            
            $add = '/?';
            if(!empty($arrQuery))
            {
            	
            	$arrQuery = array_merge($arrQuery,$toAdd);	
            }
            else
            {          	
            	$arrQuery = $toAdd;
            }  
  			foreach($arrQuery as $key=>$val)
  			{
  				if($key && $val)
  				{
  					$add .= $key.'='.$val.'&';	
  				}  				
  			}
  			$add = substr($add,0,-1);
  			            
            return $url . $add; 
             
        } 
    }  
}