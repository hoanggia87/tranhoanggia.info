<?php
    $info = new Zendvn_System_Info();
    $aclInfo  = $info->getAclInfo();
                            
    $arrInfoACL=array();
    if($aclInfo['privileges'])
    {
        foreach($aclInfo['privileges'] as $key=>$infoACL)
        {
            $arrInfoACL[$key]='/'.str_replace('_','/',$infoACL).'/';
        }       
    } 
?>

<div class="logopanel">
    <h1><a href="<?php echo $objView->baseUrl('admin');?>">Royal's CMS <span>v2013.06.08</span></a></h1>
</div><!--logopanel-->

<div class="datewidget">Today is Tuesday, Dec 25, 2012 5:30pm</div>

<div class="searchwidget">
    <form action="results.html" method="post">
        <div class="input-append">
            <input type="text" class="span2 search-query" placeholder="Search here...">
            <button type="submit" class="btn"><span class="icon-search"></span></button>
        </div>
    </form>
</div><!--searchwidget-->

<div class="plainwidget">
    <small>Using 16.8 GB of your 51.7 GB </small>
    <div class="progress progress-info">
        <div class="bar" style="width: 20%"></div>
    </div>
    <small><strong>38% full</strong></small>
</div><!--plainwidget-->

<div class="leftmenu">        
    <ul class="nav nav-tabs nav-stacked">
        <li class="nav-header">Main Navigation</li>
        <li class="active"><a href="<?php echo $objView->baseUrl('admin');?>"><span class="icon-align-justify"></span> Thống kê</a></li>

        <!-- MEDIA -->
        <?php if($aclInfo['role']=='Administrator' || in_array('/media/admin-media/index/',$arrInfoACL)){?><li><a href="<?php echo $objView->baseUrl('/media/admin-media/index/');?>"><span class="icon-picture"></span> Media</a></li><?php }?>
        <!-- MEDIA END -->


        <!-- USER -->
        <?php if($aclInfo['role']=='Administrator' 
                || in_array('/media/admin-user/index/',$arrInfoACL)
                || in_array('/media/admin-group/index/',$arrInfoACL))
        {?>
        <li class="dropdown"><a href=""><span class="icon-user"></span> Quản lý người dùng</a>
            <ul>                
                <?php if($aclInfo['role']=='Administrator' || in_array('/media/admin-group/index/',$arrInfoACL)){?><li><a href="<?php echo $objView->baseUrl('/media/admin-group/index/');?>">Quản lý Group</a></li><?php }?>
                <?php if($aclInfo['role']=='Administrator' || in_array('/media/admin-user/index/',$arrInfoACL)){?><li><a href="<?php echo $objView->baseUrl('/media/admin-user/index/');?>">Danh sách User</a></li><?php }?>
                <?php if($aclInfo['role']=='Administrator' || in_array('/media/admin-user/index/',$arrInfoACL)){?><li><a href="<?php echo $objView->baseUrl('/media/admin-user/index/');?>">Quyền truy cập</a></li><?php }?>
            </ul>
        </li>   
        <?php } ?>
        <!-- USER END -->

        <!-- PRODUCT -->
        <?php if($aclInfo['role']=='Administrator' 
                || in_array('/product/admin-product-category/index/',$arrInfoACL)
                || in_array('/product/admin-product/index/',$arrInfoACL))
        {?>
        <li class="dropdown"><a href=""><span class="icon-briefcase"></span> Sản phẩm</a>
            <ul>                
                <?php if($aclInfo['role']=='Administrator' || in_array('/product/admin-product-category/index/',$arrInfoACL)){?><li><a href="<?php echo $objView->baseUrl('/product/admin-product-category/index/');?>">Danh mục sản phẩm</a></li><?php }?>
                <?php if($aclInfo['role']=='Administrator' || in_array('/product/admin-product/index/',$arrInfoACL)){?><li><a href="<?php echo $objView->baseUrl('/product/admin-product/index/');?>">Sản phẩm</a></li><?php }?>
            </ul>
        </li>
        <?php } ?>
        <!-- PRODUCT END -->

        <!-- ADVERTISING -->
        <?php if($aclInfo['role']=='Administrator' 
                || in_array('/advertising/admin-advertising-category/index/',$arrInfoACL)
                || in_array('/advertising/admin-advertising/index/',$arrInfoACL))
        {?>
        <li class="dropdown"><a href=""><span class="icon-calendar"></span> Quảng cáo</a>
            <ul>                
                <?php if($aclInfo['role']=='Administrator' || in_array('/media/admin-product-category/index/',$arrInfoACL)){?><li><a href="<?php echo $objView->baseUrl('/media/admin-product-catefory/index/');?>">Quản lý chi tiết</a></li><?php }?>
            </ul>
        </li>
        <?php } ?>
        <!-- ADVERTISING END -->

        <!-- ADVERTISING -->
        <?php if($aclInfo['role']=='Administrator')
        {?>
        <li class="dropdown"><a href=""><span class="icon-file"></span> Các trang tĩnh</a>
            <ul>                
                <?php if($aclInfo['role']=='Administrator' || in_array('/media/admin-product-category/index/',$arrInfoACL)){?><li><a href="<?php echo $objView->baseUrl('/media/admin-product-catefory/index/');?>">Quản lý chi tiết</a></li><?php }?>
            </ul>
        </li>
        <?php } ?>
        <!-- ADVERTISING END -->

    </ul>
</div><!--leftmenu-->








<?php
/*
<div class="leftmenu">        
    <ul class="nav nav-tabs nav-stacked">
        <li class="nav-header">Main Navigation</li>
        <li class="active"><a href="dashboard.html"><span class="icon-align-justify"></span> Dashboard</a></li>
        <li><a href="media.html"><span class="icon-picture"></span> Media</a></li>
        <li class="dropdown"><a href=""><span class="icon-briefcase"></span> UI Elements &amp; Widgets</a>
            <ul>
                <li><a href="elements.html">Theme Components</a></li>
                <li><a href="bootstrap.html">Bootstrap Components</a></li>
            </ul>
        </li>
        <li class="dropdown"><a href=""><span class="icon-th-list"></span> Tables</a>
            <ul>
                <li><a href="table-static.html">Static Table</a></li>
                <li><a href="table-dynamic.html">Dynamic Table</a></li>
            </ul>
        </li>
        <li><a href="typography.html"><span class="icon-font"></span> Typography</a></li>
        <li><a href="charts.html"><span class="icon-signal"></span> Graph &amp; Charts</a></li>
        <li><a href="messages.html"><span class="icon-envelope"></span> Messages</a></li>
        <li><a href="buttons.html"><span class="icon-hand-up"></span> Buttons &amp; Icons</a></li>
        <li class="dropdown"><a href=""><span class="icon-pencil"></span> Forms</a>
            <ul>
                <li><a href="forms.html">Form Styles</a></li>
                <li><a href="wizards.html">Wizard Form</a></li>
                <li><a href="wysiwyg.html">WYSIWYG</a></li>
            </ul>
        </li>
        <li><a href="calendar.html"><span class="icon-calendar"></span> Calendar</a></li>
        <li><a href="animations.html"><span class="icon-play"></span> Animations</a></li>
        <li class="dropdown"><a href=""><span class="icon-book"></span> Other Pages</a>
            <ul>
                <li><a href="404.html">404 Error Page</a></li>
                <li><a href="invoice.html">Invoice Page</a></li>
                <li><a href="editprofile.html">Edit Profile</a></li>
                <li><a href="grid.html">Grid Styles</a></li>
    <li><a href="faq.html">FAQ</a></li>
    <li><a href="stickyheader.html">Sticky Header Page</a></li>
            </ul>
        </li>
    </ul>
</div><!--leftmenu-->
*/

?>