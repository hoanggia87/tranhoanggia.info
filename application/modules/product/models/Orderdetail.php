<?php
class Front_Model_Orderdetail extends Zend_Db_Table{
	protected $_name = 'order_detail'; 
  	protected $_primary = array('order_id','pro_id');
  	

  	public  function saveItem($arrParam = null ,$options = null){
  		
  		if($options['task'] == 'front-add'){  			
  			
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