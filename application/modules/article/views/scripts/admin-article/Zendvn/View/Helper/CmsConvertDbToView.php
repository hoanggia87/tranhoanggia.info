<?php
class Zendvn_View_Helper_CmsConvertDbToView extends Zend_View_Helper_Abstract{
	
	public function cmsConvertDbToView($text)
	{
		  $text	= str_replace('\"','"',$text);
		  $text	= str_replace("\\'","'",$text);
		  $text	= str_replace("\\\\","\\",$text);
		  
		  return $text;
	}
}