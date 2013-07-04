<?php
class Block_ShowSidebar extends Zend_View_Helper_Abstract{
	
	public function ShowSidebar($objView=null,$option=null)
    {
        if($option == 'noiquy')
        {
            include('ShowSidebar/noiquy.php');
        }
		elseif($option=='fanpage')
			include('ShowSidebar/index.php');
		else
			include('ShowSidebar/index.php');
	}
}