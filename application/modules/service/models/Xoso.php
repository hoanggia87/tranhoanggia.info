<?php
class Service_Model_Xoso extends Zend_Db_Table
{
    var $arrCity = array(
                1 => 'Hồ Chí Minh',
                5 => 'Vũng Tàu',
                9 => 'An Giang',
                11 => 'Kiên Giang',
                13 => 'Long An',
                21 => 'Sóc Trăng',
                22 => 'Tây Ninh',
                23 => 'Bến Tre',
                24 => 'Bình Dương',
                26 => 'Bình Phước',
                27 => 'Bình Thuận',
                28 => 'Cà Mau',
                29 => 'Cần Thơ',
                31 => 'Đồng Nai',
                32 => 'Đồng Tháp',
                34 => 'Vĩnh Long',
                35 => 'Đà Lạt',
                36 => 'Hậu Giang',
                38 => 'Bạc Liêu',
                39 => 'Tiền Giang',
                40 => 'Trà Vinh',
                3 => 'Thừa Thiên Huế',
                6 => 'Đà Nẵng',
                10 => 'Khánh Hòa',
                12 => 'Kon Tum',
                14 => 'Ninh Thuận',
                16 => 'Phú Yên',
                17 => 'Quảng Bình',
                18 => 'Quảng Nam',
                19 => 'Quảng Ngãi',
                25 => 'Bình Định',
                30 => 'Daklak',
                33 => 'Gia Lai',
                37 => 'Đắc Nông',
                42 => 'Quảng Trị',
                1000 => 'Miền Bắc'
                );
    public function getListLocation()
    {
        return $this->arrCity;
    }
    public function getXoso()
    {
        $client = new Zend_Http_Client();

        $arrInfo=array();
        foreach ($this->arrCity as $k => $v) 
        {
            if($k!=1000)
            {
                $url = 'http://kqxs.123sms.vn/KetQua.aspx?id=1';
            }
            else
            {
                $url = 'http://kqxs.123sms.vn/KetQuaMB.aspx';
            }
            $client->setUri($url);
            $client->setConfig(array(
              'maxredirects' => 2,
              'timeout'      => 30,   
           
            ));
                    
            $client->setHeaders(array(
              'Host'    => 'kqxs.123sms.vn'
            
            ));
            $respone = $client->request('GET')->getBody();
            $document=Zendvn_Dom_Process::newDocumentHTML($respone);
            $arrInfo[$k]=array(
                           trim(pq('.ta1:eq(0)')->parents('td:eq(0)')->html()),
                           trim(pq('.ta1:eq(1)')->parents('td:eq(0)')->html()),
                           trim(pq('.ta1:eq(2)')->parents('td:eq(0)')->html()),
                           trim(pq('.ta1:eq(3)')->parents('td:eq(0)')->html()),
                           trim(pq('.ta1:eq(4)')->parents('td:eq(0)')->html()),
                           trim(pq('.ta1:eq(5)')->parents('td:eq(0)')->html()),
                           trim(pq('.ta1:eq(6)')->parents('td:eq(0)')->html()),
                           );
        }
        return $arrInfo;
        /*echo "<pre>";
        var_dump($arrInfo);
        echo "</pre>";*/ 
    }
    public function getNews()
    {
        $MaxNews = 10;
        $arrNews = array();
        $client = new Zend_Http_Client();
        $client->setUri('http://xoso.com.vn/default.aspx?tabid=439');
        $client->setConfig(array(
          'maxredirects' => 2,
          'timeout'      => 30,   
       
        ));        
        $client->setHeaders(array(
          'Host'    => 'xoso.com.vn'
        
        ));
        $response = $client->request('GET')->getBody();
        $response = trim ( $response );
		
    	$response = self::rmBOM ( $response );
    	$response = preg_replace ( array ('/(?=\<\!DOCTYPE).+?(?<=>)/', '/(?=\<html).+?(?<=>)/','/<\/html>(\r\n)?(.*)/','/(?=\<head).+?(?<=>)/' ), array ('', '<html>','</html>','<head><meta http-equiv="Content-Type" content="text\/html; charset=UTF-8" \/>' ), $response );
    	
    	
    	$response = trim($response);
        $document=Zendvn_Dom_Process::newDocumentHTML($response);
        $arrNews[0]['link'] = 'http://xoso.com.vn/'.trim(pq('.HotNewsTitle:eq('.$i.')')->attr('href'));
        $arrNews[0]['content'] = trim(pq('.HotNewsTitle:eq('.$i.')')->html());
        for($i=0;$i<$MaxNews-1;$i++)
        {
            //$arrNews[$i] = trim(pq('.tieude_tintuc:eq('.$i.')')->htmlOuter());
            $arrNews[$i+1]['link'] = 'http://xoso.com.vn/'.trim(pq('.NewsSummary_Title:eq('.$i.')')->attr('href'));
            $arrNews[$i+1]['content'] = trim(pq('.NewsSummary_Title:eq('.$i.')')->html());
            //echo $arrNews[$i],'<br>';
        }
        //NewsSummary_Title
        /*echo "<pre>";
        var_dump($arrNews);
        echo "</pre>";*/
        return $arrNews;
    }
    public function rmBOM($string) {
		if (substr ( $string, 0, 3 ) == pack ( "CCC", 0xef, 0xbb, 0xbf )) {
			$string = substr ( $string, 3 );
		}
		return $string;
	}
}