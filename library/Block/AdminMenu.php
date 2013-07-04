<?php
class Block_AdminMenu extends Zend_View_Helper_Abstract{
	
	public function adminMenu($objView=null,$option=null)
    {
    	
        include('AdminMenu/index.php');
	}
}