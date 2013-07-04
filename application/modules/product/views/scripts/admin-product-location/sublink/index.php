<?php 
	$linkProductManager = $this->baseUrl('/product/admin-product/index/');
    $linkProductSpecialManager = $this->baseUrl('/product/admin-product-special/index/');
	$linkProductCategoryManager = $this->baseUrl('/product/admin-product-category/index/');
	$linkProductLocationManager = $this->baseUrl('/product/admin-product-location/index/');
	$linkProductPromotionManager = $this->baseUrl('/product/admin-product-promotion/index/');
	$linkProductOrderManager = $this->baseUrl('/product/admin-product-order/index/');
	$linkProductComment = $this->baseUrl('/product/admin-product-comment/index/');
?>
<div id="submenu-box">
                            <div style="border:1px solid #CCCCCC; padding:5px">
                                <ul id="submenu">
                               	 	<li>
                                        <a href="<?php echo $linkProductCategoryManager;?>">Category manager</a>
                                    </li>
									<li>
                                        <a href="<?php echo $linkProductLocationManager;?>" class="active">Location manager</a>
                                    </li>
                                    <li>
                                        <a href="<?php echo $linkProductManager;?>">Product manager</a>
                                    </li>      
                                    <li>
                                        <a href="<?php echo $linkProductSpecialManager;?>">Product special manager</a>
                                    </li>   
                                    <!--li>
                                        <a href="<?php echo $linkProductPromotionManager;?>">Promotion manager</a>
                                    </li> 
                                    <li>
                                        <a href="<?php echo $linkProductOrderManager;?>">Order</a>
                                    </li>                               
                                    <li>
                                        <a href="<?php echo $linkProductComment;?>">Comment manager</a>
                                    </li-->
                                </ul>
                                <div class="clr"></div>
                            </div>
                        </div>	