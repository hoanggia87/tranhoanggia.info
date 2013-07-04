<?php

class Zendvn_Parser_ParserListovui extends Zendvn_Parser_ParserList {
	
	/**
	 * 
	 */
	public function parselist() {
		
		$listDOM = $this->_dom['#entries-content-ul .gag-link'];

		$i= 0;
		//$time = time();
		foreach($listDOM as $item)
		{

			//$time -= $i*60; 
			//$date = date('Y-m-d H:i:s',$time);
			$i++;
			$title = pq($item)->find("a.jump_focus")->text();
			if($title)
			{				
				$url = pq($item)->find("a.jump_focus")->attr("href"); 			
				
				$img = pq($item)->find(".img-wrap img")->attr("src");	
				
				$arrItem['title'] = $title;
				$arrItem['description']=pq($item)->find(".blog-container")->html();
				$arrItem['img'] 	= $img;
				$arrItem['url'] 	= $this->relativeURL($url);
				//$arrItem['date'] 	= $date; 

				//nếu có image này thì bài post này là video
				if(pq($item)->find(".blog-container")->html())//blog
					$arrItem['type'] 	= 'blog';
				elseif(pq($item)->find("img.videoPlay.ovui")->attr("src"))				
					$arrItem['type'] 	= 'video';
				else
					$arrItem['type'] 	= 'image';

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