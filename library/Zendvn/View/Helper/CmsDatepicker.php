<?php
class Zendvn_View_Helper_CmsDatepicker extends Zend_View_Helper_Abstract{
	
	public function cmsDatepicker($name = 'date',$value = '', $att = null, $objView,$options = null){
		if($options == 'birthday'){
	 		$picker		= '<script> $(function() {	$( "#'.$name.'" ).datepicker({changeMonth: true,changeYear: true,yearRange:"c-100:c+5"}); }); </script>';
			$picker		.= $objView->formText($name,$value,$att);
		}
		else
		{
			$picker		= '<script> $(function() {	$( "#'.$name.'" ).datepicker({changeMonth: true,changeYear: true,yearRange:"c-100:c+5"}); }); </script>';
			$picker		.= $objView->formText($name,$value,$att);
		}
		return $picker;
	}
}