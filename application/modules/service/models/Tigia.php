<?php
class Service_Model_Tigia extends Zend_Db_Table
{
    public function getTigiavang()
    {
        $client = new Zend_Http_Client();

        $arrInfo=array();
        $url = 'http://sacombank-sbj.com/AJAX.aspx?func=getgoldprice&date=&type=0';
        $client->setUri($url);
        $client->setConfig(array(
          'maxredirects' => 2,
          'timeout'      => 30,   
       
        ));
                
        $client->setHeaders(array(
          'Host'    => 'sacombank-sbj.com'
        
        ));
        $respone = $client->request('GET')->getBody();
        $document=Zendvn_Dom_Process::newDocumentHTML($respone);
        $arrInfo['vang']['trongnuoc'] = trim(pq('.divPriceTable')->html());
        $arrInfo['vang']['quocte'] = '<iframe id="frmTygia2" width="100%" height="100%" frameborder="0" style="background:#FFFFFF;" scrolling="no" src="http://classic.easy-forex.com/en/forex.quotesbox.aspx"></iframe>';
        //Lay gia vang quoc te
        $url = 'http://sacombank-sbj.com/kinh-doanh-vang/35-h-1.aspx';
        $client->setUri($url);
        $client->setConfig(array(
          'maxredirects' => 2,
          'timeout'      => 30,   
       
        ));
                
        $client->setHeaders(array(
          'Host'    => 'sacombank-sbj.com'
        ));
        $respone = $client->request('GET')->getBody();
        $document=Zendvn_Dom_Process::newDocumentHTML($respone);
        $arrInfo['bieudo']['trongnuoc'] = 'http://sacombank-sbj.com'.trim(pq('.vangtrongnuoc')->find('img')->attr('src'));
        $arrInfo['bieudo']['quocte'] = trim(pq('.vangthegioi')->find('img')->attr('src'));
        return $arrInfo;
        /*echo "<pre>";
        var_dump($arrInfo);
        echo "</pre>";*/ 
    }
    public function getTigiaUSD()
    {
        return '<iframe id="frmTygia2" width="100%" height="100%" frameborder="0" style="background:#FFFFFF;" scrolling="no" src="http://www.eximbank.com.vn/WebsiteExrate2012/ExchangeRate_vn_2012.aspx"></iframe>';
    }
    public function getNews(){
    $link='http://news.allmobile.vn/d2/d754/list.html';

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
}