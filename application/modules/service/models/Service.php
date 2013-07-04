<?php
class Service_Model_Service extends Zend_Db_Table{
	var $arrTP=array(
      23=>array(
        'khuvuc'=>'Nam Bộ',
        'citys'=>array(
              44=>'tp Hồ Chí Minh',
              96=>'An Giang',
              45=>'Bình Dương',
              53=>'Bến Tre',
              59=>'Bạc Liêu',
              60=>'Bình Phước',
              74=>'Cà Mau',
              56=>'Cần Thơ',
              134=>'Côn Đảo',
              51=>'Đồng Tháp',
              62=>'Đồng Nai',
              54=>'Hậu Giang',
              73=>'Kiên Giang',
              50=>'Long An',
              135=>'Phú Quốc',
              57=>'Sóc Trăng',
              52=>'Tiền Giang',
              66=>'Tây Ninh',
              61=>'Vũng Tàu'
        )),
        18=>array(
        'khuvuc'=>'Tây Bắc Bộ',
        'citys'=>array(
              70=>'Điện Biên',
              4=>'Hòa Bình',
              69=>'Lào Cai',
              127=>'Lai Châu',
              78=>'Sơn La',
              87=>'Yên Bái'
        )),      
        19=> array(
        'khuvuc'=>'Phía Đông Bắc Bộ',
        'citys'=>array(
            80=> 'Bắc Giang',
            81=> 'Bắc Ninh',
            86=> 'Bắc Cạn',
            130=> 'Bạch Long Vĩ',
            136=> 'Cát Bà',
            83=> 'Cao Bằng',
            88=> 'Hải Phòng',
            48=> 'Hải Dương',
            63=> 'Hương Yên',
            68=> 'Hà Giang',
            82=> 'Lạng Sơn',
            64=> 'Nam Định',
            46=> 'Ninh Bình',
            49=> 'Phủ Lý',
            120=> 'Quảng Ninh',
            84=> 'Tuyên Quang',
            85=> 'Thái Nguyên',
            58=> 'Hà Nội',
            47=> 'Thái Bình',
            76=> 'Việt Trì',
            77=> 'Vĩnh Phúc'
        )),
        20=> array(
        'khuvuc'=>'Thanh Hóa - Thừa Thiên Huế',
        'citys'=>array(
            131=> 'Cồ Cỏ',
            28=> 'Hà Tĩnh',
            132=> 'Lý Sơn',
            29=> 'Quảng Bình',
            30=> 'Quảng Trị',
            31=> 'Thừa Thiên Huế',
            106=> 'Thanh Hóa',
            111=> 'Vinh'
        )),    
        21=>array(
        'khuvuc'=> 'Đà Nẵng đến Bình Thuận',
        'citys'=>array(
            39=> 'Bình Thuận',
            94=> 'Bình Định',
            92=> 'Đà Nẵng',
            129=> 'Hoàng Sa',
            37=> 'Nha Trang',
            100=> 'Ninh Thuận',
            99=> 'Phú Yên',
            133=> 'Phú Quý',
            119=> 'Quảng Nam',
            34=> 'Quảng Ngãi',
            128=> 'Trường Sa',
        )),
        22=> array(
        'khuvuc'=>'Tây Nguyên',
        'citys'=>array(
            42=> 'Đak Lak',
            43=> 'Đà Lạt',
            71=> 'Đak Nông',
            40=> 'Kon Tum',
            41=> 'Pleiku'  
        ))
  );
	public function getThoitiet($arrParam = null, $options = null){
    //$arrParam['makv'];
    //$arrParam['matinh'];
    echo '<pre>';
    print_r($this->arrTP[$arrParam['makv']]['khuvuc']);
    print_r($this->arrTP[$arrParam['makv']]['citys'][$arrParam['matinh']]);
    echo '</pre>';
    echo 'http://www.nchmf.gov.vn/web/vi-VN/62/'.$arrParam['makv'].'/'.$arrParam['matinh'].'/map/Default.aspx';

		$client = new Zend_Http_Client();
    $client->setUri('http://www.nchmf.gov.vn/web/vi-VN/62/'.$arrParam['makv'].'/'.$arrParam['matinh'].'/map/Default.aspx');
    $client->setConfig(array(
      'maxredirects' => 2,
      'timeout'      => 30,   
   
    ));        
    $client->setHeaders(array(
      'Host'    => 'nchmf.gov.vn'
    
    ));
    $respone = $client->request('GET')->getBody();
    
    $document=Zendvn_Dom_Process::newDocumentHTML($respone);

    $arrInfo=array(
        array(
            'day'=>trim(strip_tags(pq('.tieude_dubao:eq(1)')->html())),
            'status'=>trim(pq('.tieude_dubao_yeuto_content img:eq(0)')->parents("table:eq(0)")->next()->find("td")->html()),
            'image'=>pq('.tieude_dubao_yeuto_content img:eq(0)')->attr("src"),
            't_min'=>trim(pq('.tieude_dubao_yeuto_content strong:eq(0)')->html()),
            't_max'=>trim(pq('.tieude_dubao_yeuto_content strong:eq(3)')->html())
          ),
        array(
            'day'=>trim(strip_tags(pq('.tieude_dubao:eq(2)')->html())),
            'status'=>trim(pq('.tieude_dubao_yeuto_content img:eq(1)')->parents("table:eq(0)")->next()->find("td")->html()),
            'image'=>pq('.tieude_dubao_yeuto_content img:eq(1)')->attr("src"),
            't_min'=>trim(pq('.tieude_dubao_yeuto_content strong:eq(1)')->html()),
            't_max'=>trim(pq('.tieude_dubao_yeuto_content strong:eq(4)')->html())
          ),
        array(
            'day'=>trim(strip_tags(pq('.tieude_dubao:eq(3)')->html())),
            'status'=>trim(pq('.tieude_dubao_yeuto_content img:eq(2)')->parents("table:eq(0)")->next()->find("td")->html()),
            'image'=>pq('.tieude_dubao_yeuto_content img:eq(2)')->attr("src"),
            't_min'=>trim(pq('.tieude_dubao_yeuto_content strong:eq(2)')->html()),
            't_max'=>trim(pq('.tieude_dubao_yeuto_content strong:eq(5)')->html())
          )
      );

      echo '<pre>';
      print_r($arrInfo);
      echo '</pre>';
    
    return $result;
		
	}
  public function getBongda($arrParam = null, $options = null){
    
  }
	
}