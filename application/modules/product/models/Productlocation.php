<?php
class Product_Model_Productlocation extends Zend_Db_Table{
	protected $_name = 'product_location';
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
  		elseif($options['task'] == "admin-list-select"){
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
		
			if(!$arrParam['parents'])
			{
				$arrParam['parents']=0;
			}
			
			$db = Zend_Registry::get('connectDb');
			//$db = Zend_Db::factory($adapter, $config);
			$sql = $db->select()
				->from($this->_name,array('id','name'))				
				->where('status = ?', 1,INTEGER)		
				->where('parents = ?', $arrParam['parents'],INTEGER)		
							
				->order('order ASC')					
				->order('id ASC');	
			$result = $db->fetchAll($sql);	

			
  		}
        elseif($options['task'] == 'list-location-search'){
		
			if(!$arrParam['parents'])
			{
				$arrParam['parents']=0;
			}
			
			$db = Zend_Registry::get('connectDb');
			//$db = Zend_Db::factory($adapter, $config);
			$sql = $db->select()
				->from($this->_name,array('id','name'))				
				->where('status = ?', 1,INTEGER)		
				->where('parents = ?', $arrParam['parents'],INTEGER)		
							
				->order('order ASC')					
				->order('id ASC');	
			$result = $db->fetchAll($sql);	
            $newArr=array();
            foreach($result as $key=>$info)
            {
                $newArr[$info['id']]=$info['name'];
                
            }
			$result=$newArr;
  		}
        elseif($options['task'] == 'list-location-by-parent'){
		
            $newArr=array();
			if($arrParam['ph']!=0)//n?u ngý?i dùng ch?n phý?ng
			{
				$newArr[0]=array('id'=>$arrParam['ph']);
                
			}
            else //n?u ngý?i dùng không ch?n phý?ng
            {
                $db = Zend_Registry::get('connectDb');
                if($arrParam['qu']!=0)//n?u ngý?i dùng ch?n qu?n th? m?nh l?y danh sách ID c?a phý?ng
                {
                    $sql = $db->select()
            				->from($this->_name,array('id'))				
            				->where('status = 1')		
            				->where('parents = ?', $arrParam['qu'],INTEGER)
            				->order('order ASC')					
            				->order('id DESC');	
                    $newArr = $db->fetchAll($sql);
                }
                else //n?u ngý?i dùng không ch?n qu?n
                {
                    //l?y danh sách Qu?n trong thành ph?
                    $sql = $db->select()
            				->from($this->_name,array('id'))				
            				->where('status = 1')		
            				->where('parents = ?', $arrParam['tp'],INTEGER)
            				->order('order ASC')					
            				->order('id DESC');
                    $newArr = $db->fetchAll($sql);
                    $listQu='';
                    foreach($newArr as $k=>$v)
                    {
                        $listQu.=$v['id'].',';
                    }
                    if($listQu)
                    {
                        $listQu=substr($listQu,0,strlen($listQu)-1);
                    }
                    else
                    {
                        $listQu='0';
                    }
                    
                    //l?y danh sách phý?ng trong danh sách qu?n
                    $sql = $db->select()
            				->from($this->_name,array('id'))				
            				->where('status = 1')		
            				->where('parents IN ('.$listQu.')')
            				->order('order ASC')					
            				->order('id DESC');	
                    $newArr = $db->fetchAll($sql);
                }
            }
			
			foreach($newArr as $k=>$i)
            {
                $result[$i['id']]=$i['id'];
            }
			
			
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
		elseif($options['task'] == 'admin-edit'){
			$where = 'id = ' . $arrParam['id'];
			$result = $this->fetchRow($where)->toArray();
		}
        elseif($options['task'] == 'admin-location-parents'){
            $db = Zend_Registry::get('connectDb');
			//$db = Zend_Db::factory($adapter,$config);
			
			$sql = $db->select()
						->from($this->_name.' as c1',array('c1.id as ph','c1.parents as qu'))
						->joinLeft($this->_name.' as c2','c1.parents=c2.id',array('c2.parents as tp'))
						->where('c1.id = ?', $arrParam['district_id'],INTEGER);//$arrParam['id'] id c?a phu?ng
			$result = $db->fetchRow($sql);
            // select p2.id,p2.parents  from product_location  p2 where p2.id = ( select p1.parents from product_location  p1 where p1.id=100)
        }
        elseif($options['task'] == 'admin-location-info-name'){
            $db = Zend_Registry::get('connectDb');
			//$db = Zend_Db::factory($adapter,$config);
			
			$sql = $db->select()
						->from($this->_name,array('name','id'))
						->where('id = ?', $arrParam['tp'],INTEGER);//$arrParam['id'] id c?a phu?ng
			$result = $db->fetchRow($sql);
            // select p2.id,p2.parents  from product_location  p2 where p2.id = ( select p1.parents from product_location  p1 where p1.id=100)
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