<?php
class Product_Model_Productspecial extends Zend_Db_Table{
	protected $_name = 'product_special';
	protected $_primary = 'id';

	public function countItem($arrParam = null, $options = null){
		$db = Zend_Registry::get('connectDb');
		//$db = Zend_Db::factory($adapter,$config);
		$ssFilter = $arrParam['ssFilter'];
		
		$select = $db->select()
					->from($this->_name.' AS n',array('COUNT(n.id) AS totalItem'));
		
		if(!empty($ssFilter['keywords'])){
				$keywords = '%' . $ssFilter['keywords'] . '%';
				$select->where('n.name LIKE ?',$keywords,STRING);
		}
		
		if($ssFilter['cat_id']>0){
			$select->where('n.cat_id = ?',$ssFilter['cat_id'],INTEGER);
		}
         if($ssFilter['district_id']>0){
			$select->where('n.district_id = '.$ssFilter['district_id'].' OR n.thanhpho = '.$ssFilter['district_id'].' OR n.quan = '.$ssFilter['district_id']);                
			
		}
		//echo $select;
		$result = $db->fetchOne($select);
		return $result;
		
	}
	
	public function updateBroadcastCount($arrParam = null, $options = null)
	    {
	        try{
	            $id     = $arrParam['id'];
	            $db     = Zend_Registry::get('connectDb');
	            
	            $sql    = "UPDATE ".$this->_name." SET broadcast_count=IF(broadcast_count IS NULL,'1',broadcast_count+1) WHERE id='".$id."'";           
	            $rs     = $db->query($sql); 
	            if($rs)
	                {
	                    return 1;
	                }
	             else
	                {
	                    return 0;
	                }   
	        }
	        catch(Exception $e)
	        {
	            return 0;
	        }
	        
	    }  
	    
    public function isOwner($arrParam = null, $options = null){
        
        $ns = new Zend_Session_Namespace('info');
		$nsInfo = $ns->getIterator();
      
		if($arrParam['created_by']==$nsInfo['member']['id'])
        {
            return 1;
        }
        elseif($nsInfo['group']['permission']=='Full Access')
        {            
            return 1;
        }
        elseif($nsInfo['group']['id']==27)//group Super Product Manager có quyền chỉnh sửa trong product của bất kỳ sản phẩm nào 
        {            
            return 1;
        }
        else
        {
            return 0;  
        }
      
        
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
						 ->from($this->_name.' AS n',array('id','name','status','order','created'))
						 ->joinLeft('product_category AS nc','nc.id = n.cat_id','nc.name as cat_name')
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
				$select->where('n.name LIKE ?',$keywords,STRING);
			}
			if($ssFilter['cat_id']>0){
				$select->where('n.cat_id = ?',$ssFilter['cat_id'],INTEGER);
				
			}
            if($ssFilter['district_id']>0){
				$select->where('n.district_id = '.$ssFilter['district_id'].' OR n.thanhpho = '.$ssFilter['district_id'].' OR n.quan = '.$ssFilter['district_id']);                
				
			}
			if($ssFilter['date_from']!='' && $ssFilter['date_to']!=''){
					$select->where("n.created >= '".$ssFilter['date_from']."'");
					$select->where("n.created <= '".$ssFilter['date_to']."'");
			}
			//echo $select;
			$result  = $db->fetchAll($select);
		}
		if($options['task'] == 'front-order'){
			$select = $db->select()
						 ->from($this->_name.' AS p',array('id','name','summary','broadcast_count','image','created','percent'))
                         ->where('p.thanhpho = ?',$arrParam['tp'],INTEGER)
                         ->where('p.start_date < Now()')
						 ->where('p.end_date > Now()')						 
                         ->where('p.cat_id = ?',$arrParam['cat_id'],INTEGER)
                         ->where('p.id <> ?',$arrParam['id'],INTEGER)
						 ->joinLeft('product_category AS pc','pc.id = p.cat_id',array('pc.id as cat_id','pc.name as cat_name'))
						 ->group('p.id')						 
						 ->order('p.id DESC')
                         ->limit(7, 0);
			$result  = $db->fetchAll($select);						
		}
        if($options['task'] == 'list-suggest-name')
        {
            if($arrParam['q'])
            {
                
                $col = $db->quoteIdentifier('p.name');
                $where1 = $db->quoteInto("$col LIKE ?", '%'.$arrParam['k'].'%');
                $where2 = $db->quoteInto('match(p.name,p.summary) against(?)',$arrParam['k']);
                
                
                $select = $db->select()
    						->from($this->_name.' AS p',array('id','name'))
                            ->where($where1 .' OR '.$where2)
                            ->where('p.thanhpho = ?',$arrParam['tp'],INTEGER)
                            ->joinLeft('product_category AS pc','pc.id = p.cat_id',array('pc.id as cat_id','pc.name as cat_name'))
                            ->group('p.id')
                            ->order('p.id DESC')
                            ->limit(10, 0);
                $result  = $db->fetchAll($select);
            }
            else
            {
                $result=null;
            }
            
            	
        }
        if($options['task'] == 'list-top'){
            $select = $db->select()
						 ->from($this->_name.' AS p',array('id','name','image','summary','broadcast_count','percent','broadcast_count'))
                         ->where('p.thanhpho = ?',$arrParam['tp'],INTEGER)
						 ->where('p.is_top = 1')
						 ->where('p.start_date_istop < Now()')
						 ->where('p.end_date_istop > Now()')
                         ->where('p.start_date < Now()')
						 ->where('p.end_date > Now()')
						 ->joinLeft('product_category AS pc','pc.id = p.cat_id',array('pc.id as cat_id','pc.name as cat_name'))
						 ->group('p.id')
						 ->order('p.id DESC');
			$result  = $db->fetchAll($select);
        }
        if($options['task'] == 'list-business'){//liet ke danh sách các s?n ph?m c?a doanh nghi?p
            $arrayField=array('id','name','image','summary','broadcast_count','percent','IF((start_date_ishot <= Now() && Now() <= end_date_ishot && hot=1),1,0) as is_hot');
            $select = $db->select()
						 ->from($this->_name.' AS p',$arrayField)
                         ->where('p.thanhpho = ?',$arrParam['tp'],INTEGER)
                         ->where('p.isbusiness = 1 ')						 
						 ->where('p.start_date_isbusiness < Now()')
						 ->where('p.end_date_isbusiness > Now()')                                               
                         ->where('p.start_date < Now()')
						 ->where('p.end_date > Now()')                                                                                                    
                                          
						 ->joinLeft('product_category AS pc','pc.id = p.cat_id',array('pc.id as cat_id','pc.name as cat_name'))
						 ->group('p.id')                         
						 ->order('p.id DESC END');                         
			$result  = $db->fetchAll($select);
        }
        if($options['task'] == 'list-by-cat'){//liet ke danh sách các s?n ph?m c?a doanh nghi?p
            $arrayField=array('id','name','image','summary','broadcast_count','percent','IF((start_date_ishot <= Now() && Now() <= end_date_ishot && hot=1),1,0) as is_hot');
            $select = $db->select()
						 ->from($this->_name.' AS p',$arrayField)
                         ->where('p.cat_id = ?',$arrParam['cat'],INTEGER)                                              
                         ->where('p.start_date < Now()')
						 ->where('p.end_date > Now()')   
						 ->joinLeft('product_category AS pc','pc.id = p.cat_id',array('pc.id as cat_id','pc.name as cat_name'))
						 ->group('p.id')                         
						 ->order('p.id DESC END');                         
			$result  = $db->fetchAll($select);
        }
        if($options['task'] == 'list-business-front'){//liet ke danh sách các s?n ph?m c?a doanh nghi?p
            $select = $db->select()
						 ->from($this->_name.' AS p',array('id','name','summary','broadcast_count','image','percent'))
                         ->where('p.thanhpho = ?',$arrParam['tp'],INTEGER)
                         ->where('p.isbusiness = 1 ')						 
						 ->where('p.start_date_isbusiness < Now()')
						 ->where('p.end_date_isbusiness > Now()')
                         ->where('p.status = 1')
						 ->where('p.start_date_isfront < Now()')
						 ->where('p.end_date_isfront > Now()')                         
                         ->where('p.start_date < Now()')
						 ->where('p.end_date > Now()')                                                                                                    
                                          
						 ->joinLeft('product_category AS pc','pc.id = p.cat_id',array('pc.id as cat_id','pc.name as cat_name'))
						 ->group('p.id')                         
						 ->order('p.id DESC END');                         
			$result  = $db->fetchAll($select);
        }
		if($options['task'] == 'list-all-by-category'){
		
			$select = $db->select()
						 ->from($this->_name.' AS p',array('id','name','image','summary','broadcast_count','percent'))
						 ->where('p.thanhpho = ?',$arrParam['tp'],INTEGER)
                         ->where('p.status = 1')
						 ->where('p.start_date_isfront < Now()')
						 ->where('p.end_date_isfront > Now()')
                         ->where('p.start_date < Now()')
						 ->where('p.end_date > Now()')
						 ->joinLeft('product_category AS pc','pc.id = p.cat_id',array('pc.id as cat_id','pc.name as cat_name'))
						 ->group('p.id')
						 ->order('p.id DESC');
			$result  = $db->fetchAll($select);			
			
			$newArray=array();
			
			foreach($result as $key=>$info)
			{
				$newArray[$info['cat_id']][]=$info;				
			}
			$result=$newArray;
			
		}
        
		if($options['task'] == 'list-by-search'){
            $arrayField=array('id','name','broadcast_count','image','summary','percent','IF((start_date_ishot <= Now() && Now() <= end_date_ishot && hot=1),1,0) as is_hot');
            
            $listPhuong=implode(',',$arrParam['listPhuong']);
            if($arrParam['listPhuong']!=0)
            {
                if($arrParam['cat'])//neu cat khac rong va 0 th? them thang nay vao
                {
                    if($arrParam['cat']=='9999')//day la category doanh nghiep                    
                    {
                        $select = $db->select()
						 ->from($this->_name.' AS p',$arrayField)
                         ->where('p.isbusiness = 1 ')						 
						 ->where('p.start_date_isbusiness < Now()')
						 ->where('p.end_date_isbusiness > Now()')
                         ->where('p.start_date < Now()')
						 ->where('p.end_date > Now()')
                                                                                                    
                         ->where('p.district_id in ('.$listPhuong.')')
                         
						 ->joinLeft('product_category AS pc','pc.id = p.cat_id',array('pc.id as cat_id','pc.name as cat_name'))
						 ->group('p.id')
                         
                         ->order('p.start_date_ishot < Now() AND p.end_date_ishot < Now() AND p.hot DESC')
						 ->order('p.id DESC END')
                         
                         ;
                     }
                    else
                    {                                                                
                                                                                   
                        $select = $db->select()
						 ->from($this->_name.' AS p',$arrayField)
						 
						 ->where('p.start_date < Now()')
						 ->where('p.end_date > Now()')
                         ->where('p.district_id in ('.$listPhuong.')')
                         ->where('p.cat_id = ?',$arrParam['cat'],INTEGER)
						 ->joinLeft('product_category AS pc','pc.id = p.cat_id',array('pc.id as cat_id','pc.name as cat_name'))
						 ->group('p.id')
                         ->order('p.start_date_ishot < Now() AND p.end_date_ishot < Now() AND p.hot DESC')                    
						 ->order('p.id DESC END')
                         ;
                         
                    }
                }
                else
                {
                    
                    $select = $db->select()
						 ->from($this->_name.' AS p',$arrayField)
						 
						 ->where('p.start_date < Now()')
						 ->where('p.end_date > Now()')
                         ->where('p.district_id in ('.$listPhuong.')')
                         //->where('p.cat_id = ?',$arrParam['cat'],INTEGER)
						 ->joinLeft('product_category AS pc','pc.id = p.cat_id',array('pc.id as cat_id','pc.name as cat_name'))
						 ->group('p.id')
                         ->order('p.start_date_ishot < Now() AND p.end_date_ishot < Now() AND p.hot DESC')
						 ->order('p.id DESC END')
                         ;
                }
                   
                
            }
            else
            {
                if($arrParam['cat'])//neu cat khac rong va 0 th? them thang nay vao
                {
                    if($arrParam['cat']=='9999')//day la category doanh nghiep                    
                    {
                        $select = $db->select()
						 ->from($this->_name.' AS p',$arrayField)
						 
						 ->where('p.isbusiness = 1 ')						 
						 ->where('p.start_date_isbusiness < Now()')
						 ->where('p.end_date_isbusiness > Now()')
                         ->where('p.start_date < Now()')
						 ->where('p.end_date > Now()')
                         
                         ->where('p.district_id in ('.$listPhuong.')')                         
						 ->joinLeft('product_category AS pc','pc.id = p.cat_id',array('pc.id as cat_id','pc.name as cat_name'))
						 ->group('p.id')
                         ->order('p.start_date_ishot < Now() AND p.end_date_ishot < Now() AND p.hot DESC')
						 ->order('p.id DESC END')
                         ;
                     }
                     
                    else
                    {                       
                        $select = $db->select()
						 ->from($this->_name.' AS p',$arrayField)
						 
						 ->where('p.start_date < Now()')
						 ->where('p.end_date > Now()')
                         ->where('p.district_id in ('.$listPhuong.')')                         
						 ->joinLeft('product_category AS pc','pc.id = p.cat_id',array('pc.id as cat_id','pc.name as cat_name'))
						 ->group('p.id')
                         ->order('p.start_date_ishot < Now() AND p.end_date_ishot < Now() AND p.hot DESC')
						 ->order('p.id DESC END')
                         ;                                                                 
                    }                
                    
                    
                    
                }
                else
                {
                    $select = $db->select()
						 ->from($this->_name.' AS p',$arrayField)
						 
						 ->where('p.start_date < Now()')
						 ->where('p.end_date > Now()')
                         ->where('p.district_id in ('.$listPhuong.')')
                         ->where('p.cat_id = ?',$arrParam['cat'],INTEGER)                         
						 ->joinLeft('product_category AS pc','pc.id = p.cat_id',array('pc.id as cat_id','pc.name as cat_name'))
						 ->group('p.id')
                         ->order('p.start_date_ishot < Now() AND p.end_date_ishot < Now() AND p.hot DESC')
						 ->order('p.id DESC END')
                         ;
                }
            }
            if($arrParam['k'])
            {
                $col = $db->quoteIdentifier('p.name');
                $where1 = $db->quoteInto("$col LIKE ?", '%'.$arrParam['k'].'%');
                $where2 = $db->quoteInto('match(p.name,p.summary) against(?)',$arrParam['k']);
                $select->where($where1 .' OR '.$where2); 
                //$select->where('match(p.name,p.summary) against(?)',$arrParam['k'],STRING);
            }
			//echo $select;
			$result  = $db->fetchAll($select);			
			
			
            /*$newArray=array();			
			foreach($result as $key=>$info)
			{
				$newArray[$info['cat_id']][]=$info;				
			}
			$result=$newArray;*/
			
		}
		
		return $result;
	
	}
	
	public function saveItem($arrParam = null, $options = null){
		
     
		if($options['task'] == 'admin-add'){
		  
			$row =  $this->fetchNew();
			
			$row->name 			= $arrParam['name'];
  			$row->summary 		= $arrParam['summary'];
			$row->summary_1 	= $arrParam['summary_1'];
			$row->summary_2 	= $arrParam['summary_2'];
  			$row->description 	= $arrParam['description'];  			
  			$row->image 		= $arrParam['image'];
			$row->image1 		= $arrParam['image1'];
			$row->map_image 	= $arrParam['map_image'];
  			$row->price 		= $arrParam['price'];
			$row->price_1 		= $arrParam['price_1'];
  			$row->hot 			= $arrParam['hot'];
  			$row->created 		= date('Y-m-d H:i:s');
            
            
            $ns = new Zend_Session_Namespace('info');
            $nsInfo = $ns->getIterator();
      
  			$row->created_by 	= $nsInfo['member']['id'];
  			$row->modified 		= date('Y-m-d H:i:s');
  			$row->modified_by 	= $nsInfo['member']['id'];
  			$row->hit 			= $arrParam['hit'];
  			$row->status 		= $arrParam['status'];
  			$row->cat_id		= $arrParam['cat_id'];
  			$row->order			= $arrParam['order'];
			$row->comment		= $arrParam['comment'];
			$row->district_id	= $arrParam['district_id'];
			$row->ward_id		= $arrParam['ward_id'];
			$row->contact		= $arrParam['contact'];
			$row->start_date	= $arrParam['start_date'];
			$row->end_date		= $arrParam['end_date'];
            $row->start_date_isbusiness	= $arrParam['start_date_isbusiness'];
			$row->end_date_isbusiness	= $arrParam['end_date_isbusiness'];
            $row->isbusiness	= $arrParam['isbusiness'];
            $row->start_date_isfront	= $arrParam['start_date_isfront'];
			$row->end_date_isfront		= $arrParam['end_date_isfront'];
            $row->start_date_ishot	    = $arrParam['start_date_ishot'];
			$row->end_date_ishot		= $arrParam['end_date_ishot'];
            $row->percent		= $arrParam['percent'];
            $row->address		= $arrParam['address'];
            $row->map_x		= $arrParam['map_x'];
            $row->map_y		= $arrParam['map_y'];
            $row->is_top		= $arrParam['is_top'];
            $row->start_date_istop		= $arrParam['start_date_istop'];
            $row->end_date_istop		= $arrParam['end_date_istop'];
            $row->thanhpho          = $locationInfo['tp'];
            $row->quan          = $locationInfo['qu'];
            
			$row->save();
            
            return $row->id;
		}
		
		elseif($options['task'] == 'admin-edit'){
		  
              
			$where = 'id = ' . $arrParam['id'];
			$row = $this->fetchRow($where);
		
			$row->name 			= $arrParam['name'];
  			$row->summary 		= $arrParam['summary'];
			$row->summary_1 	= $arrParam['summary_1'];
			$row->summary_2 	= $arrParam['summary_2'];
  			$row->description 	= $arrParam['description'];  			
  			$row->image 		= $arrParam['image'];
			$row->image1 		= $arrParam['image1'];
			$row->map_image 	= $arrParam['map_image'];
  			$row->price 		= $arrParam['price'];
  			$row->hot 			= $arrParam['hot'];
  			//$row->created 		= date('Y-m-d H:i:s');
  			//$row->created_by 	= 1;
  			$row->modified 		= date('Y-m-d H:i:s');
            
            $ns = new Zend_Session_Namespace('info');
            $nsInfo = $ns->getIterator();
  			$row->modified_by 	= $nsInfo['member']['id'];
  			$row->hit 			= $arrParam['hit'];
  			$row->status 		= $arrParam['status'];
  			$row->cat_id		= $arrParam['cat_id'];
  			$row->order			= $arrParam['order'];
			$row->comment		= $arrParam['comment'];
			
			$row->district_id	= $arrParam['district_id'];
			$row->ward_id		= $arrParam['ward_id'];
			$row->contact		= $arrParam['contact'];
			$row->start_date_isbusiness	= $arrParam['start_date_isbusiness'];
			$row->end_date_isbusiness	= $arrParam['end_date_isbusiness'];
            $row->isbusiness	= $arrParam['isbusiness'];
			$row->start_date	= $arrParam['start_date'];
			$row->end_date		= $arrParam['end_date'];
			$row->start_date_isfront	= $arrParam['start_date_isfront'];
			$row->end_date_isfront		= $arrParam['end_date_isfront'];
            $row->start_date_ishot	= $arrParam['start_date_ishot'];
			$row->end_date_ishot	= $arrParam['end_date_ishot'];
            $row->percent		= $arrParam['percent'];
            $row->address		= $arrParam['address'];
            $row->map_x		= $arrParam['map_x'];
            $row->map_y		= $arrParam['map_y'];
            $row->is_top		= $arrParam['is_top'];
            $row->start_date_istop		= $arrParam['start_date_istop'];
            $row->end_date_istop		= $arrParam['end_date_istop'];
            
            $row->thanhpho          = $locationInfo['tp'];
            $row->quan              = $locationInfo['qu'];
			$row->save();
		}
		
	}
	
	public function getItem($arrParam = null, $options = null){
		
		if($options['task'] == 'admin-info' || $options['task'] == 'admin-edit'){
			$where = 'id = ' . $arrParam['id'];
            
            //$result = $this->fetchRow($where);
            //echo '<pre>';
            //print_r($result);
            //echo '</pre>';
			//$result = $this->fetchRow($where)->toArray();
		 $result = null;
			$rs = $this->fetchRow($where);
			if($rs)
			{
				$result = $rs->toArray();
			}
		}
		
		elseif($options['task'] == 'admin-info'){
			$db = Zend_Registry::get('connectDb');
			//$db = Zend_Db::factory($adapter,$config);
			
			$sql = $db->select()
						->from($this->_name.' as n','*')
						->joinLeft('product_category as nc','n.cat_id=nc.id',array('nc.name as cat_name'))
						->where('n.id = ?', $arrParam['id'],INTEGER);
			$result = $db->fetchRow($sql);
		}
		elseif($options['task'] == 'admin-edit'){
			$db = Zend_Registry::get('connectDb');
			//$db = Zend_Db::factory($adapter,$config);
			
			$sql = $db->select()
						->from($this->_name.' as n','*')
						->joinLeft('product_category as nc','n.cat_id=nc.id',array('nc.name as cat_name'))
						->joinLeft('users as u','n.created_by=u.id',array('u.user_name as created_name'))
						->where('n.id = ?', $arrParam['id'],INTEGER);
			$result = $db->fetchRow($sql);
		}
		elseif($options['task'] == 'front-detail'){
			$db = Zend_Registry::get('connectDb');
			//$db = Zend_Db::factory($adapter,$config);
			
			$sql = $db->select()
						->from($this->_name.' as p','*')
						->joinLeft('product_category as pc','p.cat_id=pc.id',array('pc.name as cat_name'))						
						->where('p.id = ?', $arrParam['id'],INTEGER);
			$result = $db->fetchRow($sql);
		}
		
		
		
		return $result;
	}
	
	public function deleteItem($arrParam = null, $options = null){
		if($options['task'] == 'admin-delete'){
			$where = ' id = ' . $arrParam['id'];
			//xoa avatar
			$sql=$this->select()
  							->from($this->_name,array('image','image1','map_image'))
  							->where($where);
			$result = $this->fetchAll($sql)->toArray();
			foreach($result as $key => $image)
  				{
  					$uploadDir = $arrParam['controllerConfig']['<?php echo $this->imgUrl ?>Dir'];					
					@unlink($uploadDir . 'full/' . $image['image']);
					@unlink($uploadDir . 'image/' . $image['image']);
					
					@unlink($uploadDir . 'full/' . $image['image1']);
					@unlink($uploadDir . 'image1/' . $image['image1']);								
					
					@unlink($uploadDir . 'full/' . $image['map_image']);
					@unlink($uploadDir . 'map/' . $image['map_image']);								
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
  							->from($this->_name,array('image','image1','map_image'))
  							->where($where);
  				$result = $this->fetchAll($sql)->toArray();
				foreach($result as $key => $image)
  				{
  					$uploadDir = $arrParam['controllerConfig']['<?php echo $this->imgUrl ?>Dir'];					
					@unlink($uploadDir . 'full/' . $image['image']);
					@unlink($uploadDir . 'image/' . $image['image']);
					
					@unlink($uploadDir . 'full/' . $image['image1']);
					@unlink($uploadDir . 'image1/' . $image['image1']);								
					
					@unlink($uploadDir . 'full/' . $image['map_image']);
					@unlink($uploadDir . 'map/' . $image['map_image']);							
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