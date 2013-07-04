<?php
class Zendvn_Paginator{
	
	public function createPaginator($totalItem, $arrPaginator,$options = null){
		
		$adapter = new Zend_Paginator_Adapter_Null($totalItem);
		
		$paginator  = new Zend_Paginator($adapter);
		
		$paginator->setItemCountPerPage($arrPaginator['itemCountPerPage']);		
		$paginator->setPageRange($arrPaginator['pageRange']);
		$paginator->setCurrentPageNumber($arrPaginator['currentPage']);
		
		return $paginator;
	}
}