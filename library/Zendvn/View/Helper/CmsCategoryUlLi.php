<?php
class Zendvn_View_Helper_CmsCategoryUlLi extends Zend_View_Helper_Abstract{
	
		public function cmsCategoryUlLi($menu,$parents_id,$link='#',&$newMenu,$objView){
				
		

		foreach($menu as $key => $val){
			$id 			= $val['id'];
			//$name 			= $val['name'];
			
			//ve category dang cay
			//$padding = '';
			$lil='<li>';
			$lir='</li>';
			$text='';
			if($val['level'] == 1){
				$newMenu .= '<ul>' . $val['name'];
				
			}else{
				//$padding = 'padding-left: ' . $info['level'] * 30 . 'px';
				
				$newMenu .= $lil . '<a href="'.$link.'/cat_id/'.$val['id'].'">'.$val['name'].'</a>' . $lir;
			}
				//$name = '<div class="div" style="' . $padding . '">' . $name . '</div>';
			$newMenu = $newMenu . '</ul>';			

		}
	
		
	}
	
}