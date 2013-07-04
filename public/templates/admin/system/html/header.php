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
    echo $this->doctype() 
?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
       	<?php echo $this->headTitle() ?>
       	<?php echo $this->headMeta() ?>
		<?php echo $this->headLink() ?>
		<?php echo $this->headScript() ?>
    </head>
    <body id="minwidth-body">
   
        <div id="border-top" class="h_green">
            <div>
                <div>
                    <span class="version">Version 07.2012</span>
                    <span class="title" style="padding-left:20px">Royal CMS </span>
                </div>
            </div>
        </div>
        <div id="header-box">
            <div id="module-status">
                <span class="preview">
                    <a target="_blank" href="#">Preview</a>
                </span>
                <a href="#">
                    <span class="no-unread-messages">0</span>
                </a>
                <span class="loggedin-users">1</span>
                <span class="logout">
                    <a href="<?php echo $this->baseUrl('/default/public/logout');?>">Logout</a>
                </span>
            </div>
            <div id="module-menu">

                <!-- BEGIN: Menu -->
                <ul class="menuTiny" id="menuTiny">
                    <li><a href="#" class="menuTinyLink">Main</a>
                        <ul>
                            <li><a href="<?php echo $this->baseUrl('/default/public/change-password/');?>">Change password</a></li>
                            <li><a href="<?php echo $this->baseUrl('/default/public/logout');?>">Logout</a></li>
                        </ul>
                    </li>
                    <li><a href="#" class="menuTinyLink">View</a>
                        <ul>
                            <li><a href="<?php echo $this->baseUrl('/default/index/index/');?>">Front End</a></li>

                        </ul>
                    </li>

                    <!--li>
                        <a href="#" class="menuTinyLink">Sản phẩm </a>
                        <ul>
                            <?php if($aclInfo['role']=='Administrator' || in_array('/product/admin-product-category/index/',$arrInfoACL)){?><li><a href="<?php echo $this->baseUrl('/product/admin-product-category/index/');?>">Quản lý nhóm sản phẩm</a></li><?php }?>
                            
                            <?php if($aclInfo['role']=='Administrator' || in_array('/product/admin-product/index/',$arrInfoACL)){?><li><a href="<?php echo $this->baseUrl('/product/admin-product/index/');?>">Quản lý sản phẩm</a></li><?php }?>
                            
                        </ul>
                    </li-->
                    <li>
                        <a href="#" class="menuTinyLink">Quản lý bài post</a>
                        <ul>
                            
                            <?php if($aclInfo['role']=='Administrator' || in_array('/article/admin-article/index/',$arrInfoACL)){?><li><a href="<?php echo $this->baseUrl('/article/admin-article/index/');?>">Duyệt tin</a></li><?php }?>
                            <?php if($aclInfo['role']=='Administrator' || in_array('/article/admin-article-manager/index/',$arrInfoACL)){?><li><a href="<?php echo $this->baseUrl('/article/admin-article-manager/index/');?>">Quản lý tin</a></li><?php }?>
                            
                        </ul>
                    </li>
                    <li>
                        <a href="#" class="menuTinyLink">Quản lý nội dung</a>
                        <ul>
                            <?php if($aclInfo['role']=='Administrator' || in_array('/content/admin-content/about/',$arrInfoACL)){?><li><a href="<?php echo $this->baseUrl('/content/admin-content/about/');?>">Giới thiệu ShockVL</a></li><?php }?>
                            <?php if($aclInfo['role']=='Administrator' || in_array('/content/admin-content/noiquy/',$arrInfoACL)){?><li><a href="<?php echo $this->baseUrl('/content/admin-content/noiquy/');?>">Nội quy đăng bài</a></li><?php }?>
                            <?php if($aclInfo['role']=='Administrator' || in_array('/content/admin-content/hoidap/',$arrInfoACL)){?><li><a href="<?php echo $this->baseUrl('/content/admin-content/hoidap/');?>">Hỏi đáp</a></li><?php }?>
                            <?php if($aclInfo['role']=='Administrator' || in_array('/content/admin-content/lienhe/',$arrInfoACL)){?><li><a href="<?php echo $this->baseUrl('/content/admin-content/lienhe/');?>">Liên hệ</a></li><?php }?>
                        </ul>
                    </li>

                   <!--li>
                        <a href="#" class="menuTinyLink">Photo</a>
                        <ul>
                            <?php if($aclInfo['role']=='Administrator' || in_array('/photo/admin-photo-category/index/',$arrInfoACL)){?><li><a href="<?php echo $this->baseUrl('/photo/admin-photo-category/index/');?>">Category manager</a></li><?php }?>
                            <?php if($aclInfo['role']=='Administrator' || in_array('/photo/admin-photo-album/index/',$arrInfoACL)){?><li><a href="<?php echo $this->baseUrl('/photo/admin-photo-album/index/');?>">Album manager</a></li><?php }?>
                            <?php if($aclInfo['role']=='Administrator' || in_array('/photo/admin-photo/index/',$arrInfoACL)){?><li><a href="<?php echo $this->baseUrl('/photo/admin-photo/index/');?>">Photo manager</a></li>   <?php }?>                         
                            
                        </ul>
                    </li-->
                    <!--
                    <li>
                        <a href="#" class="menuTinyLink">Nhân Nghĩa</a>
                        <ul>
                            <?php if($aclInfo['role']=='Administrator' || in_array('/news/admin-news-category/index/',$arrInfoACL)){?><li><a href="<?php echo $this->baseUrl('/news/admin-news-category/index/section/1');?>">Category manager</a></li><?php }?>
                            <?php if($aclInfo['role']=='Administrator' || in_array('/news/admin-news/index/',$arrInfoACL)){?><li><a href="<?php echo $this->baseUrl('/news/admin-news/index/section/1');?>">Aticle manager</a></li><?php }?>
                            
                        </ul>
                    </li>                    
                    <li>
                        <a href="#" class="menuTinyLink">Tin tức</a>
                        <ul>
                            <?php if($aclInfo['role']=='Administrator' || in_array('/news/admin-news-category/index/',$arrInfoACL)){?><li><a href="<?php echo $this->baseUrl('/news/admin-news-category/index/section/2');?>">Category manager</a></li><?php }?>
                            <?php if($aclInfo['role']=='Administrator' || in_array('/news/admin-news/index/',$arrInfoACL)){?><li><a href="<?php echo $this->baseUrl('/news/admin-news/index/section/2');?>">Aticle manager</a></li><?php }?>
                            
                        </ul>
                    </li>
                    <li>
                        <a href="#" class="menuTinyLink">Dịch vụ</a>
                        <ul>
                            <?php if($aclInfo['role']=='Administrator' || in_array('/news/admin-news-category/index/',$arrInfoACL)){?><li><a href="<?php echo $this->baseUrl('/news/admin-news-category/index/section/3');?>">Category manager</a></li><?php }?>
                            <?php if($aclInfo['role']=='Administrator' || in_array('/news/admin-news/index/',$arrInfoACL)){?><li><a href="<?php echo $this->baseUrl('/news/admin-news/index/section/3');?>">Aticle manager</a></li><?php }?>
                            
                        </ul>
                    </li>
                  <li>
                        <a href="#" class="menuTinyLink">Thiết kế Web</a>
                        <ul>
                            <?php if($aclInfo['role']=='Administrator' || in_array('/news/admin-news-category/index/',$arrInfoACL)){?><li><a href="<?php echo $this->baseUrl('/news/admin-news-category/index/section/4');?>">Category manager</a></li><?php }?>
                            <?php if($aclInfo['role']=='Administrator' || in_array('/news/admin-news/index/',$arrInfoACL)){?><li><a href="<?php echo $this->baseUrl('/news/admin-news/index/section/4');?>">Aticle manager</a></li><?php }?>
                            
                        </ul>
                    </li>
                    
					<li>
                        <a href="#" class="menuTinyLink">Quảng cáo</a>
                        <ul>
                            <?php if($aclInfo['role']=='Administrator' || in_array('/advertising/admin-advertising-category/index/',$arrInfoACL)){?><li><a href="<?php echo $this->baseUrl('/advertising/admin-advertising-category/index/');?>">Vị trí quảng cáo</a></li><?php }?>
                            <?php if($aclInfo['role']=='Administrator' || in_array('/advertising/admin-advertising-module/index/',$arrInfoACL)){?><li><a href="<?php echo $this->baseUrl('/advertising/admin-advertising-module/index/');?>">Module quảng cáo</a></li><?php }?>
                            <?php if($aclInfo['role']=='Administrator' || in_array('/advertising/admin-advertising/index/',$arrInfoACL)){?><li><a href="<?php echo $this->baseUrl('/advertising/admin-advertising/index/');?>">Danh sách quảng  cáo</a></li><?php }?>    
                        </ul>
                    </li>
					-->
                    <!--
					<li>
                        <a href="#" class="menuTinyLink" style="cursor:pointer !important">FAQ Info</a>
                        <ul>
                            <?php if($aclInfo['role']=='Administrator' || in_array('/faq/admin-faq/',$arrInfoACL)){?><li><a href="<?php echo $this->baseUrl('/faq/admin-faq/');?>">Câu hỏi thường gặp</a></li><?php }?>
                            <?php if($aclInfo['role']=='Administrator' || in_array('contact/admin-contact/aboutdoctor',$arrInfoACL)){?><li><a href="<?php echo $this->baseUrl('contact/admin-contact/aboutdoctor');?>">Info Bác sĩ ơi</a></li><?php }?>
                            <?php if($aclInfo['role']=='Administrator' || in_array('contact/admin-contact/callme',$arrInfoACL)){?><li><a href="<?php echo $this->baseUrl('contact/admin-contact/callme');?>">Info Gửi câu hỏi</a></li><?php }?>
                            <?php if($aclInfo['role']=='Administrator' || in_array('/faq/admin-callme-category/',$arrInfoACL)){?><li><a href="<?php echo $this->baseUrl('/faq/admin-callme-category/');?>">Danh mục Bác sĩ ơi</a></li><?php }?>
                            <?php if($aclInfo['role']=='Administrator' || in_array('/faq/admin-callme/',$arrInfoACL)){?><li><a href="<?php echo $this->baseUrl('/faq/admin-callme/');?>">Câu hỏi Bác sĩ ơi</a></li><?php }?>
                        </ul>                        
                    </li>  
					-->					


                </ul>

                <script type="text/javascript">
                    var menu=new menu.dd("menu");
                    menu.init("menuTiny","menuTinyHover");
                </script><!-- END: Menu -->				




            </div>
            <div class="clr"></div>
        </div>
        <script>
            jQuery(document).ready(function(){
                
                jQuery("#menuTiny li ul").each(function(){
                    
                    if(jQuery(this).find("li").length==0)
                    {
                        jQuery(this).parent().remove();
                    }
                
                });    
            });
            
        </script>