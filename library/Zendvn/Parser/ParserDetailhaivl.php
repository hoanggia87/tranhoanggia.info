<?php

class Zendvn_Parser_ParserDetailhaivl extends Zendvn_Parser_ParserDetail {
	
	/**
	 * 
	 */
	public function setDom() {
		
		$this->_html = $this->_dom['.photoDetails'];
		//echo pq($this->_html)->html();
	}

	/**
	 * 
	 */
	public function setTitle() {
		//echo $this->_html;
		$this->_title =  pq($this->_html)->find(".photoInfo h1")->text();
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
			$temp =  pq($this->_html)->find(".photoImg iframe")->attr('src');
			$arrTemp=explode('/', $temp);
		
			$this->_imageUrl = 'https://i3.ytimg.com/vi/'.$arrTemp[count($arrTemp)-1].'/hqdefault.jpg';
		}			
		elseif($this->_type=='image')
			$this->_imageUrl =  pq($this->_html)->find(".photoImg img")->attr('src');

	}

	public function setType()
	{
		//nếu có iframe này thì đó là video
		if(pq($this->_html)->find(".photoImg iframe")->attr('src'))				
			$type 	= 'video';
		else
			$type 	= 'image';

		$this->_type =  $type;
	}
	
	public function setVideo()
	{
		//nếu có image này thì bài post này là video
		
		$this->_video =   pq($this->_html)->find(".photoImg iframe")->attr('src');
		
	
	}

	public function setDescription()
	{
		//$this->_description = pq($this->_html)->find(".blog-container")->html();
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