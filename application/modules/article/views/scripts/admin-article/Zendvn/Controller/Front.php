<?php
class Zendvn_Controller_Front{
	
	public function loadModule(){
		$front = Zend_Controller_Front::getInstance();
		echo '<pre>';
		print_r($front);
		echo '</pre>';
	}
}
