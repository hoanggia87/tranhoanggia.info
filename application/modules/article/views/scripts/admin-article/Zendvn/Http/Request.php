<?php
class Zendvn_Http_Request {
	
	private function curl_exec_follow(/*resource*/ $ch, /*int*/ &$maxredirect = null,&$new_host=null) {
				
		$content_length = 10000000;
		$mr = $maxredirect === null ? 5 : intval ( $maxredirect );
		if (ini_get ( 'open_basedir' ) == '' && ini_get ( 'safe_mode' == 'Off' )) {
			curl_setopt ( $ch, CURLOPT_FOLLOWLOCATION, $mr > 0 );
			curl_setopt ( $ch, CURLOPT_MAXREDIRS, $mr );
		
		} else {
			
			curl_setopt ( $ch, CURLOPT_FOLLOWLOCATION, false );
			if ($mr > 0) {

				
				$newurl = curl_getinfo ( $ch, CURLINFO_EFFECTIVE_URL );
						
				$rch = curl_copy_handle ( $ch );
				curl_setopt ( $rch, CURLOPT_HEADER, true );
				curl_setopt ( $rch, CURLOPT_NOBODY, true );
				//curl_setopt($rch, CURLOPT_FORBID_REUSE, false);				
				do {					
					curl_setopt ( $rch, CURLOPT_URL, $newurl );
					$header = curl_exec ( $rch );
					
					if (curl_errno ( $rch )) {
						$code = 0;
					
					} else {
						$code = curl_getinfo ( $rch, CURLINFO_HTTP_CODE );
						if ($code == 301 || $code == 302) {
							preg_match ( '/location:(.*?)\n/', strtolower ( $header ), $matches );
							
							$newurl = trim ( array_pop ( $matches ) );
							
							$arr_url = parse_url ( $newurl );
							if($arr_url['scheme'])							
								$new_host = "{$arr_url['scheme']}://{$arr_url['host']}";
							if($new_host)
							{
								$newurl = self::returnRelativePath ( $newurl, $new_host );
							}	
						
						} else {
							$code = 0;
						
						}
						preg_match ( '/content-length:(.*?)\n/', strtolower ( $header ), $content_length );
						$content_length = trim ( array_pop ( $content_length ) );
					
					}
				} while ( $code && -- $mr );
				curl_close ( $rch );
				if (! $mr) {
					if ($maxredirect === null) {
						trigger_error ( 'Too many redirects. When following redirects, libcurl hit the maximum amount.', E_USER_WARNING );
					} else {
						$maxredirect = 0;
					}

				}
				
				curl_setopt ( $ch, CURLOPT_URL, $newurl );
			}
		}
		$last_uri = curl_getinfo ( $ch, CURLINFO_EFFECTIVE_URL );
		
		$respone = curl_exec ( $ch );

		if (preg_match ( '/<meta http-equiv="refresh"(.*)URL=(.*?)""/', strtolower ( $respone ), $redirect_url )) {
			
			$url = $redirect_url [2];
			curl_setopt ( $ch, CURLOPT_URL, self::returnRelativePath ( $url, $new_host ) );
			return self::curl_exec_follow ( $ch, self::returnRelativePath ( $url, $new_host ) );
		} else {
			if (preg_match ( '/location.href="(.*?)"/', strtolower ( $respone ), $redirect_url )) {
				
				
				$url = $redirect_url [1];
				curl_setopt ( $ch, CURLOPT_URL, self::returnRelativePath ( $url, $new_host ) );
				return self::curl_exec_follow ( $ch, self::returnRelativePath ( $url, $new_host ) );
			} else {
							
				return $respone;
			}
		}
			
	}
	
	public static function returnRelativePath($url, $fullDomain) {
		if (! preg_match ( "/(https?:\/\/)/", $url )) {
			if (substr ( $url, 0, 1 ) == '/')
				$url = $fullDomain . $url;
			else {
				$url = $fullDomain . '/' . $url;
			}
		}
		return $url;
	}
	
	public static function getpageSource($url, $userAgent = "") {
		$url = trim ( $url );
		$maxredirect = 3;
		
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_HTTPGET, true );
		curl_setopt ( $ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );
		curl_setopt ( $ch, CURLOPT_HTTPHEADER, array ("Content-Type: text/html;charset=utf-8", "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8", "Accept-Language: en-us,en;q=0.5", "Accept-Encoding: gzip, deflate", "Connection: keep-alive" ) );
		if ($userAgent) {
			curl_setopt ( $ch, CURLOPT_USERAGENT, $userAgent );
		}
		curl_setopt ( $ch, CURLOPT_ENCODING, 1 );
		curl_setopt ( $ch, CURLOPT_HEADER, false );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, 10 );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt ( $ch, CURLOPT_TIMEOUT, 10 );
		$response = self::curl_exec_follow ( $ch, $maxredirect );

		//echo $response;
		
		$response = trim ( $response );
		
		$response = self::rmBOM ( $response );
		$response = preg_replace ( array ('/(?=\<\!DOCTYPE).+?(?<=>)/', '/(?=\<html).+?(?<=>)/','/<\/html>(\r\n)?(.*)/','/(?=\<head).+?(?<=>)/' ), array ('', '<html>','</html>','<head><meta http-equiv="Content-Type" content="text\/html; charset=UTF-8" \/>' ), $response );
		
		
		$response = trim($response);
		
		curl_close ( $ch );	
				
		return $response;
	
		
	//$this->pageSource = @file_get_contents($url);
	}
	
	public function rmBOM($string) {
		if (substr ( $string, 0, 3 ) == pack ( "CCC", 0xef, 0xbb, 0xbf )) {
			$string = substr ( $string, 3 );
		}
		return $string;
	}

}