<?php

class Zendvn_Parser_ParserDetailchatvl extends Zendvn_Parser_ParserDetail {
	
	/**
	 * 
	 */
	public function setDom() {
		
		$this->_html = $this->_dom['#content-holder'];
		//echo pq($this->_html)->html();
	}

	/**
	 * 
	 */
	public function setTitle() {
		//echo $this->_html;
		$this->_title =  pq($this->_html)->find(".post-info-pad h1")->text();
	}

	/**
	 * 
	 */
	public function setPubdate() {
		//$this->_pubdate = pq($this->_html)->find(".bv-date")->text();
	}
	/**
	 * 
	 */
	
	public function setImage()
	{
		//echo 'aaaaaaaaaaaaaaaa'.$this->_type;
		if($this->_type=='video')
		{
			//echo $this->_html;
			$this->_imageUrl =  pq($this->_html)->find(".img-wrap [itemprop='thumbnailUrl']")->attr("href");
		}			
		elseif($this->_type=='image')
			$this->_imageUrl =  pq($this->_html)->find(".post-container .img-wrap img")->attr('src');

	}

	public function setType()
	{
		//nếu có image này thì bài post này là video

		if(pq($this->_html)->find(".blog-container")->html())//blog
			$type 	= 'blog';
		elseif(pq($this->_html)->find(".img-wrap")->attr("itemtype")=='http://schema.org/VideoObject')				
			$type 	= 'video';
		else//http://schema.org/ImageObject
			$type 	= 'image';

		$this->_type =  $type;
	}
	
	public function setVideo()
	{
		//nếu có image này thì bài post này là video
		
		$temp =  pq($this->_html)->find(".img-wrap iframe")->attr("src");
		
		$arrTemp=explode('?', $temp );
		$this->_video=$arrTemp[0];
		
	}

	public function setDescription()
	{
		$this->_description = pq($this->_html)->find(".blog-container")->html();
	}
	
	
	
	/**
	 * 
	 */
	public function setContent() {
		
			
	}

	/**
	 * 
	 */
	public function setAuthor() {
		
	}


}

?>