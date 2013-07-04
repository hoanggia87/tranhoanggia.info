<?php
if($objView->arrParam['controller'] == 'post')
{
    $post = 'current';
}
?>
<div id="nav-top">
        <a href="<?php echo $objView->baseUrl('/');?>" class="logo"></a>
        <a href="<?php echo $objView->baseUrl('dang-nhap.shock')?>" class="top-gray-button">Đăng nhập</a>
        <a href="<?php echo $objView->baseUrl('dang-anh.shock')?>" class="top-gray-button <?php echo $post;?>" id="menu_login">Đăng bài</a>
        <a href="<?php echo $objView->baseUrl('bang-xep-hang')?>" class="top-gray-button" id="menu_login">Bảng xếp hạng</a>
        <a href="#" class="orange-dropdown" id="menu_hover" tooltip="menu_hover">Chuyên mục</a>
        <div class="dropdown-menu-block" id="top_pullmenu" style="position: absolute; top: 38px; left: 1184.5px;">
            <div class="arrow-block">
                <div class="drop-arrow"></div>
            </div>
            <div class="drop-menu-bg gradient">
                <a href="<?php echo $objView->baseUrl('new')?>" class="menu-company">Hình ảnh - Video</a> 
                <a href="<?php echo $objView->baseUrl('truyen-cuoi')?>" class="menu-free-membership">Truyện cười</a>
                <a href="<?php echo $objView->baseUrl('bao-hay')?>" class="menu-pro-membership">Báo hay</a>
                <a href="<?php echo $objView->baseUrl('tam-su')?>" class="menu-resellers">Tâm sự</a> 
                <!--a href="/#help" class="menu-faq">Hỗ trợ - Hỏi đáp</a>    
                <a href="/#blog" class="menu-blogs">Blog</a>	
                <a href="/#copyright" class="menu-copyright">Copyright</a>			
                <a href="/#contact" class="menu-contact">Liên hệ</a> 
                <a href="/#privacy" class="menu-privacy">Chính sách riêng tư</a>
                <a href="/#terms" class="menu-terms">Điều khoản dịch vụ</a>     
                <a href="/#developers" class="menu-developers">Nhà phát triển</a-->
            </div>
        </div>
        <a href="<?php echo $objView->baseUrl('hot');?>" class="top-gray-button" id="menu_login">Hot</a>
        <a href="<?php echo $objView->baseUrl('new');?>" class="top-gray-button" id="menu_login">Shock toàn tập</a>
    </div>