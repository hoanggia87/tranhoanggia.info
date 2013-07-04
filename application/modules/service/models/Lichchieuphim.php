<?php
class Service_Model_Lichchieuphim extends Zend_Db_Table
{
    var $arrListLocation = array('ha-noi' => 'Hà Nội','tphcm' => 'Hồ Chí Minh','hai-phong' => 'Hải Phòng','da-nang' => 'Đà Nẵng');
    public function getListLocation()
    {
        return $this -> arrListLocation;
    }
	public function getLichchieuphim($arrParam)
    {
        $client = new Zend_Http_Client();
        $arrLocation = $this->arrListLocation;
        foreach($arrLocation as $key => $value)
        {
            $client->setUri('http://vietbao.vn/vn/lich-chieu-phim/'.$key.'/ngay-'.$arrParam['date']);
            $client->setConfig(array(
              'maxredirects' => 2,
              'timeout'      => 30,   
           
            ));
            $client->setHeaders(array(
                  'Host'    => 'vietbao.vn'
                
                ));
            $respone = $client->request('GET')->getBody();
            $document=Zendvn_Dom_Process::newDocumentHTML($respone);
            pq('.form-update:first')->empty();
            $arrResult[$key] = trim(pq('.presentation')->htmlOuter());
        }
        return $arrResult;
        /*echo '<pre>';
        var_dump($arrResult);
        echo '</pre>';*/
    }
    public function getNews(){
    $link='http://news.allmobile.vn/d2/db/list.html';

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