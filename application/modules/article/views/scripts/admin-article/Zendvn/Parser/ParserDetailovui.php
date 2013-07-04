<?php

class Zendvn_Parser_ParserDetailovui extends Zendvn_Parser_ParserDetail {
	
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
		$this->_imageUrl =  pq($this->_html)->find(".post-container .img-wrap img")->attr('src');
	}

	public function setDescription()
	{
		$this->_description = pq($this->_html)->find(".bv-content")->text();
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