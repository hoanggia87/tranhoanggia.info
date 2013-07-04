<?php
class Block_ShowMenu extends Zend_View_Helper_Abstract{
	
	public function ShowMenu($objView=null,$option=null)
    {
    	if($option=='mobile')
        	include('ShowMenu/index.mobile.php');
        else
        	include('ShowMenu/index.php');
	}
}