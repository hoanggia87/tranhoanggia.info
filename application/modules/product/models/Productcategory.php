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
		

		//lấy danh sach lên 
		$db = Zend_Registry::get('connectDb');
		//$db = Zend_Db::factory($adapter,$config);
		$sql = $db->select()
					->from($this->_name,array('id','name','parents','status','order'))
					->order('order ASC')						
				  	->order('id DESC');						
		$result = $db->fetchAll($sql);

		$dequy 	= new Zendvn_Proccess_Dequi();
		$newArray = array();
		$dequy->dequi($result,0,1, $newArray,array('id','name','parents','status','order') );
		
		
		$result = $newArray;



		$orderPos=array();
		$arrArrayTemp=array();		//lưu lại thông tin của mang order
		$i=0;
		$iPos=0;
		$arrNeed=array();
		foreach($newArray as $key => $val){
			//vị trí thứ tự hiện t
			$orderPos=$arrOrder[$val['level']][$val['parents']]+=1;

			$arrArrayTemp[$i]['id']=$val['id'];
			$arrArrayTemp[$i]['level']=$val['level'];
			$arrArrayTemp[$i]['parents']=$val['parents'];
			$arrArrayTemp[$i]['order']=$orderPos;

			if($val['id']==$arrParam['id'])//nếu là phần tự được sort thì lưu lại thong tin
			{
				$arrNeed=$arrArrayTemp[$i];
				$iPos=$i;
			}
			$i++;
		}

	
		//sort
		$temp1=$temp2=array();
		$len=count($arrArrayTemp);
		if($arrParam['type']==-1)//up
		{
			for ($j=$iPos-1; $j >= 0; $j--) { 
				if($arrArrayTemp[$j]['level']==$arrNeed['level'] && $arrArrayTemp[$j]['parents']==$arrNeed['parents'])
				{
					
					$temp=$arrArrayTemp[$j]['order'];
					$arrArrayTemp[$j]['order']=$arrArrayTemp[$iPos]['order'];
					$arrArrayTemp[$iPos]['order']=$temp;
					//lưu lại thông tin 2 phần tử cần update
					$temp1=$arrArrayTemp[$j];
					$temp2=$arrArrayTemp[$iPos];
					break;
				}
			}
		}
		elseif ($arrParam['type']==1) //down
		{
			for ($j=$iPos+1; $j < $len; $j++) { 
				if($arrArrayTemp[$j]['level']==$arrNeed['level'] && $arrArrayTemp[$j]['parents']==$arrNeed['parents'])
				{
					$temp=$arrArrayTemp[$j]['order'];
					$arrArrayTemp[$j]['order']=$arrArrayTemp[$iPos]['order'];
					$arrArrayTemp[$iPos]['order']=$temp;
					//lưu lại thông tin 2 phần tử cần update
					$temp1=$arrArrayTemp[$j];
					$temp2=$arrArrayTemp[$iPos];
					break;
				}
			}
		}
		
		//upddate vào db


		if($temp1 && $temp2)
		{
			$info =	new Zendvn_System_Info();
			$infoUser=	$info->getInfo();
			$infoUser=$infoUser['member'];

			$modified=date('Y-m-d H:i:s');
			$modified_by 	= $infoUser['user_id'];

			$strSQL='UPDATE '.$this->_name.' SET `modified`=\''.$modified.'\',`modified_by`=\''.$modified_by.'\',`order` = (CASE `id` ';
			$strSQL.=' WHEN '.$temp1['id'].' THEN '.$temp1['order'];
			$strSQL.=' WHEN '.$temp2['id'].' THEN '.$temp2['order'];
			echo $strSQL.=	' ELSE `order` END) WHERE id IN ('.$temp1['id'].','.$temp2['id'].') ';
		
			$db->query($strSQL);


			
			
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
					  	->order('id DESC');						
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
					  	->order('id DESC');						
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
			
            //echo $sql;
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
                        ->joinLeft($this->_name.' AS nc1','nc1.id = c.parents',array('nc1.name as cat_name_1','nc1.id as cat_id_1'))  						
						->where('c.status=1')
						->order('c.order ASC')						
					  	->order('c.id ASC');
                          						
            if($options['parent'])
            {
                $sql->where('c.parents=?',$options['parent'],INTEGER);                

            }
            
            
           
            if($options['inlude_parent'])
            {

                $sql->orWhere('c.id=?',$options['parent'],INTEGER);                

            }
           
		    $result= $db->fetchAll($sql);
            foreach($result as $key=>$info)
            {
                $newArray[$info['id']]=$info;
                
            }
            
            $result=$newArray;
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
			//$row->order 	= $arrParam['order'];

			


			$info =	new Zendvn_System_Info();
			$infoUser=	$info->getInfo();

			$infoUser=$infoUser['member'];

			$currDay=date('Y-m-d H:i:s');
			$row->created 		= $currDay;
			$row->created_by 	= $infoUser['user_id'];
			$row->modified 		= $currDay;
			$row->modified_by 	= $infoUser['user_id'];


			//$row->status 	= $arrParam['status'];
  			
			
			return $row->save();
		}
		
		if($options['task'] == 'admin-edit'){
			$where = 'id = ' . $arrParam['id'];
			$row = $this->fetchRow($where);
			
			$row->name 		= $arrParam['name'];
			$row->parents 	= $arrParam['parents'];
			$row->order 	= $arrParam['order'];
			$row->status 	= $arrParam['status'];

			$info =	new Zendvn_System_Info();
			$infoUser=	$info->getInfo();

			$infoUser=$infoUser['member'];

			$currDay=date('Y-m-d H:i:s');
			
			$row->modified 		= $currDay;
			$row->modified_by 	= $infoUser['user_id'];


			
			$row->save();
		}
		
	}
	
	public function getItem($arrParam = null, $options = null){
		
		if($options['task'] == 'admin-info'){
			$db = Zend_Registry::get('connectDb');
			//$db = Zend_Db::factory($adapter,$config);
			
			$sql = $db->select()
						->from($this->_name.' as c1',array('id','name','parents','status','order'))
						->joinLeft($this->_name.' as c2','c1.parents=c2.id',array('c2.name as parent_name','c2.id as parent_id','c2.name as cat_name_1','c2.id as cat_id_1'))
						->where('c1.id = ?', $arrParam['id'],INTEGER);
			$result = $db->fetchRow($sql);
		}
		if($options['task'] == 'admin-edit'){
			//$where = 'id = ' . $arrParam['id'];
			//$result = $this->fetchRow($where)->toArray();

			$db = Zend_Registry::get('connectDb');
			$sql = $db->select()
						->from($this->_name.' as c1',array('*',	'DATE_FORMAT(c1.`created`,\' %H:%i:%s %d/%m/%Y\') as created_fm',
																'DATE_FORMAT(c1.`modified`,\' %H:%i:%s %d/%m/%Y\') as modified_fm'))
						->joinLeft($this->_name.' as c2','c1.parents=c2.id',array('c2.name as parent_name','c2.id as parent_id','c2.name as cat_name_1','c2.id as cat_id_1'))
						->where('c1.id = ?', $arrParam['id'],INTEGER);
			$result = $db->fetchRow($sql);

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