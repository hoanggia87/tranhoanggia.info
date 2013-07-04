<?php
class Default_Model_Users extends Zend_Db_Table{
	protected $_name = 'users';
	protected $_primary = 'id';

	public function countItem($arrParam = null, $options = null){
		$db = Zend_Registry::get('connectDb');
		//$db = Zend_Db::factory($adapter,$config);
		$ssFilter = $arrParam['ssFilter'];
		
		$select = $db->select()
					->from('users AS u',array('COUNT(u.id) AS totalItem'));
		
		if(!empty($ssFilter['keywords'])){
				$keywords = '%' . $ssFilter['keywords'] . '%';
				$select->where('u.user_name LIKE ?',$keywords,STRING);
		}
		
		if($ssFilter['group_id']>0){
			$select->where('u.group_id = ?',$ssFilter['group_id'],INTEGER);
		}
		//echo $select;
		$result = $db->fetchOne($select);
		return $result;
		
	}
    
    
    public function getUserByListIds($userList,$option=null)
    {
        $db = Zend_Registry::get('connectDb');
        if (!is_array($userList))
		{
			$userList = explode(",",$userList);	
		}
	
		foreach ($userList as $key)
		{
			$strUser .= "'$key',";
		}
        
        $strUser = substr($strUser,0,-1);
		$select = $db->select()
			 ->from('users AS u',array('id','user_name','status','email','register_date'))
			 ->joinLeft('user_group AS g','g.id = u.group_id','group_name')   
             ->where('u.id IN ('.$strUser.')');       
         $result = $db->fetchAll($select);
         return $result;    
            
    }
	public function checkAccount($account ,$options = null){
	  		
  		if($options == null){
  			$db = Zend_Registry::get('connectDb');
  			//$db = Zend_Db::factory($adapter,$config);
  			$sql = $db->select()
					->from($this->_name,array('id'))
					->where("user_name=?",$account,STRING);
			$result = $db->fetchAll($sql);
			$result = $result[0];
			if(count($result)==0)
				$result=count($result);
			else
				$result=count($result);
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
						 ->from('users AS u',array('id','user_name','status','email','register_date'))
						 ->joinLeft('user_group AS g','g.id = u.group_id','group_name');
			
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
				$select->where('u.user_name LIKE ?',$keywords,STRING);
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
			//$row->group_name 	= $arrParam['group_name'];
			$row->user_name 	= $arrParam['user_name'];
  			$row->user_avatar 	= $arrParam['user_avatar'];
  			$row->password 		= md5($arrParam['user_name'].$arrParam['password']); 			
  			$row->email 		= $arrParam['email'];
  			$row->first_name 	= $arrParam['first_name'];
  			$row->last_name 	= $arrParam['last_name'];
  			
  			$row->birthday 		= $arrParam['birthday'];
  			
  			$row->register_date = date('Y-m-d H:i:s');
  			$row->register_ip 	= getenv('REMOTE_ADDR');
  			$row->visited_date 	= '0000-00-00';
  			$row->visited_ip 	= '0.0.0.0';
  			//$row->active_code	= $arrParam['active_code'];
  			$row->status		= $arrParam['status'];
  			$row->sign			= $arrParam['sign'];
  			$row->group_id		= $arrParam['group_id'];
			
			$row->save();
		}
		
		if($options['task'] == 'admin-edit'){
			$where = 'id = ' . $arrParam['id'];
			$row = $this->fetchRow($where);
			
			$row->user_name 	= $arrParam['user_name'];
  			$row->user_avatar 	= $arrParam['user_avatar'];
  			$row->password 		= md5($arrParam['user_name'].$arrParam['password']); 			
  			$row->email 		= $arrParam['email'];
  			$row->first_name 	= $arrParam['first_name'];
  			$row->last_name 	= $arrParam['last_name'];  			
  			
  			$row->birthday 		= $arrParam['birthday'];
  			
  			//$row->register_date = date('Y-m-d');
  			//$row->register_ip 	= getenv('REMOTE_ADDR');
  			$row->visited_date 	= '0000-00-00';
  			$row->visited_ip 	= '0.0.0.0';
  			$row->active_code	= $arrParam['active_code'];
  			$row->status		= $arrParam['status'];
  			$row->sign			= $arrParam['sign'];
  			$row->group_id		= $arrParam['group_id'];
			
			$row->save();
		}
		if($options['task'] == 'admin-changepassword'){
			
			$where = 'id = ' . $arrParam['id'];
			$row = $this->fetchRow($where);
			
  			$row->password 		= md5($arrParam['user_name'].$arrParam['new_password']); 			
  						
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
			//xoa avatar
			$sql=$this->select()
  							->from($this->_name,array('user_avatar'))
  							->where($where);
			$result = $this->fetchAll($sql)->toArray();
			foreach($result as $key => $image)
  				{
  					$uploadDir = $arrParam['controllerConfig']['imagesDir'];					
					@unlink($uploadDir . 'thumb/' . $image['user_avatar']);
					@unlink($uploadDir  . $image['user_avatar']);								
  				}
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
				
				echo '<br>' . $ids = implode(',',$cid);
				echo '<br>' . $where = 'id IN (' . $ids . ')';
				//xoa avatar
				$sql=$this->select()
  							->from($this->_name,array('user_avatar'))
  							->where($where);
  				$result = $this->fetchAll($sql)->toArray();
				foreach($result as $key => $image)
  				{
  					$uploadDir = $arrParam['controllerConfig']['imagesDir'];					
					@unlink($uploadDir . 'thumb/' . $image['user_avatar']);
					@unlink($uploadDir  . $image['user_avatar']);								
  				}
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