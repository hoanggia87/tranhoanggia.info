<?php
class Block_AdminHeaderpanel extends Zend_View_Helper_Abstract{
	
	public function adminHeaderpanel($objView=null,$option=null)
    {
    	
        include('AdminHeaderpanel/index.php');
	}
}