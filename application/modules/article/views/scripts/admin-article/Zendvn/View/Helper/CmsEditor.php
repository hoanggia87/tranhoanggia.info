<?php
require_once (PUBLIC_PATH . '/scripts/fckeditor/fckeditor.php');
class Zendvn_View_Helper_CmsEditor extends Zend_View_Helper_Abstract{
	
	public function cmsEditor($name = 'content',$value = '',$width = '100%',$height = 500,$option=null){
		if($option==null){
			$oFCKeditor = new FCKeditor($name);
			$oFCKeditor->BasePath = PUBLIC_URL . '/scripts/fckeditor/' ;
			$oFCKeditor->Width = $width;
			$oFCKeditor->Height = $height;
			$oFCKeditor->Value = $value ;
		}
		if($option=='basic'){
			$oFCKeditor = new FCKeditor($name);
			$oFCKeditor->BasePath = PUBLIC_URL . '/scripts/fckeditor/' ;
			$oFCKeditor->Width = $width;
			$oFCKeditor->Height = $height;
			$oFCKeditor->ToolbarSet="Basic";
			$oFCKeditor->Value = $value ;
		}
		return $oFCKeditor->Create();
	}
}