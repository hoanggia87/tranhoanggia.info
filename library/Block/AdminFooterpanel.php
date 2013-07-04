<?php
class Block_AdminFooterpanel extends Zend_View_Helper_Abstract{
	
	public function adminFooterpanel($objView=null,$option=null)
    {
    	
        include('AdminFooterpanel/index.php');
	}
}