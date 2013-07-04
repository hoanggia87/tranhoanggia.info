<?php

class Zendvn_Parser_ParserDetailkenh14 extends Zendvn_Parser_ParserDetail {
	
	/**
	 * 
	 */
	public function setDom() {
		$this->_html = $this->_dom['.news-detail:first'];
	//	echo pq($this->_html)->html();
	}

	/**
	 * 
	 */
	public function setTitle() {
		$this->_title =  pq($this->_html)->find("h1:first")->text();		
	}

	/**
	 * 
	 */
	public function setPubdate() {
		$this->_pubdate = pq($this->_html)->find("span.date")->text();
	}
	/**
	 * 
	 */
	
	public function setDescription()
	{
		$this->_description = pq($this->_html)->find(".sapo")->text();
	}
	
	
	
	private function traverseDom($dom,&$content,&$is_img_set,$level)
	{
		foreach( pq($dom)->contents() as $children)
		{
			$nodeType =  $children->nodeType;	
			$tagName = $children->tagName;				
			if($nodeType == 3)
			{
				if(rtrim(pq($children)->text()) != "")
				{
					if(pq($content)->find('.photo:last')->length())
					{
						if(pq($content)->find('.photo:last')->nextAll('p') ->length())
						{
							pq($content)->find('p:last')->append(pq($children)->text());
						}
						else
						{
							pq($content)->append("<p>".pq($children)->text()."</p>");	
						}	
					}
					else
					{
						if(pq($content)->find('p')->length())
						{
								pq($content)->find('p:last')->append(pq($children)->text());
						}
						else
						{
							pq($content)->append("<p>".pq($children)->text()."</p>");
						}
						
					}					
					
				}				

			}
			else 
			{
				switch ($tagName)
				{
					case 'a':
						pq($content)->find('p:last')->append(pq($children)->htmlOuter());
						break;
					case 'img':
						$img_src = pq($children)->attr("src");
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
						//pq($content)->find('p:last')->append(pq($children)->htmlOuter());
						break;
						case 'p':
						case 'span':
							/*if(pq($children)->text())
							{
								$attr = pq($children)->attr("style");
								if($attr == 'font-style: italic;')
								{
									$span_content = "<i>".pq($children)->text()."</i>";
								}
								if($attr == 'font-style: bold;')
								{
									$span_content = "<b>".pq($children)->text()."</b>";
								}
								else
								{
									$span_content = pq($children)->text();
								}								
								pq($content)->find('p:last')->append($span_content);
							}
							else
							{
								pq($content)->append($this->traverseDom($children,$content,$is_img_set,$level++));
							}
												
						break;*/
						case 'div':
								pq($content)->append($this->traverseDom($children,$content,$is_img_set,$level++));
							break;
						case 'br':
							break;
							
						default:
							
						break;		
				}
			}
		}
	}
	
	
	/**
	 * 
	 */
	public function setContent() {
		$image_div = "";
		$this->_content = "";
		$content = phpQuery::newDocument(); 
		$level = 0;
		$is_img_set = 0;
		$this->traverseDom($this->_html['.news-detailct'],$content,$is_img_set,$level);		
		$this->_content = $content->htmlOuter();
			
	}

	/**
	 * 
	 */
	public function setAuthor() {
		$this->_author = pq($this->_html)->find(".tacgia")->text();
	}


}

?>