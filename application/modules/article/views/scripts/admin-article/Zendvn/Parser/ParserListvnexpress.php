<?php

class Zendvn_Parser_ParserListvnexpress extends Zendvn_Parser_ParserList {
	
	/**
	 * 
	 */
	public function parselist() {
		
		$listDOM = $this->_dom['.content-center .folder-news'];
		$i= 0;
		$time = time();
		foreach($listDOM as $item)
		{			
			$time -= $i*60; 
			$date = date('Y-m-d H:i:s',$time);
			$i++;

			$title = pq($item)->find(".right-fnews h2.h2Title-14 a")->text();		
			if($title)
			{				
				$url = pq($item)->find(".right-fnews h2.h2Title-14 a")->attr("href"); 			
				$description = pq($item)->find(".right-fnews h3.h3Lead ")->text();
				$img = pq($item)->find(".left-fnews img")->attr("src");			
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