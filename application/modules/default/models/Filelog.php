<?php
class Default_Model_Filelog extends Zend_Db_Table{
	protected $_name = 'counter';
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
					$select->where('n.ip LIKE ?',$keywords,STRING);
			}
			
		
			if($ssFilter['date_from']!='' && $ssFilter['date_to']!=''){
					$select->where("n.timelogin >= '".$ssFilter['date_from']."'");
					$select->where("n.timelogin <= '".$ssFilter['date_to']."'");
			}
			//echo $select;
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
						 ->from($this->_name.' AS n',array('id','ip','timelogin','browser','user_id','status'))
						 ->joinLeft('users as u','n.user_id=u.id','u.user_name');
						 
						 
		
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
				$select->where('n.ip LIKE ?',$keywords,STRING);
			}
			
			if($ssFilter['date_from']!='' && $ssFilter['date_to']!=''){
					$select->where("n.timelogin >= '".$ssFilter['date_from']."'");
					$select->where("n.timelogin <= '".$ssFilter['date_to']."'");
			}
			//echo $select;
			$result  = $db->fetchAll($select);
		}
		
		return $result;
	
	}
	
	public function saveItem($arrParam = null, $options = null){
		
		if($options['task'] == 'admin-add'){
			$row =  $this->fetchNew();
			
			$row->ip 		= $arrParam['ip'];
			$row->timelogin = $arrParam['timelogin'];
			$row->browser 	= $arrParam['browser'];
			$row->user_id 	= $arrParam['user_id'];
  			
			$row->save();
		}
		
		if($options['task'] == 'admin-edit'){
			$where = 'id = ' . $arrParam['id'];
			$row = $this->fetchRow($where);
		
			$row->ip 		= $arrParam['ip'];
			$row->timelogin = $arrParam['timelogin'];
			$row->browser 	= $arrParam['browser'];
			$row->user_id 	= $arrParam['user_id'];
			$row->save();
		}
		
	}
	
	public function getItem($arrParam = null, $options = null){
		
		if($options['task'] == 'admin-info' || $options['task'] == 'admin-edit'){
			$where = 'id = ' . $arrParam['id'];
			$result = $this->fetchRow($where)->toArray();
		}
		
		return $result;
	}
	
	public function deleteItem($arrParam = null, $options = null){
		if($options['task'] == 'admin-delete'){
			$where = ' id = ' . $arrParam['id'];
			
			//xoa record
			$this->delete($where);
		}
		
		if($options['task'] == 'admin-multi-delete'){
			$cid = $arrParam['cid'];
			
			if(count($cid)>0){
				if($arrParam['type'] == 1){
					$status = 1;
				}else{
					$status = 0;
				}
				
				$ids = implode(',',$cid);
				$where = 'id IN (' . $ids . ')';
				
				//xoa record
				$this->delete($where);
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
}