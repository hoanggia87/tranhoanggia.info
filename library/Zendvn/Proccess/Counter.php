<?php
class Zendvn_Proccess_Counter{
	function getRealIpAddr()
		{
			if (!empty($_SERVER['HTTP_CLIENT_IP']))  //check ip from share internet
			{
			  $ip=$_SERVER['HTTP_CLIENT_IP'];
			}
			elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   
			//to check ip is pass from proxy
			{
			  $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
			}
			else
			{
			  $ip=$_SERVER['REMOTE_ADDR'];
			}
			return $ip;
		}
	function getBrowserinfo(){
			$browserInfo=array(	'name'=>'',
								'version'=>'');
			$aBrowser=array('Firefox'=>'','MSIE'=>'','Chrome'=>'','Opera'=>'','Safari'=>'');
			$subject=$_SERVER['HTTP_USER_AGENT'];
			
			foreach($aBrowser as $key => $info)
			{
				$pattern = '#'.$key.'#imsU';
				if(preg_match($pattern, $subject, $matches))
				{
					$browserInfo['name']=$key;
					if($key=='MSIE')
					{
						$pattern_ = '#(?<=MSIE )[0-9.]+#';
						preg_match($pattern_, $subject, $matche);
						$browserInfo['version']=$matche[0];
					}
					else{
						$pattern_ = '#(?<='.$key.'/)[0-9.]+#';
						preg_match($pattern_, $subject, $matche);
						$browserInfo['version']=$matche[0];						
					}
					return $browserInfo; //array
				}
			}
			return null;
		}
	
	function getCountofWeek($day){
			$date['first'] = mktime(0,0,0,date("m"),date("d",$day)-date("w",$day),date("Y"));
			$date['last'] = mktime(0,0,0,date("m"),date("d",$day)+(6-date("w",$day)),date("Y"));
			return $date;
		}
		
	function getUserOnline(){
			/*$s_id = session_id();    // Bien s_id        
			$time = time();            // Lay thoi gian hien tai 
			$time_secs = 3;        // Thoi gian tinh bang seconds de delete & insert cai $s_id    moi
			$time_out = $time - $time_secs;    // Lay thoi gian hien tai    
			
			mysql_query("DELETE FROM useronline WHERE s_time < '$time_out'");                // Delete tat ca nhung rows trong khoang thoi gian qui dinh san
			mysql_query("DELETE FROM useronline WHERE s_id = '$s_id'");                        // Delete cai $s_id cua chinh thang nay
			mysql_query("INSERT INTO useronline (s_id, s_time) VALUES ('$s_id', '$time')");    // Delete no xong lai insert chinh no
			$user_online = mysql_num_rows(mysql_query("SELECT id FROM useronline"));        // Dem so dong trong table stats, chinh la so nguoi dang online
			return $user_online;*/
	}
		
}