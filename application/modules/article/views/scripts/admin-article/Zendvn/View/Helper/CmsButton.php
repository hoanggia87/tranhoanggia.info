<?php
class Zendvn_View_Helper_CmsButton extends Zend_View_Helper_Abstract{
	
	public function cmsButton($name, $link = '#',$imgLink, $type = 'link'){
		
		if($type == 'link'){
			$aTag = 'href="' . $link . '" ';
		}else{
			$aTag = 'href="#" onclick="OnSubmitForm(\'' . $link . '\')"';
		}
		
		$xhtml = '<div class="toolbar-button" >
                        <a ' . $aTag . '>
        				<img src="' . $imgLink . '">  
                        <br>
                       ' . $name . '
                        </a>
                    </div>';
		return $xhtml;
	}
}