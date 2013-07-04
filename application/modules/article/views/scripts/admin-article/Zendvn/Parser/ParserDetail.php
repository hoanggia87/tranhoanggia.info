<?php

abstract class  Zendvn_Parser_ParserDetail {
	protected $_html;
	protected $_content;
	protected $_title;
	protected $_description;
	protected $_pubdate;
	protected $_author;
	protected $_dom;
	protected $_article;
	protected $_ppurl;
	protected $_imageUrl = "";
	protected $_imgHeight;
	protected $_imgWidth;
	function __construct($html,$ppurl) {
		$this->_ppurl = trim($ppurl);				
		$this->_dom = Zendvn_Dom_Process::newDocumentHTML($html,"utf-8");	
		$this->setDom();
		$this->setTitle();
		$this->setAuthor();
		$this->setImage();
		$this->setPubdate();
		$this->setDescription();
		$this->setContent();		
		$this->setArticle();
		
		
	}
	public abstract  function setDom();
	public abstract  function setTitle();
	public abstract  function setPubdate();
	public abstract  function setContent();
	public abstract  function setDescription();
	public abstract  function setAuthor();
	
	public function setArticle()
	{	
		$this->_article['title'] 	= $this->_title;
		$this->_article['description'] 	= $this->_description;
		$this->_article['content'] 	= $this->_content;
		$this->_article['pubdate'] 	= $this->_pubdate;
		$this->_article['author'] 	= $this->_author;
		$this->_article['image'] 	= $this->_imageUrl;
		$this->_article['imgHeight'] 	= $this->_imgHeight;
		$this->_article['imgWidth'] 	= $this->_imgWidth;
	}
	
	public function getArticle()
	{		
		return $this->_article;
	}
	
	public function setImage($imgSrc,$height = 0,$width = 0)
	{
		$this->_imageUrl 	= $imgSrc;
		$this->_imgHeight 	= $height;
		$this->_imgWidth 	= $width;
	}
	protected function relativeURL($url)
	{
		$url = trim($url);
		if(!strpos($url,"://"))
		{
			
			if(substr($url,0,1) == '/')
			{				
				$url = $this->_ppurl.$url;				
			}
			else
			{
				$url = $this->_ppurl."/".$url;
			}
		}	
		
		return $url;
	}
}

?>