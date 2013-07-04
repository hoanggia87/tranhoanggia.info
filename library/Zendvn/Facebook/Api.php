<?php
    //require_once (PUBLIC_PATH . '/scripts/facebookapi/base_facebook.php');
    require_once ('Facebook.php');

    class Zendvn_Facebook_Api extends facebook
    {
    	protected $level=array(
    			1=>10,
    			2=>20,
    			3=>30,
    			4=>40,
    			5=>50,
    			6=>60,
    			7=>70,
    			8=>80,
    			9=>90,
    			10=>100,
    			11=>110,
    			12=>120,
    			13=>130,
    			14=>140,
    			15=>150,
    			16=>160,
    			17=>170,
    			18=>180,
    			19=>190,
    			20=>200,
    			21=>210,
    			22=>220,
    			23=>230,
    			240=>240
    		);
	    /**
	    * Identical to the parent constructor, except that
	    * we start a PHP session to store the user ID and
	    * access token if during the course of execution
	    * we discover them.
	    *
	    * @param Array $config the application configuration.
	    * @see BaseFacebook::__construct in facebook.php
	    */
	    public function __construct($config) 
	    {
		    if (!session_id()) 
		    {
		      	session_start();
		    }
		    parent::__construct($config);
		  	
		}

		public function getListPage()
		{
			$strFQL='SELECT page_id, type from page_admin WHERE uid=me()';
			return $this->selectFQL($strFQL);
		}
		public function getListFriend($arrParam=null,$options=null)
		{
			$limit='';
			if($options['limit'])
			{
				$limit=$options['limit'];
			}
			$strFQL='SELECT uid2 FROM friend WHERE uid1=me() ORDER BY rand() '.$limit ;
			$rs=$this->selectFQL($strFQL);
			$arrF=array();
			foreach ($rs as $key => $value) {
                  $arrF[]=$value['uid2'];
            }
            return $arrF;
		}
		
		public function getListUserIDLiked($objID='10200251264505359')
		{			

			$strFQL='SELECT uid,username,profile_url FROM user where uid IN (SELECT user_id FROM like WHERE object_id=\''.$objID.'\' limit 0,3000)';
			
			return $rs=$this->selectFQL($strFQL);
			$arrF=array();
			foreach ($rs as $key => $value) {
                  $arrF[]=$value['user_id'];
            }
            return $arrF;

		}
		public function getUserInfo($arrUserID)
		{			

			$listID='';
			if(is_array($arrUserID))
			{
				foreach ($arrUserID as $key => $value) {
                  $listID.='\''.$value.'\',';
            	}
            	$listID=substr($listID, 0, -1);
			}
			else
			{
				$listID=$arrUserID;
			}
            

            

			$strFQL='SELECT uid,username,profile_url FROM user where uid IN ('.$listID.')';
			
			$rs=$this->selectFQL($strFQL);

			$arrF=array();
			foreach ($rs as $key => $value) {
                  $arrF[$value['uid']]=$value;
            }
            
            return $arrF;

		}
		public function getListFriendAndFan($pageID='130958636927179')
		{			
			/*$arrFriend=$this->getListFriend();

			$listFriend='';
            foreach ($arrFriend as $key => $value) {
                  $listFriend.='\''.$value.'\',';
            }

            $listFriend=substr($listFriend, 0, -1);*/
			//$strFQL='SELECT uid FROM page_fan WHERE uid IN ('.$listFriend.') AND page_id=\''.$pageID.'\'';
            $strFQL='SELECT uid,username,profile_url FROM user where uid IN ( SELECT uid FROM page_fan WHERE uid IN (SELECT uid2 FROM friend WHERE uid1=me()) AND page_id=\''.$pageID.'\')';
            $list = $this->selectFQL($strFQL);
           
            return $list;
            
		}
		
		
		public function getAvatars($arrID,$type=1)
		{			
			if($arrID)
			{
				$listFriendFan='';
				if(is_array($arrID))
				{
					foreach ($arrID as $key => $value) 
					{
		                $listFriendFan.='\''.$value.'\',';
		            }
		            $listFriendFan=substr($listFriendFan, 0, -1);	
				}
				else
				{
					$listFriendFan=$arrID;
				}
	            

				$strFQL='SELECT id, width, height, url, is_silhouette, real_width, real_height FROM profile_pic WHERE id IN ('.$listFriendFan.')';
				switch ($type) {
					case 2:
						$strFQL.=' AND width=121';
						break;
					case 3:
						$strFQL.=' AND width=155 AND height=50';
						break;
					default:
						break;
				}				
            	$rs= $this->selectFQL($strFQL);
            	$arrnew=array();
            	foreach ($rs as $key => $value) {
            		$arrnew[$value['id']]=$value;
            	}
            	return $arrnew;
			}
		}

		public function getLevel($countFriendFan)
		{
			
			foreach ($this->level as $key => $value) 
			{
				if($countFriendFan<$value)
				{
					return $key;
				}
			};

		}

		public function selectFQL($strFQL)
		{
			$access_token = parent::getAccessToken();
			$strFQL=$this->strFQL($strFQL);          	
          	// Lay danh sach ban
          	$fql_query_url = 'https://graph.facebook.com/'
            	. 'fql?q='.$strFQL
            	. '&access_token=' . $access_token;
          	$fql_query_result = file_get_contents($fql_query_url);
          	
          	//$fql_query_obj = json_decode($fql_query_result, true);

          	$fql_query_obj = json_decode(preg_replace('/"uid":(\d+)/', '"uid":"$1"', $fql_query_result),true);
          	//$array = json_decode(json_encode($fql_query_obj), true);
          	//foreach ($fql_query_obj['data'] as $key => $value) {
          	//	$fql_query_obj['data'][$key]['uid']=json_decode(json_encode($value['uid']));
          	//}
          	return $fql_query_obj['data'];
		}
		public function strFQL($strFQL)
		{
			return str_replace(' ', '+', $strFQL);
		}
    }
    
    

?>