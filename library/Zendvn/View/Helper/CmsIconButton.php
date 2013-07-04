<?php
class Zendvn_View_Helper_CmsIconButton extends Zend_View_Helper_Abstract{
	
	public function cmsIconButton($title = '', $class, $link = null,$feature=''){
		
	
			return ' <a href="'.$link.'" target="_blank" title="'.$title.'" class="feature feature_'.$feature.'"><span class="'.$class.'"></span></a> ';

                   
	}
}