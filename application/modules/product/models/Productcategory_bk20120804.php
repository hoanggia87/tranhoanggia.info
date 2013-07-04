<?php
class Product_Model_Productcategory extends Zend_Db_Table{
	protected $_name = 'product_category';
	protected $_primary = 'id';

	public function countItem($arrParam = null, $options = null){
		$db = Zend_Registry::get('connectDb');
		//$db = Zend_Db::factory($adapter,$config);
		$ssFilter = $arrParam['ssFilter'];
		
		$select = $db->select()
					->from($this->_name.' AS c',array('COUNT(c.id) AS totalItem'));
		
		if(!empty($ssFilter['keywords'])){
				$keywords = '%' . $ssFilter['keywords'] . '%';
				$select->where('c.name LIKE ?',$keywords,STRING);
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
		
	
		
		if($options['task'] == "admin-list"){
  			if(!isset($options['parent_id'])){
  				$options['parent_id']=0;
  			}
  			$db = Zend_Registry::get('connectDb');
			//$db = Zend_Db::factory($adapter,$config);
			$sql = $db->select()
						->from($this->_name,array('id','name','parents','status','order'))
						->order('order ASC')						
					  	->order('id ASC');						
			$result = $db->fetchAll($sql);
			$dequy 	= new Zendvn_Proccess_Dequi();
			$newArray = array();
			//format theo chuan category
			if($options['root']=='true')
				$newArray[]=array(
							'id'=>0,
							'name'=>'Root'
				);
			$dequy->dequi($result,$options['parent_id'],1, $newArray,array('id','name','parents','status','order') );
  			$result = $newArray;	  	
  		}   
  		if($options['task'] == "admin-list-select"){
  			if(!isset($options['parent_id'])){
  				$options['parent_id']=0;
  			}
  			$db = Zend_Registry::get('connectDb');
  			//$db = Zend_Db::factory($adapter, $config);
			$sql = $db->select()
						->from($this->_name,array('id','name','parents','status','order'))
						->order('order ASC')						
					  	->order('id ASC');						
			$result = $db->fetchAll($sql);
			$dequy 	= new Zendvn_Proccess_Dequi();
			$newArray = array();
			//format theo chuan category
			if($options['root']=='true')
				$newArray[]=array(
							'id'=>0,
							'name'=>'Root'
				);
			$dequy->dequi($result,$options['parent_id'],1, $newArray,array('id','name','parents','status','order') );
  			$result = $newArray;	  	
  		}
  		elseif($options['task'] == 'list-location'){
			
			$db = Zend_Registry::get('connectDb');
			//$db = Zend_Db::factory($adapter, $config);
			$sql = $db->select()
				->from($this->_name,array('id','name'))				
				->where('status = ?', 1,INTEGER)		
				->where('parents = ?', $arrParam['parents'],INTEGER)		
							
				->order('order ASC')					
				->order('id ASC');	
			
            
            $result = $db->fetchAll($sql);	
			$newArray=array();
  			foreach($result as $key=>$info)
			{
				$newArray[$info['id']]=$info;
			}
            
			$result=$newArray;
  		} 
        elseif($options['task'] == 'list-location-search'){
            $db = Zend_Registry::get('connectDb');
			//$db = Zend_Db::factory($adapter, $config);
			$sql = $db->select()
				->from($this->_name,array('id','name'))				
				->where('status = ?', 1,INTEGER)		
				->where('parents = ?', $arrParam['parents'],INTEGER)		
							
				->order('order ASC')					
				->order('id ASC');	
			$result = $db->fetchAll($sql);	
			$newArray=array();
  			foreach($result as $key=>$info)
			{
				$newArray[$info['id']]=$info['name'];
			}
			$result=$newArray;
  		}
        elseif($options['task'] == "cms-list"){
  			$db = Zend_Registry::get('connectDb');
  			//$db = Zend_Db::factory($adapter, $config);
			$sql = $db->select()
						->from($this->_name.' as c',array('id','name','parents','status','order'))						
						->where('c.status=1')
						->order('c.order ASC')						
					  	->order('c.id ASC');						
			$result = $db->fetchAll($sql);
  			//echo $sql;			  	
  		} 
        
		return $result;
	
	}
	
	public function saveItem($arrParam = null, $options = null){
		
		if($options['task'] == 'admin-add'){
			$row =  $this->fetchNew();
			//$row->group_name 	= $arrParam['group_name'];
			$row->name 		= $arrParam['name'];
			$row->parents 	= $arrParam['parents'];
			$row->order 	= $arrParam['order'];
			$row->status 	= $arrParam['status'];
  			
			
			$row->save();
		}
		
		if($options['task'] == 'admin-edit'){
			$where = 'id = ' . $arrParam['id'];
			$row = $this->fetchRow($where);
			
			$row->name 		= $arrParam['name'];
			$row->parents 	= $arrParam['parents'];
			$row->order 	= $arrParam['order'];
			$row->status 	= $arrParam['status'];
			
			$row->save();
		}
		
	}
	
	public function getItem($arrParam = null, $options = null){
		
		if($options['task'] == 'admin-info'){
			$db = Zend_Registry::get('connectDb');
			//$db = Zend_Db::factory($adapter,$config);
			
			$sql = $db->select()
						->from($this->_name.' as c1',array('id','name','parents','status','order'))
						->joinLeft($this->_name.' as c2','c1.parents=c2.id',array('c2.name as parent_name','c2.id as parent_id'))
						->where('c1.id = ?', $arrParam['id'],INTEGER);
			$result = $db->fetchRow($sql);
		}
		if($options['task'] == 'admin-edit'){
			$where = 'id = ' . $arrParam['id'];
			$result = $this->fetchRow($where)->toArray();
		}
		return $result;
	}
	
	public function deleteItem($arrParam = null, $options = null){
		if($options['task'] == 'admin-delete'){
			$result = $this->fetchAll()->toArray();
			
			$cid = array($arrParam['id']);
			if(count($cid)>0){
				for($i=0; $i<count($cid); $i++){
					$id = $cid[$i];
					$dequi = new Zendvn_Proccess_Dequi();
					$dequi->dequi($result,$id,1,$newArray);
					
					$newArray[]['id'] = $id;
				}

				foreach ($newArray as $key => $val){
						//echo '<br> Delete id = ' . $val['id'];
						$where = 'id  = ' . $val['id'];
						$this->delete($where);
						
						
				}
			}
		}
		
		if($options['task'] == 'admin-multi-delete'){
			
			$result = $this->fetchAll()->toArray();
			
			$cid = $arrParam['cid'];
			if(count($cid)>0){
				for($i=0; $i<count($cid); $i++){
					$id = $cid[$i];
					$dequi = new Zendvn_Proccess_Dequi();
					$dequi->dequi($result,$id,1,$newArray);
					
					$newArray[]['id'] = $id;
				}

				foreach ($newArray as $key => $val){
						//echo '<br> Delete id = ' . $val['id'];
						$where = 'id  = ' . $val['id'];
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
}