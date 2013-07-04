<?php
class Zendvn_View_Helper_CmsIconButton extends Zend_View_Helper_Abstract{
	
	public function cmsIconButton($title = '', $imgLink, $link = null){
		
		if($link == null){
			$xhtml = '<img src="' . $imgLink . '" title="' . $title . '">';
		}else{
			$xhtml = ' <a href="' . $link . '">
	                      <img src="' . $imgLink  . '" title="' . $title .'" > 
	                   </a>';
		}
		return $xhtml;
                   
	}
}