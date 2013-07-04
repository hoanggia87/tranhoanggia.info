<?php

class Zendvn_Parser_ParserList24h extends Zendvn_Parser_ParserList {
	
	/**
	 * 
	 */
	public function parselist() {
		
		$listDOM = $this->_dom['.boxDoi-sub-c .boxDonItem'];
		$i= 0;
		$time = time();
		foreach($listDOM as $item)
		{
			$time -= $i*60; 
			$date = date('Y-m-d H:i:s',$time);
			$i++;
			$title = pq($item)->find(".div_title_news a")->text();
			if($title)
			{				
				$url = pq($item)->find(".div_title_news a")->attr("href"); 			
				$description = pq($item)->find(".div_brief_news")->text();
				$img = pq($item)->find(".boxDoiItem-img img")->attr("src");			
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