<?php
class Block_ShowFbLike extends Zend_View_Helper_Abstract{
	public function ShowFbLike()
	{
		?>
		
        
    <div  id="shareSocial">
        <div  class="shareSocial-content">
            
            <div class="addthis_toolbox addthis_default_style ">
                
        
                <a class="addthis_button_facebook_like" fb:like:layout="button_count"></a>
                
                <!--a class="addthis_button_google_plusone" g:plusone:size="medium"></a-->
                <span class="social-share">
                    <a class="addthis_button_facebook"></a>                
                    <!--a class="addthis_button_twitter"></a-->
                    <a class="addthis_button_google"></a>
                    <a class="addthis_button_zingme"></a>
                    <!--a class="addthis_button_email"></a-->
                    <!--a class="addthis_button_rss"></a>
                    <a class="addthis_button_print"></a-->
                </span>
            </div>
            
        </div>
    </div>
    <script type="text/javascript">var addthis_config = {"data_track_clickback":true};</script>
    <script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#pubid=ra-4dfccec67f881afc"></script>
    
		<?php 
	}
	
	
}