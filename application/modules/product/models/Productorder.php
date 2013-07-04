<?php
class Product_Model_Productorder extends Zend_Db_Table{
	protected $_name = 'product_order'; 
  	protected $_primary = 'id';
  	
	public function countItem($arrParam = null ,$options = null){
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

  	public function listItem($arrParam = null, $options = null){
  		$paginator = $arrParam['paginator'];
		$ssFilter = $arrParam['ssFilter'];
		
  		if($options['task'] == "admin-list"){
  			
  			$db = Zend_Registry::get('connectDb');
			//$db = Zend_Db::factory($adapter,$config);
			$select = $db->select()
						->from($this->_name,array('id','name','phone','email','created','created_by','status'))						
					  	->order('status ASC')
                        ->order('created DESC');						
			if($paginator['itemCountPerPage']>0){
				$page = $paginator['currentPage'];
				$rowCount = $paginator['itemCountPerPage'];
				$select->limitPage($page,$rowCount);
			}			  	
			$result = $db->fetchAll($select);	

  		}  

		return $result;
	
  	}

  	

  	
  	public function saveItem($arrParam = null, $options = null){
		
		if($options['task'] == 'admin-add'){
			$row =  $this->fetchNew();
			//$row->group_name 	= $arrParam['group_name'];
			$row->name 		    = $arrParam['name'];
  			$row->phone 		= $arrParam['phone'];
			$row->address_1 		= $arrParam['address_1'];
            $row->address_2 		= $arrParam['address_2'];
            $row->created 		= $arrParam['created'];
            $row->created_by 	= $arrParam['created_by'];
            $row->status 		= $arrParam['status'];
            $row->note 		    = $arrParam['note'];
            $row->email 		= $arrParam['email'];
            
			return $row->save();
            
		}
		
		if($options['task'] == 'admin-edit'){
			$where = 'id = ' . $arrParam['id'];
			$row = $this->fetchRow($where);
			$row->name 		    = $arrParam['name'];
  			$row->phone 		= $arrParam['phone'];
			$row->address_1 		= $arrParam['address_1'];
            $row->address_2 		= $arrParam['address_2'];
            $row->created 		= $arrParam['created'];
            $row->created_by 	= $arrParam['created_by'];
            $row->status 		= $arrParam['status'];
            $row->note 		    = $arrParam['note'];
            $row->email 		= $arrParam['email'];
			
			$row->save();
		}
		
	}
	
	public function getItem($arrParam = null, $options = null){
		
		if($options['task'] == 'admin-info'){
			$where = 'id = ' . $arrParam['id'];
			$result = $this->fetchRow($where)->toArray();
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