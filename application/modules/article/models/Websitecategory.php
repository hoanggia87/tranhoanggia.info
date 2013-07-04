<?php
class Article_Model_Websitecategory extends Zend_Db_Table{
	protected $_name = 'website_category';
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
			$select = $db->select()
						->from($this->_name.' AS n',array('COUNT(n.id) AS totalItem'))
						->where('n.cat_id = ?',$arrParam['cat_id'],INTEGER);
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
						 ->from($this->_name.' AS n',array('id','name','link'))
						 ->where('n.web_id = ?',$arrParam['web_id'],INTEGER);
		
			//echo $select;
			$result  = $db->fetchAll($select);
		}
		if($options['task'] == 'front-list')
        {
			$select = $db->select()
						 ->from($this->_name.' AS n',array('id','title','summary','full_image','created'))
						 ->where('n.status = 1')
						 ->joinLeft($this->_name.'_category AS nc','nc.id = n.cat_id',array('nc.id as cat_id','nc.name as cat_name'))
						 ->group('n.id')
						 ->order('n.id DESC');
			$result  = $db->fetchAll($select);						
		}
		if($options['task'] == 'front-list-by-cat')
        {
			$select = $db->select()
						 ->from($this->_name.' AS n',array('id','title','summary','full_image','created'))				 
						 ->joinLeft($this->_name.'_category AS nc','nc.id = n.cat_id',array('nc.id as cat_id','nc.name as cat_name'))
						 ->where('n.status = 1')
						 ->where('n.cat_id = ?',$arrParam['cat_id'],INTEGER)
						 ->group('n.id')
						 ->order('n.id DESC');
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
			
			$row->title 		= $arrParam['title'];
			$row->summary 		= $arrParam['summary'];
			$row->full_image 	= $arrParam['full_image'] ; 			
  			//$row->full_image 	= $arrParam['full_image'];  

  			$row->is_hot 			= $arrParam['is_hot'];
  			$row->is_home 			= $arrParam['is_home'];
            $row->is_home_mobile 	= $arrParam['is_home_mobile'];
  			$row->link 			= $arrParam['link'];
  			$row->created 		= date('Y-m-d H:i:s');;
  			$row->created_by 	= 1;
  			$row->modified 		= date('Y-m-d H:i:s');
  			$row->modified_by 	= 1;
  			$row->hit 			= $arrParam['hit'];
  			$row->status 		= $arrParam['status'];
  			$row->cat_id		= $arrParam['cat_id'];
            $row->link		= $arrParam['link'];
  			//$row->order			= $arrParam['order'];
  			
			return $row->save();
			
		}
		
		if($options['task'] == 'admin-edit'){
			$where = 'id = ' . $arrParam['id'];
			$row = $this->fetchRow($where);
		
			$row->title 		= $arrParam['title'];
			$row->summary 		= $arrParam['summary'];
  			$row->full_image 	= $arrParam['full_image'] ; 	
  			$row->is_hot 			= $arrParam['is_hot'];
  			$row->is_home 			= $arrParam['is_home'];
            $row->is_home_mobile 			= $arrParam['is_home_mobile'];
  			$row->link 			= $arrParam['link'];
  			
  			$row->modified 		= date('Y-m-d H:i:s');
  			$row->modified_by 	= 1;
  			
  			$row->status 		= $arrParam['status'];
  			$row->cat_id		= $arrParam['cat_id'];
  			$row->order			= $arrParam['order'];
  			
			$row->link		= $arrParam['link'];
			
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


			$where = 'id = ' . $arrParam['cat_id'];
			try{
				$result = $this->fetchRow($where);
				return $result->toArray(); 
				/*
				echo '<pre>';
				print_r($result->toArray());
				echo '</pre>';
				return;
				if($result)
				{
					$result = $result->toArray();	
				}	*/
			}catch(Exception $e)
			{
				$result = 0;
			}	
			
		}
		
		return $result;
		
	}
	
	public function deleteItem($arrParam = null, $options = null){
		if($options['task'] == 'admin-delete'){
			$where = ' id = ' . $arrParam['id'];			
			//xoa avatar
			$sql=$this->select()
  							->from($this->_name,array('full_image'))
  							->where($where);  							
			$result = $this->fetchAll($sql);
			if($result)
			{
				$result = $result->toArray();
				foreach($result as $key => $image)
  				{
  					$uploadDir = $arrParam['controllerConfig']['imagesDir'];					
					@unlink($uploadDir . 'full_images/' . $image['full_image']);
					@unlink($uploadDir . 'crop_images/' . $image['full_image']);
					@unlink($uploadDir . 'thumb_images_293x145/' . $image['full_image']);	
					@unlink($uploadDir . 'thumb_images_500x500/' . $image['full_image']);												
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
  							->from($this->_name,array('full_image'))
  							->where($where);
				$result = $this->fetchAll($sql);
				if($result)
				{
					$result = $result->toArray();
					foreach($result as $key => $image)
	  				{
	  					$uploadDir = $arrParam['controllerConfig']['imagesDir'];					
						@unlink($uploadDir . 'full_images/' . $image['full_image']);
						@unlink($uploadDir . 'crop_images/' . $image['full_image']);
						@unlink($uploadDir . 'thumb_images_293x145/' . $image['full_image']);	
						@unlink($uploadDir . 'thumb_images_500x500/' . $image['full_image']);						
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


	public function changeIsHome($arrParam = null, $options = null){
		$cid = $arrParam['cid'];
		
		if(count($cid)>0){
			if($arrParam['type'] == 1){
				$status = 1;
			}else{
				$status = 0;
			}
			
			$ids = implode(',',$cid);
			$data = array('is_home'=>$status);
			$where = 'id IN (' . $ids . ')';
			$this->update($data,$where);
		}
		if($arrParam['id']>0){
			if($arrParam['type'] == 1){
				$status = 1;
			}else{
				$status = 0;
			}
			
			$data = array('is_home'=>$status);
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