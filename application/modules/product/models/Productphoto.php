<?php
class Product_Model_Productphoto extends Zend_Db_Table{
	protected $_name = 'product_photo';
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
						 ->from($this->_name.' AS n',array('id','title','status','order','created','full_image'))
						 ->joinLeft($this->_name.'_category AS nc','nc.id = n.cat_id','nc.name as cat_name')
						 ->group('n.id');
		
			if(!empty($ssFilter['col']) && !empty($ssFilter['order'])){
				$select->order($ssFilter['col'] . ' ' . $ssFilter['order']);
			
			}
			if($paginator['itemCountPerPage']>0){
				$page = $paginator['currentPage'];
				$rowCount = $paginator['itemCountPerPage'];
				$select->limitPage($page,$rowCount);
			}
			
			if(!empty($ssFilter['keywords'])){
				$keywords = '%' . $ssFilter['keywords'] . '%';
				$select->where('n.title LIKE ?',$keywords,STRING);
			}
			if($ssFilter['cat_id']>0){
				$select->where('n.cat_id = ?',$ssFilter['cat_id'],INTEGER);
				
			}
			if($ssFilter['date_from']!='' && $ssFilter['date_to']!=''){
					$select->where("n.created >= '".$ssFilter['date_from']."'");
					$select->where("n.created <= '".$ssFilter['date_to']."'");
			}
			//echo $select;
			$result  = $db->fetchAll($select);
		}
		elseif($options['task'] == 'front-list'){
			$select = $db->select()
						 ->from($this->_name.' AS n',array('id','title','summary','full_image','created'))
						 ->where('n.status = 1')
						 ->joinLeft($this->_name.'_category AS nc','nc.id = n.cat_id',array('nc.id as cat_id','nc.name as cat_name'))
						 ->group('n.id')
						 ->order('n.id DESC');
			$result  = $db->fetchAll($select);						
		}
		elseif($options['task'] == 'front-list-by-cat'){
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
        elseif($options['task'] == 'list-by-proid'){
			$select = $db->select()
						 ->from($this->_name,array('id','full_image'))
						 ->where('pro_id = ?',$arrParam['id'],INTEGER)
                         ->where('type = ?',$options['type'],INTEGER)
						 ->order('id DESC');
			
			$result  = $db->fetchAll($select);						
		}
        
		return $result;
	
	}
	
	public function saveItem($arrParam = null, $options = null){
		
		if($options['task'] == 'admin-add'){
			$row =  $this->fetchNew();
			
			$row->title 		= $arrParam['title'];
			$row->summary 		= $arrParam['summary'];
  			$row->full_image 	= $arrParam['full_image'];  			
  			$row->hot 			= $arrParam['hot'];
  			$row->created 		= date('Y-m-d H:i:s');;
  			$row->created_by 	= 1;
  			$row->modified 		= date('Y-m-d H:i:s');
  			$row->modified_by 	= 1;
  			$row->hit 			= $arrParam['hit'];
  			$row->status 		= $arrParam['status'];
  			$row->cat_id		= $arrParam['cat_id'];
            $row->pro_id		= $arrParam['pro_id'];
            $row->type		= $arrParam['type'];
  			//$row->order			= $arrParam['order'];
  			
			$row->save();
		}
		
		if($options['task'] == 'admin-edit'){
			$where = 'id = ' . $arrParam['id'];
			$row = $this->fetchRow($where);
		
			$row->title 		= $arrParam['title'];
			$row->summary 		= $arrParam['summary'];
  						
  			$row->hot 			= $arrParam['hot'];
  			
  			$row->modified 		= date('Y-m-d H:i:s');
  			$row->modified_by 	= 1;
  			
  			$row->status 		= $arrParam['status'];
  			$row->cat_id		= $arrParam['cat_id'];
  			$row->order			= $arrParam['order'];
  			$row->comment		= $arrParam['comment'];
			$row->pro_id		= $arrParam['pro_id'];
			$row->type		= $arrParam['type'];
			$row->save();
		}
		
	}
	
	public function getItem($arrParam = null, $options = null){
		
		if($options['task'] == 'admin-info' || $options['task'] == 'admin-edit'){
			$where = 'id = ' . $arrParam['id'];
			$result = $this->fetchRow($where)->toArray();
		}
		
		if($options['task'] == 'admin-info'){
			$db = Zend_Registry::get('connectDb');
			//$db = Zend_Db::factory($adapter,$config);
			
			$sql = $db->select()
						->from($this->_name.' as n','*')
						->joinLeft('news_category as nc','n.cat_id=nc.id',array('nc.name as cat_name'))
						->where('n.id = ?', $arrParam['id'],INTEGER);
			$result = $db->fetchRow($sql);
		}
		if($options['task'] == 'admin-edit'){
			$db = Zend_Registry::get('connectDb');
			//$db = Zend_Db::factory($adapter,$config);
			
			$sql = $db->select()
						->from($this->_name.' as n','*')
						->joinLeft('news_category as nc','n.cat_id=nc.id',array('nc.name as cat_name'))
						->joinLeft('users as u','n.created_by=u.id',array('u.user_name as created_name'))
						->where('n.id = ?', $arrParam['id'],INTEGER);
			$result = $db->fetchRow($sql);
		}
		return $result;
		
		
		
		return $result;
	}
	
	public function deleteItem($arrParam = null, $options = null){
		if($options['task'] == 'admin-photo-product'){
			$where = ' id = ' . $arrParam['idp'];
			//xoa avatar
			$sql=$this->select()
  							->from($this->_name,array('full_image'))
  							->where($where);
			$result = $this->fetchAll($sql)->toArray();
            
			foreach($result as $key => $image){
				$uploadDir = $arrParam['controllerConfig']['<?php echo $this->imgUrl ?>Dir'];
                @unlink($uploadDir . 'full/' . $image['full_image']);					
				@unlink($uploadDir . 'slideshow/' . $image['full_image']);
											
			}
			//xoa record
			$this->delete($where);
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
}