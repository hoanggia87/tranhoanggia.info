<?php

class Stats_Model_Stats {
	
	public function updateStats($arrParam)
	{
		$article_id = $arrParam['article_id'];
		$user_id	= $arrParam['user_id'];
		$like_count = $arrParam['like_count'];
		$comment_count	= $arrParam['comment_count'];
		$db =  Zend_Registry::get('connectDb'); 		
		//$strSQL = "INSERT INTO `stats`(article_id,user_id,like_count,comment_count) values('$article_id','$user_id','$like_count','$comment_count') ON DUPLICATE KEY UPDATE like_count='$like_count',comment_count='$comment_count'";
		$strSQL = "UPDATE article_detail SET like_count='$like_count',comment_count='$comment_count' WHERE id='$article_id'";
		$db->query($strSQL);
		
	}
	public function updateView($id)
	{
	
		$db =  Zend_Registry::get('connectDb'); 	
		if(is_array($id))
		{
			
			
			$listID=implode(',', $id);

			//$strSQL = "INSERT INTO `stats`(article_id,view_count)  values$arrVar  ON DUPLICATE KEY UPDATE view_count=view_count+1";

		}
		else
		{
			
			$listID=$id;
			//$strSQL = "INSERT INTO `stats`(article_id,view_count) values($id,'0') ON DUPLICATE KEY UPDATE view_count=view_count+1";
		}
		//echo $strSQL;
		$strSQL = "UPDATE `article_detail` SET view_count=view_count+1 WHERE id IN ($listID)";
		$db->query($strSQL);
		
	}
	
	public function getnTop($arrParam,$options = null)
	{
		$db =  Zend_Registry::get('connectDb');
		$select  = $db->select()
					->from(`stats`);
		if($options['order'] == 'like_count')
		{
			$select->order('like_count DESC');	
		}else if($options['order'] == 'comment_count')
		{
			$select->order('comment_count DESC');	
		}
		else
		{
			$select->order('comment_count DESC')
					->	order('like_count DESC');
		}
					
		
		$select->limitPage(0,$arrParam['limit']);

		return $result  = $db->fetchAll($select);
	}
	
}

?>