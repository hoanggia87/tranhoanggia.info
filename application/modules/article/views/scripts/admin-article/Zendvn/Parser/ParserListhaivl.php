<?php

class Zendvn_Parser_ParserListhaivl extends Zendvn_Parser_ParserList {
	
	/**
	 * 
	 */
	public function parselist() {
		
		$listDOM = $this->_dom['.photoList .photoListItem'];

		$i= 0;
		//$time = time();
		foreach($listDOM as $item)
		{

			//$time -= $i*60; 
			//$date = date('Y-m-d H:i:s',$time);
			$i++;
			$title = pq($item)->find(".info h2 a")->text();
			if($title)
			{				
				$url = pq($item)->find(".thumbnail a")->attr("href"); 			
				
				$img = pq($item)->find(".thumbnail img")->attr("src");	
				
				$arrItem['title'] = $title;
				$arrItem['description']=pq($item)->find(".blog-container")->html();
				$arrItem['img'] 	= $img;
				$arrItem['url'] 	= $this->relativeURL($url);
				//$arrItem['date'] 	= $date; 

				//nếu có image này thì bài post này là video
				if(pq($item)->find(".blog-container")->html())//blog
					$arrItem['type'] 	= 'blog';
				elseif(pq($item)->find(".thumbnail .videoIndicator")->attr("src"))				
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