<?php
class Service_Model_Bongda extends Zend_Db_Table{
  var $func=array(
      1=>'Kết quả',
      2=>'Lịch thi đấu',
      3=>'Bảng xếp hạng'
    );
  var $arrNation=array(
      1=>'ANH',
      2=>'TÂY BAN NHA',
      3=>'ITALIA',
      4=>'ĐỨC',
      5=>'PHÁP'
    );
	var $arrNationFunc=array(
      1=>array(
          1=>'http://thethao.vnexpress.net/lich-thi-dau/chitiet/ngay/{0}/lid/1/gmt/7/g/quoc-te',
          2=>'http://thethao.tinmoi.vn/P/ty-le-anh.html',
          3=>'http://thethao.vnexpress.net/bang-xep-hang/ngoai-hang-anh-2012-2013-1-127.html'
        ),
      2=>array(
          1=>'http://thethao.vnexpress.net/lich-thi-dau/chitiet/ngay/{0}/lid/2/gmt/7/g/quoc-te',
          2=>'http://thethao.tinmoi.vn/P/ty-le-tbn.html',
          3=>'http://thethao.vnexpress.net/bang-xep-hang/la-liga-2012-2013-2-128.html'
        ),
      3=>array(
          1=>'http://thethao.vnexpress.net/lich-thi-dau/chitiet/ngay/{0}/lid/3/gmt/7/g/quoc-te',
          2=>'http://thethao.tinmoi.vn/P/ty-le-italia.html',
          3=>'http://thethao.vnexpress.net/bang-xep-hang/serie-a-2012-2013-3-129.html'
        ),
      4=>array(
          1=>'http://thethao.vnexpress.net/lich-thi-dau/chitiet/ngay/{0}/lid/4/gmt/7/g/quoc-te',
          2=>'http://thethao.tinmoi.vn/P/ty-le-duc.html',
          3=>'http://thethao.vnexpress.net/bang-xep-hang/bundesliga-2012-2013-4-130.html'
        ),
      5=>array(
          1=>'http://thethao.vnexpress.net/lich-thi-dau/chitiet/ngay/{0}/lid/5/gmt/7/g/quoc-te',
          2=>'http://thethao.tinmoi.vn/P/ty-le-phap.html',
          3=>'http://thethao.vnexpress.net/bang-xep-hang/ligue-1-2012-2013-5-131.html'
        ) 
  );
  
    public function getCountry()
    {
        return $this->arrNation;
    }
	public function getBXH($arrParam = null, $options = null)
    {
        $arrContent = array();
        foreach($this->arrNation as $key => $val)
        {
            $idQG=$key;
            $idFT=3;
            $link=$this->arrNationFunc[$idQG][$idFT];
        
      		$client = new Zend_Http_Client();
            $client->setUri($link);
            $client->setConfig(array(
              //'maxredirects' => 2,
              //'timeout'      => 30,   
           
            ));        
            $client->setHeaders(array(
              'Host'    => 'thethao.vnexpress.net'
            
            ));
            $respone = $client->request('GET')->getBody();    
            
            $document=Zendvn_Dom_Process::newDocumentHTML($respone,'utf-8');
            $arrContent[$idQG]=pq('.content_bxh')->html();
        }

    return $arrContent;
		
	}
	public function getLTD($arrParam = null, $options = null)
    {
        $arrConent = '';
        foreach($this->arrNation as $key => $val)
        {
            $idQG=$key;
            $idFT=1;
            /*if($arrParam['m']<10)
            {
             $arrParam['m']='0'.$arrParam['m'];
            }
            if($arrParam['d']<10)
            {
             $arrParam['d']='0'.$arrParam['d'];
            }*/
            $link=$this->prepareQuery($this->arrNationFunc[$idQG][$idFT],array($arrParam['date']));
        
            $client = new Zend_Http_Client();
            $client->setUri($link);
            $client->setConfig(array(
              'maxredirects' => 2,
              'timeout'      => 30,   
           
            ));        
            $client->setHeaders(array(
              'Host'    => 'thethao.vnexpress.net'
            
            ));
            $respone = $client->request('GET')->getBody();    
            $respone=json_decode($respone,true);
            /*echo "<pre>";
            var_dump($respone);
            echo "</pre>";*/
            $document=Zendvn_Dom_Process::newDocumentHTML($respone['html']);
        
            pq('.txt_link_thongtin_capdau')->empty();
            $arrConent[$key]=pq('.content_block:eq(0)')->html();
            //echo $arrConent[$key];
        }


    return $arrConent;
    
  }
  public function getTLC(){
    $link='http://w2.ketqua-tructuyen.com/ty-le-ca-cuoc-bong-da.aspx';
    $client = new Zend_Http_Client();
    $client->setUri($link);
    $client->setConfig(array(
      'maxredirects' => 2,
      'timeout'      => 30,   
   
    ));        
    $client->setHeaders(array(
      'Host'    => 'w2.ketqua-tructuyen.com'
    
    ));
    $response = $client->request('GET')->getBody(); 
    $response = trim ( $response );
		
	$response = self::rmBOM ( $response );
	$response = preg_replace ( array ('/(?=\<\!DOCTYPE).+?(?<=>)/', '/(?=\<html).+?(?<=>)/','/<\/html>(\r\n)?(.*)/','/(?=\<head).+?(?<=>)/' ), array ('', '<html>','</html>','<head><meta http-equiv="Content-Type" content="text\/html; charset=UTF-8" \/>' ), $response );
	
	
	$response = trim($response);
    //echo $response;
    //return $response;
       
    $document=Zendvn_Dom_Process::newDocumentHTML($response);

    $content=pq('#ctl00_ContentPlaceHolder1_Label1')->html();
    
    return $content;
    
  }
  public function getNews(){
    $link='http://news.allmobile.vn/d2/d1/list.html';

    $client = new Zend_Http_Client();
    $client->setUri($link);
    $client->setConfig(array(
      'maxredirects' => 2,
      'timeout'      => 30,   
   
    ));        
    $client->setHeaders(array(
      'Host'    => 'news.allmobile.vn'
    
    ));
    $respone = $client->request('GET')->getBody();
    return $respone;
    
  }
  public function prepareQuery($strQuery, $arrArguments)
  {
    $intCount = 0;

    foreach($arrArguments as $strArgument)
    {
      $strQuery = str_replace('{' . $intCount++ . '}', ($strArgument), $strQuery);
    }

    return $strQuery;
  }
  public function rmBOM($string) {
		if (substr ( $string, 0, 3 ) == pack ( "CCC", 0xef, 0xbb, 0xbf )) {
			$string = substr ( $string, 3 );
		}
		return $string;
	}
}