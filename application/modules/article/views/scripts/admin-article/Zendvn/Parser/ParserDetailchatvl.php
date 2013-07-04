<?php

class Zendvn_Parser_ParserDetailovui extends Zendvn_Parser_ParserDetail {
	
	/**
	 * 
	 */
	public function setDom() {
		$this->_html = $this->_dom['.tin-anh:first'];
	//	echo pq($this->_html)->html();
	}

	/**
	 * 
	 */
	public function setTitle() {
		$this->_title =  pq($this->_html)->find("span.bv-tieude")->text();
	}

	/**
	 * 
	 */
	public function setPubdate() {
		$this->_pubdate = pq($this->_html)->find(".bv-date")->text();
	}
	/**
	 * 
	 */
	
	public function setDescription()
	{
		$this->_description = pq($this->_html)->find(".bv-content")->text();
	}
	
	
	
	/**
	 * 
	 */
	public function setContent() {
		$image_div = "";
		$this->_content = "";
		$content = phpQuery::newDocument(); 
		
		$is_img_set = 0;
		foreach(pq($this->_html)->children("p,center") as $children)
		{
			$image_div = "";
			$tagName = $children->tagName;			
			//echo pq($children)->html();
			if($tagName == 'center')
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
			if($tagName == 'p')
			{	
			
				if(pq($children)->attr("style") == "text-align: center; font-style: italic; color: rgb(0, 0, 255);")
				{
					//caption for image
					pq(".photo:last .caption")->html(pq($children)->text());										
				}
				else
				{
					if(pq($children)->html())
						//$this->_content .="<p>".pq($children)->html()."</p>";
						pq($content)->append("<p>".pq($children)->html()."</p>");	
				}			
				 
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