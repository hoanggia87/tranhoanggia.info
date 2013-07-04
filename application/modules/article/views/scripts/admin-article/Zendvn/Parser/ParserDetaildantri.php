<?php

class Zendvn_Parser_ParserDetaildantri extends Zendvn_Parser_ParserDetail {
	
	/**
	 * 
	 */
	public function setDom() {
		$this->_html = $this->_dom['#content .content'];	
	}

	/**
	 * 
	 */
	public function setTitle() {
		$this->_title =  pq($this->_html)->find("font:first")->text();
	}

	/**
	 * 
	 */
	public function setPubdate() {
		$this->_pubdate = pq($this->_html)->find("meta")->attr("content");
	}
	/**
	 * 
	 */
	
	public function setDescription()
	{
		$this->_description = pq($this->_html)->find("span:first span")->text();
	}
	
	
	
	/**
	 * 
	 */
	public function setContent() {
		$image_div = "";
		$this->_content = "";
		$content = phpQuery::newDocument(); 
		
		$is_img_set = 0;
		foreach(pq($this->_html['.news_details'])->children("p,div") as $children)
		{
			$is_img = 0;
			
			$image_div = "";
			$tagName = $children->tagName;
						
			if(pq($children)->find("img")->length())
			{
				$img_src = pq($children)->find("img")->attr("src");
				if($img_src)
				{
					$image_div = '<div class="photo img">';
					 $img_src = $this->relativeURL($img_src);					 
					 list($width, $height, $type, $attr) =  getimagesize($img_src);
					 if(!$is_img_set)
					 {
					 	$this->setImage($img_src,$height,$width);
					 	$is_img_set = 1;
					 }
					 	
					$image_div .= '<img width="'.$width.'" height="'.$height.'" src="'.$img_src.'">';
					//<div class="caption">Sau lễ khai mạc là trận thi đấu bóng rổ giữa ĐH Kiến Trúc và ĐH Xây Dựng</div>
					$image_div.='<div class="caption"></div>';
					$image_div.= "</div>";	
					//$this->_content .= $image_div;
					pq($content)->append($image_div);
				}
			}
			else if(pq($children)->text())
			{
				pq($content)->append("<p>".pq($children)->html()."</p>");
			}
						
		}
		
		$this->_content = $content->htmlOuter();
			
	}

	/**
	 * 
	 */
	public function setAuthor() {
		
	}


}

?>