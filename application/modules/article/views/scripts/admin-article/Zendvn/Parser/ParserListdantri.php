<?php

class Zendvn_Parser_ParserListdantri extends Zendvn_Parser_ParserList {
	
	/**
	 * 
	 */
	public function parselist() {
		
		$listDOM = $this->_dom['.wid470 .mt3'];
		$time = time();
		$i= 0;
		foreach($listDOM as $item)
		{
			$time -= $i*60; 
			$date = date('Y-m-d H:i:s',$time);
			$i++;
			$title = pq($item)->find(".mr1 h2 a")->text();		
			if($title)
			{				
				$url = pq($item)->find(".mr1 h2 a")->attr("href"); 
				pq($item)->find(".mr1 .wid324 a")->empty();							
				$description = pq($item)->find(".mr1 .wid324")->text() ;				
				$img = pq($item)->find(".mt3 a img")->attr("src");			
				$arrItem['title'] = $title;
				$arrItem['description'] = $description;
				$arrItem['img'] 	= $img;
				$arrItem['url'] 	= $this->relativeURL($url);
				$arrItem['date'] 	= $date; 
				array_push($this->_list,$arrItem);	
			}
			
		}
		
	}
	
	/*public function getList()
	{
		parent::getList();
	}*/

}

?>