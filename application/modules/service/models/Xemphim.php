<?php
class Service_Model_Xemphim extends Zend_Db_Table{
  var $listInfo=array(
      'ho-chi-minh'=>array('Hồ Chí Minh',array(
          'MegaStar Pandora',
          'MegaStar Crescent Mall',
          'MegaStar C.T Plaza',
          'MegaStar Hùng Vương',
          'MegaStar Pakson Paragon',
          'Lotte Nam Sài Gòn',
          'Lotte Diamond',
          'Galaxy Nguyễn Du',
          'Galaxy Nguyễn Trãi',
          'Galaxy Tân Bình',
          'BHD Star Maximark',
          'BHD Star ICON 68',
          'Cinebox Lý Chính Thắng',
          'Cinebox Hòa Bình',
          'Đống Đa Cinema'
        )),
      'ha-noi'=>array('Hà Nội',array(
          'MegaStar Vincom City Tower',
          'MegaStar MIPEC Tower',
          'Lotte  Landmark',
          'Lotte Hà Đông',
          'Platinum Garden Mall',
          'Platinum Vincom Long Biên',
          'Trung Tâm Chiếu Phim Quốc Gia',
          'Tháng 8 Cinema',
          'Kim Đồng Cinema',
          'Dân Chủ Cinema',
          'Ngọc Khánh Cinema'
        )),
      'da-nang'=>array('Đà Nẵng',array(
        'MegaStar Vĩnh Trung',
        'Lotte Đà Nẵng'
        )),
      'bien-hoa'=>array('Biên Hòa',array(
        'MegaStar Biên Hòa',
        'Lotte Đồng Nai'
        )),
      'hai-phong'=>array('Hải Phòng',array(
        'MegaStar Thùy Dương'
        )),
      'nha-trang'=>array('Nha Trang',array(
        'Lotte  Nha Trang',
        'Platinum Nha Trang'
        ))
    );

	public function getCategory($arrParam = null, $options = null){

    $rw=new Zendvn_View_Helper_CmsRewriteLink();
    $arrTP=array();
    
    if(!$arrParam['tp'])
    {
      foreach($this->listInfo as $key=>$value)
      {
        $arrTP[]=array(
          'id'=>$rw->noSign($value[0]),
          'name'=>$value[0]
        );
      }    
    }
    else
    {
      foreach($this->listInfo[$arrParam['tp']][1] as $key=>$value)
      {
        $arrTP[]=array(
          'id'=>$rw->noSign($value),
          'name'=>$value
        );
      }     
    }

    

    return $arrTP;
	}
	public function getLichChieuPhim($arrParam = null, $options = null){
    $idTP=$arrParam['tp'];
    $idRap=$arrParam['rap'];
    echo $link='http://phimchieurap.vn/lich-chieu/'.$idTP.'/rap/'.$idRap;


    $client = new Zend_Http_Client();
    $client->setUri($link);
    $client->setConfig(array(
      'maxredirects' => 2,
      'timeout'      => 30,   
   
    ));        
    $client->setHeaders(array(
      'Host'    => 'phimchieurap.vn'
    
    ));
    $respone = $client->request('GET')->getBody();

    // Assign encoding at construction
    $dom = new Zend_Dom_Query($respone, 'utf-8');
    // Assign encoding through mutator
    $dom->setEncoding('utf-8');
    // Assign encoding in setDocument*() methods
    $dom->setDocument($respone, 'utf-8');
    //$dom->setDocumentHtml($respone, 'utf-8');
    
    //$dom->query('.fl.left_main_bg .chieu');

    //$content=$dom->getDocument();
    $document=Zendvn_Dom_Process::newDocumentHTML($dom->getDocument(),"utf-8");
    $content=pq('.chieu')->html();
    
    echo '<pre>';
    print_r($content);
    echo '</pre>';ob_end_flush();
    return $content;
    
  }
  public function getTLC($arrParam = null, $options = null){
    $idQG=$arrParam['qg'];
    $idFT=2;
    $link=$this->arrNationFunc[$idQG][$idFT];

    $client = new Zend_Http_Client();
    $client->setUri($link);
    $client->setConfig(array(
      'maxredirects' => 2,
      'timeout'      => 30,   
   
    ));        
    $client->setHeaders(array(
      'Host'    => 'thethao.tinmoi.vn'
    
    ));
    $respone = $client->request('GET')->getBody();    
    $document=Zendvn_Dom_Process::newDocumentHTML($respone);

    $content=pq('#main div:eq(0)')->html();

    return $content;
    
  }
}