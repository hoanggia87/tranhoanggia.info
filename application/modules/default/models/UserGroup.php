<?php
class Default_Model_UserGroup extends Zend_Db_Table{
	protected $_name = 'user_group';
	protected $_primary = 'id';
	public function itemInSelectbox($arrParam = null, $options = null){
		$db = Zend_Registry::get('connectDb');
		//$db = Zend_Db::factory($adapter,$config);
		if($options == null){
			$select = $db->select()
						 ->from($this->_name, array('id','group_name'));
			$result = $db->fetchPairs($select)	;
			$result[0] = ' -- Select a Item -- ';
			ksort($result);
								 
		}
		return $result;
	} 
	public function countItem($arrParam = null, $options = null){
		$db = Zend_Registry::get('connectDb');
		//$db = Zend_Db::factory($adapter,$config);
		$ssFilter = $arrParam['ssFilter'];
		
		$select = $db->select()
					->from('user_group AS g',array('COUNT(g.id) AS totalItem'));
		
		if(!empty($ssFilter['keywords'])){
				$keywords = '%' . $ssFilter['keywords'] . '%';
				$select->where('g.group_name LIKE ?',$keywords,STRING);
		}
		//echo $select;
		$result = $db->fetchOne($select);
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
						 ->from('user_group AS g',array('id','group_name','status','group_acp','order','permission'))
						 ->joinLeft('users AS u','g.id = u.group_id','COUNT(u.id) AS members')
						 ->group('g.id');
			
			if($paginator['itemCountPerPage']>0){
				$page = $paginator['currentPage'];
				$rowCount = $paginator['itemCountPerPage'];
				$select->limitPage($page,$rowCount);
			}
			
			if(!empty($ssFilter['keywords'])){
				$keywords = '%' . $ssFilter['keywords'] . '%';
				$select->where('g.group_name LIKE ?',$keywords,STRING);
			}
			//echo $select;
			$result  = $db->fetchAll($select);
		}
		return $result;
	
	}
	
	public function saveItem($arrParam = null, $options = null){
		
		if($options['task'] == 'admin-add'){
			$row =  $this->fetchNew();
			$row->group_name 	= $arrParam['group_name'];
			$row->avatar 		= $arrParam['avatar'];
			$row->ranking 		= $arrParam['ranking'];			
			$row->group_acp 	= $arrParam['group_acp'];
			$row->group_default = $arrParam['group_default'];
			$row->created 		= date("Y-m-d");
			$row->created_by 	= 1;
			$row->status 		= $arrParam['status'];
			$row->order 		= $arrParam['order'];
			$row->permission	= $arrParam['permission'];
			
			$row->save();
		}
		
		if($options['task'] == 'admin-edit'){
			$where = 'id = ' . $arrParam['id'];
			
			$row =  $this->fetchRow($where);
			$row->group_name 	= $arrParam['group_name'];
			$row->avatar 		= $arrParam['avatar'];
			$row->ranking 		= $arrParam['ranking'];			
			$row->group_acp 	= $arrParam['group_acp'];
			$row->group_default = $arrParam['group_default'];
			$row->modified 		= date("Y-m-d");
			$row->modified_by 	= 1;
			$row->status 		= $arrParam['status'];
			$row->order 		= $arrParam['order'];
			$row->permission	= $arrParam['permission'];
			
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
				
				echo '<br>' . $ids = implode(',',$cid);
				echo '<br>' . $where = 'id IN (' . $ids . ')';
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