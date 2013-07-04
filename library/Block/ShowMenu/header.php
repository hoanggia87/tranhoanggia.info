<?php
$tblProduct=new Product_Model_Productcategory();
//$objView->arrParam['parents']=0;
$listProSpecial=$tblProduct->listItem($objView->arrParam,array('task'=>'cms-list'));

$category=new Zendvn_View_Helper_CmsCategory();                 
$category->formatList($listProSpecial,0,1,$newArray);
        
$category->createMenuHeader($newArray,0,$listProSpecialHTML,$objView,'menu');
?>  
<header id="header">
  <h1><a href="<?php echo DOMAIN_NAME;?>">4TeeShop</a></h1>
  <div class="lavalamp dark">
    <?php echo $listProSpecialHTML;?>
    </div>
<div id="backlinks">
    <a href="#">Giới thiệu</a>
    <a href="#">Liên hệ</a>
</div>
<div class="clearfix"></div>
</header>