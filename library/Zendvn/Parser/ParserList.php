<?php

/**
 * @author Phong
 *
 */
abstract class Zendvn_Parser_ParserList {
	protected $_html;	
	protected $_list;	
	protected $_dom;
	protected $_ppurl;
	public function __construct($html,$ppurl)
	{			
		//echo $body;
		$this->_dom = Zendvn_Dom_Process::newDocumentHTML($html,"utf-8");		
		$this->_list = array();
		$this->_ppurl = trim($ppurl);
		$this->parselist();		
		 
	}
	
	public function getList()
	{
	
		return $this->_list;
		
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
	public abstract function  parselist();
	
}

?>