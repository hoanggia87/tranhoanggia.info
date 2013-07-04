<?php
class Product_Model_Productorderdetail extends Zend_Db_Table{
	protected $_name = 'product_order_detail'; 
  	protected $_primary = array('order_id','pro_id');
  	
    
    public function listItem($arrParam = null, $options = null){
        
	    $db = Zend_Registry::get('connectDb');
        
  		if($options['task'] == "admin-list"){
  			
			$select = $db->select()
						 ->from($this->_name.' AS od',array('order_id','pro_id','price','quantity'))
						 ->joinLeft('product AS pr','od.pro_id = pr.id',array('pr.id','pr.name','pr.image'))
						 ->where('od.order_id=?',$arrParam['id'],INTEGER);					
			
            $result = $db->fetchAll($select);				  	
  		}  
		return $result;
	
  	}

  	public  function saveItem($arrParam = null ,$options = null){
  		
  		if($options['task'] == 'add'){ 
  			
			if(count($arrParam)>0){
	  			foreach ($arrParam as $key=>$value){
	  				//echo  $value['pro_id'].'<br />';
	  				$row = $this->fetchNew();  			
	  			
		  			$row->order_id 	= $value['order_id'];
		  			$row->pro_id 	= $value['pro_id'];
		  			$row->price 	= $value['price'];
		  			$row->quantity	= $value['quantity'];
		  			  			
		  			$row->save(); 
		  			
	  			}
			}  			 		  			
  		}  		
  	}
}