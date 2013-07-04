<?php
class Product_Model_Product extends Zend_Db_Table{
	protected $_name = 'product';
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
		
		if($arrParam['cat_id']>0){
			//$select->where('n.cat_id = ?',$ssFilter['cat_id'],INTEGER);
            $select->where('n.cat_id IN ('.$arrParam['cat_id'].') AND n.status=1');
		}
        if($options['task']=='search'){
            
            $where = $db->quoteInto('match(p.name,p.summary) against(?)',$arrParam['k']);
            //$select->where($where); 
            
            $col1 = $db->quoteIdentifier('p.name');
            $where1 = $db->quoteInto("$col1 LIKE ?", '%'.$arrParam['k'].'%');
            $col2 = $db->quoteIdentifier('p.summary');
            $where2 = $db->quoteInto("$col2 LIKE ?", '%'.$arrParam['k'].'%');            
            
            $select->where($where .' OR '. $where1 .' OR '. $where2);
        
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
						 ->from($this->_name.' AS n',array('id','name','status','order','created','image','price'))
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
        elseif($options['task'] == 'list-front'){//liet ke danh sách các s?n ph?m c?a doanh nghi?p
            $arrayField=array('id','name','image','summary','price');
            $select = $db->select()
						 ->from($this->_name.' AS p',$arrayField)     
                         ->joinLeft($this->_name.'_category AS nc','nc.id = p.cat_id',array('nc.name as cat_name','nc.id as cat_id'))
                         ->joinLeft($this->_name.'_category AS nc1','nc1.id = nc.parents',array('nc1.name as cat_name_1','nc1.id as cat_id_1'))  
                         ->where('p.status=1') 
                         ->order('p.order ASC')                   
						 ->order('p.id DESC');      
            /*
            if($paginator['itemCountPerPage']>0){
				$page = $paginator['currentPage'];
				$rowCount = $paginator['itemCountPerPage'];
				$select->limitPage($page,$rowCount);
			}  */
            //  echo                $select;
			$result  = $db->fetchAll($select);
        }
        elseif($options['task'] == 'list-newest'){//liet ke danh sách các s?n ph?m c?a doanh nghi?p
            if(!isset($options['limit']))
            {
                $options['limit']=5;
            }
            $arrayField=array('id','name','image','summary','price');
            $select = $db->select()
						 ->from($this->_name.' AS p',$arrayField)
                         ->joinLeft($this->_name.'_category AS nc','nc.id = p.cat_id',array('nc.name as cat_name','nc.id as cat_id'))
                         ->joinLeft($this->_name.'_category AS nc1','nc1.id = nc.parents',array('nc1.name as cat_name_1','nc1.id as cat_id_1'))    
                         ->where('p.status=1') 
						->order('p.order ASC') 						 
						 ->order('p.id DESC');
                         $select->limit($options['limit'],0);
            
            
			$result  = $db->fetchAll($select);
            if($options['local']=='index')
            {
                $newArray=array();
                
                $objView=$options['objView'];
                foreach($result as $key=>$info)
                {
                    $newArray[$key]['name']=$info['name'];
                    
                    $rw=new Zendvn_View_Helper_CmsRewriteLink();
                    $info['name']=$rw->noSign($info['name']);
                    $info['cat_name']=$rw->noSign($info['cat_name']);
                    $info['cat_name_1']=$rw->noSign($info['cat_name_1']);                    
                    $newArray[$key]['link']=$objView->serverUrl($objView->url($info,'product-detail-index-1'));                    
                    $newArray[$key]['image']=PUBLIC_URL.'/files/products/image/'.$info['image'];
                    $newArray[$key]['summary']=$info['summary'];
                }
                $result=$newArray;
            }
        }
		elseif($options['task'] == 'list-product-hot'){//liet ke danh sách các s?n ph?m hot
            $arrayField=array('id','name','image','summary','price');
            $select = $db->select()
						 ->from($this->_name.' AS p',$arrayField)   
                         //->where('p.hot = ?',$options['hot'],INTEGER)
                         ->joinLeft($this->_name.'_category AS nc','nc.id = p.cat_id',array('nc.name as cat_name','nc.id as cat_id'))  
                         ->joinLeft($this->_name.'_category AS nc1','nc1.id = nc.parents',array('nc1.name as cat_name_1','nc1.id as cat_id_1'))                           
                         ->where('p.status = 1')
                         ->where('p.hot = 1')
                         ->order('p.order ASC')        
						 ->order('p.id DESC');
           $limit=5;
           if($options['limit'])
                $limit=$options['limit'];
           
           $select->limit($limit,0);
            
			$result  = $db->fetchAll($select);
        }
        elseif($options['task'] == 'list-product-the-same'){//liet ke danh sách các s?n ph?m lien quan
            $arrayField=array('id','name','image','summary','price');
            $select = $db->select()
             ->from($this->_name.' AS p',$arrayField)   
                         //->where('p.hot = ?',$options['hot'],INTEGER)
                         ->joinLeft($this->_name.'_category AS nc','nc.id = p.cat_id',array('nc.name as cat_name','nc.id as cat_id'))                           
                         ->where('p.status = 1')                         
                         ->where('p.cat_id = ?',$arrParam['cat_id'],INTEGER)
                         ->where('p.id < ?',$arrParam['id'],INTEGER)
                         ->order('p.order ASC')        
             ->order('p.id DESC');
           $limit=3;

           if($options['limit'])
                $limit=$options['limit'];
           
           $select->limit($limit,0);
            
      $result  = $db->fetchAll($select);
        }
        elseif($options['task'] == 'list-by-cat'){//liet ke danh sách các s?n ph?m c?a doanh nghi?p
            $arrayField=array('id','name','image','summary','price');
            $select = $db->select()
						 ->from($this->_name.' AS p',$arrayField)   
                         ->joinLeft($this->_name.'_category AS nc','nc.id = p.cat_id',array('nc.name as cat_name','nc.id as cat_id'))                          
                         //->where('p.cat_id = ?',$arrParam['cat_id'],INTEGER)
                         ->where('p.cat_id IN ('.$arrParam['cat_id'].')')
                         ->where('p.status = 1')
                         ->order('p.order ASC')        
						 ->order('p.id DESC');
                         //$select->limit(5,0);
           // echo '|-'.$paginator['itemCountPerPage'].'-|';
            if($paginator['itemCountPerPage']>0){
      				$page = $paginator['currentPage'];
      				$rowCount = $paginator['itemCountPerPage'];
      				$select->limitPage($page,$rowCount);
                
               //$result  = $db->fetchAll($select);
      			}
            //echo $select;  
  			   $result  = $db->fetchAll($select);
        }
		elseif($options['task'] == 'search'){//liet ke danh sách các s?n ph?m c?a doanh nghi?p
            $arrayField=array('id','name','image','summary','price');
            $select = $db->select()
						 ->from($this->_name.' AS p',$arrayField)   
                 //        ->where('p.name LIKE \'%?%\'',$arrParam['k'],STRING)
                  //       ->orwhere('p.summary LIKE \'%?%\'',$arrParam['k'],STRING)
                         ->where('p.status = 1')
                         ->order('p.order ASC')        
						 ->order('p.id DESC');
                         
            
             
            
            
            $where = $db->quoteInto('match(p.name,p.summary) against(?)',$arrParam['k']);
            //$select->where($where); 
            
            $col1 = $db->quoteIdentifier('p.name');
            $where1 = $db->quoteInto("$col1 LIKE ?", '%'.$arrParam['k'].'%');
            $col2 = $db->quoteIdentifier('p.summary');
            $where2 = $db->quoteInto("$col2 LIKE ?", '%'.$arrParam['k'].'%');            
            
            $select->where($where .' OR '. $where1 .' OR '. $where2);
            
            
            if($paginator['itemCountPerPage']>0){
				$page = $paginator['currentPage'];
				$rowCount = $paginator['itemCountPerPage'];
				$select->limitPage($page,$rowCount);
			}
            
			$result  = $db->fetchAll($select);
        }
        elseif($options['task'] == 'list-newest-each-category'){
            $objCategory=new Product_Model_Productcategory;
              
            if($options['cat_parent'])
            {

              $listCat=$objCategory->listItem($arrParam,array('task'=>'cms-list','parent'=>$options['cat_parent'],'inlude_parent'=>1));  
            }
            else
            {
              $listCat=$objCategory->listItem($arrParam,array('task'=>'cms-list'));  
            }  
           
            $arrayFCat=array();
            $arrayFCat1=array();
            
            
            $arrayField='id,name,image,summary,cat_id,price';
            $select='';
            foreach($listCat as $key=>$info)
            {
              
                $select .= '(SELECT '.$arrayField.' FROM '.$this->_name.' WHERE cat_id IN ('.$info['id'].') AND status=1 ORDER BY id DESC LIMIT 0,8) UNION ALL ';
            }
            $select='SELECT * FROM ('.substr($select,0,-11).') tbl';
            $resultPro  = $db->fetchAll($select);
            
           
            $arrFullProduct=array();
            foreach($listCat as $key=>$info)
            {

                $arrFullProduct[$info['id']]['id']=$info['id'];
                $arrFullProduct[$info['id']]['name']=$info['name'];
                foreach ($resultPro as $key1 => $value) 
                {


                  if($value['cat_id']==$info['id'])
                  {
                    $arrFullProduct[$info['id']]['childPro'][$value['id']]['id']=$value['id'];  
                    $arrFullProduct[$info['id']]['childPro'][$value['id']]['name']=$value['name'];
                    $arrFullProduct[$info['id']]['childPro'][$value['id']]['summary']=$value['summary']; 
                    $arrFullProduct[$info['id']]['childPro'][$value['id']]['image']=$value['image'];  
                    $arrFullProduct[$info['id']]['childPro'][$value['id']]['price']=$value['price'];
                    $arrFullProduct[$info['id']]['childPro'][$value['id']]['cat_id']=$info['id'];
                    $arrFullProduct[$info['id']]['childPro'][$value['id']]['cat_name']=$info['name'];
                 
                  }
                }
                
            }

            
            $result=$arrFullProduct;            
        }
        elseif($options['task'] == 'list-by-arrkey'){
            $arrayField=array('id','price','name','summary','image','cat_id');
            $listID=implode(',',$arrParam);
            $select = $db->select()
						 ->from($this->_name,$arrayField)                   
                         ->where('`id` IN ('.$listID.')');
            $result  = $db->fetchAll($select);
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
            $row->summary_3 	= $arrParam['summary_3'];
			$row->summary_4 	= $arrParam['summary_4'];
            $row->summary_5 	= $arrParam['summary_5'];
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
            $row->made_uid          = $arrParam['made_uid'];
            $row->quantity=$arrParam['quantity'];
			$row->save();
                //clear cache
                $cache=Zendvn_Cache::getCacheObject('cachedataindex');  
                $cache->remove('controller_cache_list_newest_each_category');
                $cache->remove('view_cache_list_newest_each_category');                
                
                $totalItem  = $this->countItem(array('cat_id'=>$arrParam['cat_id']),array('task'=>'list-item-by-category'));
                $numberPages=ceil($totalItem/24);
                $cache1=Zendvn_Cache::getCacheObject('productindexlist');
                $cache2=Zendvn_Cache::getCacheObject('cachedatalistbycategory');
                $cache1->remove('controller_cachedatalistbycategory_count_'.$arrParam['cat_id']);             
                for ($i=0;$i<=$numberPages;$i++) {
                    $cache1->remove('view_cachedatalistbycategory_'.$arrParam['cat_id'].'_'.$i);
                    $cache2->remove('controller_cachedatalistbycategory_'.$arrParam['cat_id'].'_'.$i);
                }

            return $row->id;
		}
		
		elseif($options['task'] == 'admin-edit'){
		  
              
			$where = 'id = ' . $arrParam['id'];
            
			$row = $this->fetchRow($where);
		
			$row->name 			= $arrParam['name'];
  			$row->summary 		= $arrParam['summary'];
			$row->summary_1 	= $arrParam['summary_1'];
			$row->summary_2 	= $arrParam['summary_2'];
            $row->summary_3 	= $arrParam['summary_3'];
			$row->summary_4 	= $arrParam['summary_4'];
            $row->summary_5 	= $arrParam['summary_5'];
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
            $row->made_uid          = $arrParam['made_uid'];
            $row->quantity=$arrParam['quantity'];


                //clear cache
                $cache=Zendvn_Cache::getCacheObject('cachedataindex');  
                $cache->remove('controller_cache_list_newest_each_category');
                $cache->remove('view_cache_list_newest_each_category');                
                $cache=Zendvn_Cache::getCacheObject('productdetailindex');              
                $cache->remove('view_product_detail_'.$arrParam['id']);
                $cache=Zendvn_Cache::getCacheObject('productdetailparam');
                $cache->remove('controller_cache_product_detail_'.$arrParam['id']);//xóa thông tin detail
                $cache->remove('controller_cache_product_order_'.$arrParam['id']);//xóa order product

                $totalItem  = $this->countItem(array('cat_id'=>$arrParam['cat_id']),array('task'=>'list-item-by-category'));
                $numberPages=ceil($totalItem/24);
                $cache1=Zendvn_Cache::getCacheObject('productindexlist');
                $cache2=Zendvn_Cache::getCacheObject('cachedatalistbycategory');
                $cache1->remove('controller_cachedatalistbycategory_count_'.$arrParam['cat_id']);             
                for ($i=0;$i<=$numberPages;$i++) {
                    $cache1->remove('view_cachedatalistbycategory_'.$arrParam['cat_id'].'_'.$i);
                    $cache2->remove('controller_cachedatalistbycategory_'.$arrParam['cat_id'].'_'.$i);
                }


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
						->joinLeft($this->_name.'_category as nc','n.cat_id=nc.id',array('nc.name as cat_name'))
						->where('n.id = ?', $arrParam['id'],INTEGER);
			$result = $db->fetchRow($sql);
		}
		elseif($options['task'] == 'admin-edit'){
			$db = Zend_Registry::get('connectDb');
			//$db = Zend_Db::factory($adapter,$config);
			
			$sql = $db->select()
						->from($this->_name.' as n','*')
						->joinLeft($this->_name.'_category as nc','n.cat_id=nc.id',array('nc.name as cat_name'))
						->joinLeft('users as u','n.created_by=u.id',array('u.user_name as created_name'))
						->where('n.id = ?', $arrParam['id'],INTEGER);
			$result = $db->fetchRow($sql);
		}
		elseif($options['task'] == 'front-detail'){
			$db = Zend_Registry::get('connectDb');
			//$db = Zend_Db::factory($adapter,$config);
			
			$sql = $db->select()
						->from($this->_name.' as p','*')												
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
            //xoa cache
            $cache=Zendvn_Cache::getCacheObject('cachedataindex');  
            $cache->remove('controller_cache_list_newest_each_category');
            $cache->remove('view_cache_list_newest_each_category');                
            $cache=Zendvn_Cache::getCacheObject('productdetailindex');              
            $cache->remove('view_product_detail_'.$result['id']);
            $cache=Zendvn_Cache::getCacheObject('productdetailparam');
            $cache->remove('controller_cache_product_detail_'.$result['id']);//xóa thông tin detail
            $cache->remove('controller_cache_product_order_'.$result['id']);//xóa order product

            $totalItem  = $this->countItem(array('cat_id'=>$result['cat_id']),array('task'=>'list-item-by-category'));
            $numberPages=ceil($totalItem/24);
            $cache1=Zendvn_Cache::getCacheObject('productindexlist');
            $cache2=Zendvn_Cache::getCacheObject('cachedatalistbycategory');
            $cache1->remove('controller_cachedatalistbycategory_count_'.$result['cat_id']);             
            for ($i=0;$i<=$numberPages;$i++) {
                $cache1->remove('view_cachedatalistbycategory_'.$result['cat_id'].'_'.$i);
                $cache2->remove('controller_cachedatalistbycategory_'.$result['cat_id'].'_'.$i);
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