<?php

class Zendvn_Parser_ParserListkenh14 extends Zendvn_Parser_ParserList {
	
	/**
	 * 
	 */
	public function parselist() {
		
		$listDOM = $this->_dom['.listnews .item'];
		$i= 0;
		$time = time();
		foreach($listDOM as $item)
		{
			$time -= $i*60; 
			$date = date('Y-m-d H:i:s',$time);
			$i++;
			$title = pq($item)->find("h4.title a")->text();			
			if($title)
			{				
				$url = pq($item)->find("h4.title a")->attr("href"); 			
				$description = pq($item)->find(".sapo")->text();
				$img = pq($item)->find(".img  img")->attr("src");			
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