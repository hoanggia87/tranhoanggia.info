<?php
class Service_Model_Truyenhinh extends Zend_Db_Table
{
    /*var $arrListChanel = array(
                                189 => 'GIẢI TRÍ TV',
                                146 => 'VCTV2- Phim Việt',
                                139 => 'VCTV3 - Thể thao TV',
                                147 => 'VCTV4 - M4me',
                                185 => 'VCTV5-E - Channel',
                                148 => 'VCTV6',
                                149 => 'VCTV7 - D-Dramas',
                                150 => 'VCTV8 - Bibi',
                                186 => 'VCTV10 - O2TV',
                                183 => 'VCTV12 - Style TV',
                                187 => 'VCTV15 - Invest TV',
                                143 => 'VCTV16-BongdaTV',
                                184 => 'VCTV17',
                                188 => 'Australia Network',
                                190 => 'AXN',
                                155 => 'Cartoon Network',
                                193 => 'Channel [V]',
                                158 => 'Cinemax',
                                167 => 'Disney Channel',
                                161 => 'Diva Universal',
                                168 => 'FOX SPORTS',
                                174 => 'FOX SPORTS PLUS',
                                159 => 'HBO',
                                177 => 'HBO HD',
                                173 => 'K + Nhịp sống',
                                172 => 'K+1',
                                192 => 'NGA',
                                171 => 'NGC',
                                178 => 'NGC HD',
                                191 => 'NGW',
                                160 => 'Star Movies',
                                169 => 'Star Sports',
				                164 => 'StarWorld'                                   
                              );*/
    var $arrListChanel = array( '1' => 'VTV1',
								'2' => 'VTV2',
								'3' => 'VTV3',
								'4' => 'VTV4',
								'6' => 'VTV6',
								'9' => 'VTV9',
								'11' => 'K+1',
								'12' => 'K+NS',
								'13' => 'K+PC',
								'21' => 'VTVcab1',
								'22' => 'VTVcab2',
								'23' => 'VTVcab3',
								'26' => 'VTVcab6',
								'27' => 'VTVcab7',
								'28' => 'VTVcab8');
    public function getListChanel()
    {
        return $this->arrListChanel;
    }
	public function getTruyenhinh($arrParam)
    {
        $client = new Zend_Http_Client();
        $arrListChanel = $this->arrListChanel;
        foreach($arrListChanel as $key => $value)
        {
            $client->setUri('http://vtv.vn/TVSchedule/136/'.$key.'/'.$arrParam['date'].'/'.$arrParam['month'].'/'.$arrParam['year'].'.vtv');
            $client->setConfig(array(
              'maxredirects' => 2,
              'timeout'      => 30,   
           
            ));
            $client->setHeaders(array(
                  'Host'    => 'vtv.vn'
                
                ));
            $response = $client->request('GET')->getBody();
            $response = trim ( $response );
		
        	$response = self::rmBOM ( $response );
        	$response = preg_replace ( array ('/(?=\<\!DOCTYPE).+?(?<=>)/', '/(?=\<html).+?(?<=>)/','/<\/html>(\r\n)?(.*)/','/(?=\<head).+?(?<=>)/' ), array ('', '<html>','</html>','<head><meta http-equiv="Content-Type" content="text\/html; charset=UTF-8" \/>' ), $response );
        	
        	
        	$response = trim($response);
            $document=Zendvn_Dom_Process::newDocumentHTML($response);
            //echo trim(pq('#program-list')->htmlOuter());
            $arrResult[$key] = trim(pq('#program-list')->htmlOuter());
        }
        echo '<pre>';
        var_dump($arrResult);
        echo '</pre>';
        return $arrResult;
        /*$document=Zendvn_Dom_Process::newDocumentHTML($respone);
        echo '<pre>';
        var_dump($respone);
        echo '</pre>';*/
    }
    public function rmBOM($string) {
        if (substr ( $string, 0, 3 ) == pack ( "CCC", 0xef, 0xbb, 0xbf )) {
            $string = substr ( $string, 3 );
        }
        return $string;
    }
}