<?php 
	$linkNewsManager = $this->baseUrl('/weblink/admin-weblink/index/');
	$linkNewsCategoryManager = $this->baseUrl('/weblink/admin-weblink-category/index/');
	//$linkNewsComment = $this->baseUrl('/photo/admin-photo-comment/index/');
    $linkNewsAlbumManager = $this->baseUrl('/weblink/admin-weblink-album/index/');
?>
<div id="submenu-box">
                            <div style="border:1px solid #CCCCCC; padding:5px">
                                <ul id="submenu">
                               	 	<li>
                                        <a href="<?php echo $linkNewsCategoryManager;?>" class="active">Category manager</a>
                                    </li>                                   
                                    <li>
                                        <a href="<?php echo $linkNewsManager;?>">Web's link manager</a>
                                    </li>  
                                </ul>
                                <div class="clr"></div>
                            </div>
                        </div>	