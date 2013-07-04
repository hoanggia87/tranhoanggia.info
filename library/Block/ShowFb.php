<?php

class Block_ShowFb extends Zend_View_Helper_Abstract {
   
	public static  $_instance = null;
	public $_iInit = false;
		
	public function ShowFb($arrParam,$options=null)
	{
		if(!$this->_iInit)
		{
			$this->initScript();
			$this->_iInit = true;
		}
		return $this->showComponent($arrParam,$options);
	}
	
	public function showComponent($arrParam,$options=null)
	{
		$type = $options['type'];
		if($type == 'like')
		{
			// like 
			$linkDetail = $arrParam['detail'];
			return ' 
                        <div class="facebook-share-btn"></div>
                        <div class="facebook-btn">
                            <div class="fb-like fb_edge_widget_with_comment fb_iframe_widget" data-href="'.$linkDetail.'" data-send="false" data-layout="button_count" data-width="450" data-show-faces="false"></div>
                        </div>
                    ';			
		}
		else
		{
			// comment
			$linkDetail = $arrParam['detail'];
			return '<div class="fb-comments" data-href="'.$linkDetail.'" data-num-posts="10" data-width="720" fb-xfbml-state="rendered"></div>';
		}
		
		
		
	}
	
	
	private function initScript()
	{
		
	  ?>
	 	<div id="fb-root"></div>
		<script>
		 function updateStats(url)
		 {
			 $.ajax({url:"http://www.shockvl.com/stats/index/statsupdate/?url="+url,success:function(result){				    
				  }});
		 }
		 window.fbAsyncInit = function() {
			    FB.init({
			        appId  : '134121816783332',
			        status : true, // check login status
			        cookie : true, // enable cookies to allow the server to access the session
			        xfbml  : true  // parse XFBML
			    });

			    /* All the events registered */
			    FB.Event.subscribe('comment.create', function (response) {
			    	updateStats(response.href);			    	
			    });
			    FB.Event.subscribe('comment.remove', function (response) {
			    	updateStats(response.href);			    	
			    });
			    FB.Event.subscribe('edge.create', function (response) {
			        // do something with response
			    	updateStats(response);
			    });
			    FB.Event.subscribe('edge.remove', function (response) {
			        // do something with response
			    	updateStats(response);
			    });
			};
		(function(d, s, id) {
		  var js, fjs = d.getElementsByTagName(s)[0];
		  if (d.getElementById(id)) return;
		  js = d.createElement(s); js.id = id;
		  js.src = "//connect.facebook.net/en_GB/all.js"; fjs.parentNode.insertBefore(js, fjs);}(document, 'script', 'facebook-jssdk'));</script>
	<?php  
	}
	
	
}

?>