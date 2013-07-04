<?php
class Default_Model_Permission extends Zend_Db_Table{
	protected $_name = 'user_group_privileges';
	protected $_primary = array('privilege_id','group_id');

  

	public function countItem($arrParam = null, $options = null){
		$db = Zend_Registry::get('connectDb');
		//$db = Zend_Db::factory($adapter,$config);
		$ssFilter = $arrParam['ssFilter'];
		
		$select = $db->select()
					->from($this->_name.' AS u',array('COUNT(u.privilege_id) AS totalItem'))
					->group('u.group_id');
		
		if(!empty($ssFilter['keywords'])){
				$keywords = '%' . $ssFilter['keywords'] . '%';
				$select->where('p.name LIKE ?',$keywords,STRING);
		}
		
		if($ssFilter['group_id']>0){
			$select->where('u.group_id = ?',$ssFilter['group_id'],INTEGER);
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
						 ->from($this->_name.' AS u',array('status','group_id','privilege_id'))
						 ->joinLeft('user_group AS g','g.id = u.group_id','g.group_name')
						 ->joinLeft('privileges AS p','p.id = u.privilege_id','p.name');
			
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
				$select->where('p.name LIKE ?',$keywords,STRING);
			}
			
			if($ssFilter['group_id']>0){
				$select->where('u.group_id = ?',$ssFilter['group_id'],INTEGER);
			}
			//echo $select;
			$result  = $db->fetchAll($select);
		}
		return $result;
	
	}
	
	public function saveItem($arrParam = null, $options = null){
		
		if($options['task'] == 'admin-add'){
			$row =  $this->fetchNew();

			$row->privileges 	   = $arrParam['privileges_id'];
			$row->group_id 	       = $arrParam['group_id'];
			$row->status 	       = $arrParam['status'];
  			
			
			$row->save();
		}
		if($options['task'] == 'admin-multi-add'){

            foreach ($arrParam['privileges_id'] as $privileges_id) {
             
                $row =  $this->fetchNew();

    			$row->privilege_id 	 = $privileges_id;
    			$row->group_id 	     = $arrParam['group_id'];
    			$row->status 	     = $arrParam['status'];
      			    			
    			$row->save();
            }
            
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
			$where = ' privilege_id = ' . $arrParam['id'] . ' AND group_id = '.$arrParam['gid'];
			
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
				
				//$ids = implode(',',$cid);
                
				foreach($cid as $key=>$info)
                {
                    $inf=explode('_',$info);
                    $where = ' privilege_id = ' . $inf[0] . ' AND group_id = '.$inf[1];
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
			
			/*$ids = implode(',',$cid);
			$data = array('status'=>$status);
			$where = 'id IN (' . $ids . ')';
			$this->update($data,$where);*/
            
            foreach($cid as $key=>$info)
            {
                $inf=explode('_',$info);
                
				$data = array('status'=>$status);
                
                $where = ' privilege_id = ' . $inf[0] . ' AND group_id = '.$inf[1];
                $this->update($data,$where);
            }
            
		}
		if($arrParam['id']>0){
			if($arrParam['type'] == 1){
				$status = 1;
			}else{
				$status = 0;
			}
			
			$data = array('status'=>$status);
			//$where = 'id = ' . $arrParam['id'];
            
            $inf=explode('_',$arrParam['id']);
            $where = ' privilege_id = ' . $inf[0] . ' AND group_id = '.$inf[1];
			$this->update($data,$where);
		}
		
	}
}