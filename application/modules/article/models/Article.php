<?php
class Article_Model_Article extends Zend_Db_Table{
	protected $_name = 'article_detail';
	protected $_primary = 'id';

	public function countItem($arrParam = null, $options = null){
		$db = Zend_Registry::get('connectDb');
		//$db = Zend_Db::factory($adapter,$config);
		if($options == null){
			$ssFilter = $arrParam['ssFilter'];
			
			$select = $db->select()
						->from($this->_name.' AS n',array('COUNT(n.id) AS totalItem'));
			
			if(!empty($ssFilter['keywords'])){
					$keywords = '%' . $ssFilter['keywords'] . '%';
					$select->where('n.title LIKE ?',$keywords,STRING);
			}
			
			if($ssFilter['cat_id']>0){
				$select->where('n.cat_id = ?',$ssFilter['cat_id'],INTEGER);
			}
			//echo $select;
			$result = $db->fetchOne($select);
		}
		if($options['task'] == 'list-by-cat'){
			$numOfRecord=6;
			$select = $db->select()
						->from($this->_name.' AS n',array('COUNT(n.id) AS totalItem'))
						->where('n.cat_id = ?',$arrParam['cat_id'],INTEGER)
						->where('n.status = 1');
						//order theo 
        	
			switch ($arrParam['order']) {
				case 'new':					
					break;
				case 'hot':
					$select->where('n.is_hot = 1');					
					break;				
				default:
					
					break;
			}
			$result = $db->fetchOne($select);
		}
		
		
		return $result;
		
	}
	
	public function sortItem($arrParam = null, $options = null){
			
		$cid = $arrParam['cid'];
		$order = $arrParam['order'];
		if(count($cid)>0){
			foreach ($cid as $key => $val) {
				$where = ' id = ' . $val;				
				$data = array('order'=>$order[$val]);
				$this->update($data,$where);
			}
		}
	}
	
	public function listItem($arrParam = null, $options = null){
		$db = Zend_Registry::get('connectDb');
		//$db = Zend_Db::factory($adapter,$config);
		
		$paginator = $arrParam['paginator'];
		$ssFilter = $arrParam['ssFilter'];
		
		if($options['task'] == 'admin-list'){
			$select = $db->select()
						 ->from($this->_name.' AS n',array('id','page_id','is_adult','is_hot','link','status','DATE_FORMAT(`create_date`,\' %H:%i:%s %d/%m/%Y\') as create_date','user_id','title','image','type'))
						 ->joinLeft('website AS nc','nc.id = n.page_id','nc.name as site_name')
						 ->order('n.id DESC');
						 ;
		
			if(!empty($ssFilter['col']) && !empty($ssFilter['order'])){
				$select->order($ssFilter['col'] . ' ' . $ssFilter['order']);
			
			}
			//echo 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa: '.$paginator['itemCountPerPage'];
			if($paginator['itemCountPerPage']>0){
				$page = $paginator['currentPage'];
				$rowCount = $paginator['itemCountPerPage'];
				$select->limitPage($page,$rowCount);
			}
			
			if(!empty($ssFilter['keywords'])){
				$keywords = '%' . $ssFilter['keywords'] . '%';
				$select->where('n.title LIKE ?',$keywords,STRING);
			}
			if($ssFilter['page_id']>0){
				$select->where('n.page_id = ?',$ssFilter['page_id'],INTEGER);				
			}
           
			if($ssFilter['date_from']!='' && $ssFilter['date_to']!=''){
					$select->where("n.created >= '".$ssFilter['date_from']."'");
					$select->where("n.created <= '".$ssFilter['date_to']."'");
			}
            
			//echo $select;
			$result  = $db->fetchAll($select);
		}
		if($options['task']=='get-list-url-exist')
		{
			
			$listUrl='\''.implode('\',\'', $arrParam).'\'';

			if($listUrl)
			{
				
				$select = $db->select()
							 ->from($this->_name.' AS n',array('id','link'))
							 ->where('n.link IN ('.$listUrl.')');
				$resultTemp  = $db->fetchAll($select);	
				

				$result=array();
				foreach ($resultTemp as $key => $value) {
					$result[]=$value['link'];
				}
			}
			
		}
		if($options['task'] == 'front-list')
        {
			$select = $db->select()
						 ->from($this->_name.' AS n',array('id','title','image','description','type','user_id','create_date'))
						 ->where('n.status = 1')
						 ->joinLeft($this->_name.'_category AS nc','nc.id = n.cat_id',array('nc.id as cat_id','nc.name as cat_name'))
						 ->group('n.id')
						 ->order('n.id DESC');
			$result  = $db->fetchAll($select);						
		}
		if($options['task'] == 'front-list-order')
        {
        	$numOfRecord=6;
			$select = $db->select()
						 ->from($this->_name.' AS n',array('id','title','image','type','user_id','create_date'))
						 ->where('n.`type` = \'video\' OR n.`type` = \'image\' ')
						 ->where('n.status = 1')
						 ->where('n.id <> ?',$arrParam['id'],INTEGER)
						 ->order('rand()')
						 ->limit($numOfRecord);

			$where = $db->quoteInto('match(n.title) against(?)',$arrParam['k']);     
    
            $col1 = $db->quoteIdentifier('n.title');
            $where1 = $db->quoteInto("$col1 LIKE ?", '%'.$arrParam['k'].'%');            
            
            $select->where($where .' OR '. $where1 );
			$result  = $db->fetchAll($select);			


			//nếu chưa đủ 6 record thì mình sẽ lấy random các bài viết để bỏ vào cho đủ
			$numRecord=count($result);
			if($numRecord<$numOfRecord)
			{
				//echo 'aaaaaaaaaaa|'.$numOfRecord.'|'.$numRecord.'|';
				$limit=$numOfRecord-$numRecord;
				//lấy id ra để loại trừ
				$arrListID=array();
				foreach ($result as $key => $value) {
					$arrListID[]=$value['id'];
				}
				$listID='\''.implode('\',\'', $arrListID).'\'';

				$select = $db->select()
				 ->from($this->_name.' AS n',array('id','title','image','type','user_id','create_date'))
				 ->where('n.`type`=\'video\' OR n.`type`=\'image\'')
				 ->where('n.id NOT IN ('.$listID.')')
				 ->order('rand()')//sắp xếp ngẫu nhiên
				 ->limit($limit);
				 $arrRecordPlus  = $db->fetchAll($select);		
				foreach ($arrRecordPlus as $key => $value) 
				{
					$result[]=$value;
				}
			}


		}		
		if($options['task'] == 'front-list-by-cat')
        {
        	if(!$arrParam['cat_id'])
        		$arrParam['cat_id']=1;


			$select = $db->select()
						 ->from($this->_name.' AS n',array('id','title','image','description','type','user_id','DATE_FORMAT(`create_date`,\' %H:%i:%s %d/%m/%Y\') as create_date'))				 
						 //->joinLeft('article_category AS nc','nc.id = n.cat_id',array('nc.id as cat_id','nc.name as cat_name'))
						 ->where('n.status = 1')
						 ->where('n.cat_id = ?',$arrParam['cat_id'],INTEGER)
						 ->group('n.id')
						 ->order('n.id DESC');
						 

			//order theo 
        	
			switch ($arrParam['order']) {
				case 'new':
					
					break;
				case 'hot':
					$select->where('n.is_hot = 1');					
					break;				
				default:
					
					break;
			}

			if($paginator['itemCountPerPage']>0){
				$page = $paginator['currentPage'];
				$rowCount = $paginator['itemCountPerPage'];
				$select->limitPage($page,$rowCount);
			}
			
			$result  = $db->fetchAll($select);						
		}
		if($options['task'] == 'front-hotstie')
        {
			$select = $db->select()
						 ->from($this->_name.' AS n',array('id','title','summary','full_image','created','link'))
						 ->where('n.status = 1')
                         ->where('n.is_hot = 1')
                         ->order('n.order DESC')
						 ->order('n.id DESC');
			$result  = $db->fetchAll($select);						
		}
        if($options['task'] == 'front-homesite')
        {
			$select = $db->select()
						 ->from($this->_name.' AS n',array('id','title','summary','full_image','created','link','cat_id'))
						 ->where('n.status = 1')
                         ->where('n.is_home = 1')
                         ->order('n.order DESC')
						 ->order('n.id DESC');
			$result  = $db->fetchAll($select);						
		}
        if($options['task'] == 'front-homesitemobile')
        {
			$select = $db->select()
						 ->from($this->_name.' AS n',array('id','title','summary','full_image','created','link','cat_id'))
						 ->where('n.status = 1')
                         ->where('n.is_home_mobile = 1')
                         ->order('n.order DESC')
						 ->order('n.id DESC');
			$result  = $db->fetchAll($select);						
		}
        if($options['task'] == 'front-list-sub')
        {
			$select = $db->select()
						 ->from($this->_name.' AS n',array('id','title','summary','full_image','created','link','cat_id'))
						 ->where('n.status = 1')
                         ->where('cat_id IN ('.$arrParam['list-sub'].')')
                         ->order('n.order DESC')
						 ->order('n.id DESC');
			$result  = $db->fetchAll($select);						
		}
		return $result;
	
	}
	
	public function saveItem($arrParam = null, $options = null){
		
		if($options['task'] == 'admin-add'){
			$row =  $this->fetchNew();
			
			$row->page_id 		= $arrParam['page_id'];
			
			$row->status 	= $arrParam['status'] ; 			
  			
  			$row->link 			= $arrParam['link'];
  			$row->create_date 		= date('Y-m-d H:i:s');

  			$auth = Zend_Auth::getInstance();
  			$infoAuth = $auth->getIdentity();

  			$row->user_id 	= $infoAuth->user_id;
  			$row->title 		= $arrParam['title'];
  			
  			$row->description 			= $arrParam['description'];
  			$row->image 		= $arrParam['image'];
  			$row->video		= $arrParam['video'];
            $row->type		= $arrParam['type'];
  			
  			$row->is_hot		= $arrParam['is_hot'];
            $row->is_adult		= $arrParam['is_adult'];
  			
  			$row->cat_id		= $arrParam['cat_id'];
			return $row->save();
			
		}
		
		if($options['task'] == 'admin-edit'){
			$where = 'id = ' . $arrParam['id'];
			$row = $this->fetchRow($where);
		
			$row->page_id 		= $arrParam['page_id'];
			
			$row->status 	= $arrParam['status'] ; 			
  			
  			$row->link 			= $arrParam['link'];
  			$row->create_date 		= date('Y-m-d H:i:s');
  			$auth = Zend_Auth::getInstance();
  			$infoAuth = $auth->getIdentity();

  			$row->user_id 	= $infoAuth->user_id;
  			$row->title 		= $arrParam['title'];
  			
  			$row->description 			= $arrParam['description'];
  			$row->image 		= $arrParam['image'];
  			$row->video		= $arrParam['video'];
            $row->type		= $arrParam['type'];
  			
  			$row->is_hot		= $arrParam['is_hot'];
            $row->is_adult		= $arrParam['is_adult'];
			$row->cat_id		= $arrParam['cat_id'];
			$row->save();
		}
		
	}

	

	public function updateClick($arrParam)
    {
        $db = Zend_Registry::get('connectDb');
        $db->query('UPDATE '.$this->_name.' SET hit=hit+1 WHERE id=\''.$arrParam['id'].'\'');
        return;
    }
	public function getItem($arrParam = null, $options = null){
		
		if($options['task'] == 'admin-info' || $options['task'] == 'admin-edit'){
			$where = 'id = ' . $arrParam['id'];
			try{
				$result = $this->fetchRow($where);
				if($result)
				{
					$result = $result->toArray();	
				}	
			}catch(Exception $e)
			{
				$result = 0;
			}	
			
		}
				
		if($options['task'] == 'admin-edit'){
			$db = Zend_Registry::get('connectDb');
			//$db = Zend_Db::factory($adapter,$config);
			
			$sql = $db->select()
						->from($this->_name.' as n','*')
						->joinLeft('weblink_category as nc','n.cat_id=nc.id',array('nc.name as cat_name'))
                        
						->joinLeft('users as u','n.created_by=u.id',array('u.user_name as created_name'))
						->where('n.id = ?', $arrParam['id'],INTEGER);
			$result = $db->fetchRow($sql);
		}
		if($options['task'] == 'front-detail'){
			$db = Zend_Registry::get('connectDb');
			//$db = Zend_Db::factory($adapter,$config);
			
			$sql1 = $db->select()
						->from($this->_name.' as n','*')
						//->joinLeft('website_category as nc','n.cat_id=nc.id',array('nc.name as cat_name'))            
						
						->where('n.id = ?', $arrParam['id'],INTEGER);
			$sql2 = $db->select()
						->from($this->_name.' as n1','*')
						
						->where('n1.id < ?', $arrParam['id'],INTEGER)
						->order('n1.id DESC')
						->limit(1,0);
			$sql3 = $db->select()
						->from($this->_name.' as n2','*')
						
						->where('n2.id > ?', $arrParam['id'],INTEGER)
						->order('n2.id ASC')
						->limit(1,0);
			$sql=$db->select()->union(array('('.$sql1.')', '('.$sql2.')', '('.$sql3.')'), Zend_Db_Select::SQL_UNION_ALL );

			$resultTemp = $db->fetchAll($sql);

			$result=array();
			foreach ($resultTemp as $key => $value) {
				$result[$value['id']]=$value;
			}
			
		}
		return $result;
	}
	
	public function deleteItem($arrParam = null, $options = null){
		if($options['task'] == 'admin-delete'){
			$where = ' id = ' . $arrParam['id'];			
			//xoa avatar
			$sql=$this->select()
  							->from($this->_name,array('type','image'))
  							->where($where);  							
			$result = $this->fetchAll($sql);
			if($result)
			{
				$result = $result->toArray();
				foreach($result as $key => $image)
  				{
  					if($imag['type']=='image')
  					{
  						$uploadDir = $arrParam['controllerConfig']['imagesDir'];					
						@unlink($uploadDir . 'images/' . $image['image']);
  					}
  					
															
  				}
				//xoa record
				$this->delete($where);	
			}
			
		}
		
		if($options['task'] == 'admin-multi-delete'){
			$cid = $arrParam['cid'];
			
			if(count($cid)>0){
				if($arrParam['type'] == 1){
					$status = 1;
				}else{
					$status = 0;
				}
				
				echo '<br>' . $ids = implode(',',$cid);
				echo '<br>' . $where = 'id IN (' . $ids . ')';
				//xoa avatar
				$sql=$this->select()
  							->from($this->_name,array('type','image'))
  							->where($where);
				$result = $this->fetchAll($sql);
				if($result)
				{
					$result = $result->toArray();
					foreach($result as $key => $image)
	  				{
	  					if($imag['type']=='image')
	  					{
	  						$uploadDir = $arrParam['controllerConfig']['imagesDir'];					
							@unlink($uploadDir . 'images/' . $image['image']);
	  					}
	  					
	  				}
					//xoa record
					$this->delete($where);
				}	
			}
		}
	}

	public function changeStatus($arrParam = null, $options = null){
		$cid = $arrParam['cid'];
		
		if(count($cid)>0){
			if($arrParam['type'] == 1){
				$status = 1;
			}else{
				$status = 0;
			}
			
			$ids = implode(',',$cid);
			$data = array('status'=>$status);
			$where = 'id IN (' . $ids . ')';
			$this->update($data,$where);
		}
		if($arrParam['id']>0){
			if($arrParam['type'] == 1){
				$status = 1;
			}else{
				$status = 0;
			}
			
			$data = array('status'=>$status);
			$where = 'id = ' . $arrParam['id'];
			$this->update($data,$where);
		}
		
	}


	public function changeIsAdult($arrParam = null, $options = null){
		$cid = $arrParam['cid'];
		
		if(count($cid)>0){
			if($arrParam['type'] == 1){
				$status = 1;
			}else{
				$status = 0;
			}
			
			$ids = implode(',',$cid);
			$data = array('is_adult'=>$status);
			$where = 'id IN (' . $ids . ')';
			$this->update($data,$where);
		}
		if($arrParam['id']>0){
			if($arrParam['type'] == 1){
				$status = 1;
			}else{
				$status = 0;
			}
			
			$data = array('is_adult'=>$status);
			$where = 'id = ' . $arrParam['id'];
			$this->update($data,$where);
		}
		
	}

    public function changeIsHomeMobile($arrParam = null, $options = null){
		$cid = $arrParam['cid'];
		
		if(count($cid)>0){
			if($arrParam['type'] == 1){
				$status = 1;
			}else{
				$status = 0;
			}
			
			$ids = implode(',',$cid);
			$data = array('is_home_mobile'=>$status);
			$where = 'id IN (' . $ids . ')';
			$this->update($data,$where);
		}
		if($arrParam['id']>0){
			if($arrParam['type'] == 1){
				$status = 1;
			}else{
				$status = 0;
			}
			
			$data = array('is_home_mobile'=>$status);
			$where = 'id = ' . $arrParam['id'];
			$this->update($data,$where);
		}
		
	}
    
	public function changeIsHot($arrParam = null, $options = null){
		$cid = $arrParam['cid'];
		
		if(count($cid)>0){
			if($arrParam['type'] == 1){
				$status = 1;
			}else{
				$status = 0;
			}
			
			$ids = implode(',',$cid);
			$data = array('is_hot'=>$status);
			$where = 'id IN (' . $ids . ')';
			$this->update($data,$where);
		}
		if($arrParam['id']>0){
			if($arrParam['type'] == 1){
				$status = 1;
			}else{
				$status = 0;
			}
			
			$data = array('is_hot'=>$status);
			$where = 'id = ' . $arrParam['id'];
			$this->update($data,$where);
		}
		
	}

}