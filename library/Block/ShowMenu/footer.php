 <footer>
    <div class=" container_12">
    	<div class="wrapper">
    		<div class="grid_12">
    			<div class="wrapper">

					<div class="privacy fright">Copyright &copy; 2012<a href="http://www.nhannghia.net/" title="Chuyên cung cấp Laptop, Máy In ..." target="_blank"><strong style="color: red;">Công ty Nhân Nghĩa</strong></a>. All Rights Reserved.  Designed by<a href="http://www.nhannghia.net/" title="Chuyên cung cấp Laptop, Máy In ..." target="_blank"><strong style="color: red; display:block">Nhân Nghĩa Computer</strong></a>
					<br />
                    Bản quyền thuộc<a href="http://www.nhannghia.net/" title="Chuyên cung cấp Laptop, Máy In ..." target="_blank"><strong style="color: red; display:block">Công ty Nhân Nghĩa</strong></a>. Vui lòng để trích dẫn khi sử dụng bài viết.</br>
					Đối tác liên kết<a href="http://www.themuasam.vn/" title="Dịch vụ thẻ mua sắm thông minh" target="_blank"><strong style="color: red; display:block">www.TheMuaSam.vn</strong></a> - Dịch vụ thẻ giảm giá thông minh.
					<br /></br>
					</div>
					<div class="privacy fright"><b style="color:#008000;">Địa chỉ 1: 71/9 Đường Tân Mỹ, P. Tân Thuận Tây, Quận 7, TP.HCM (Khu Đô Thị Phú Mỹ Hưng)</b>
					<br />
					<div class="privacy fright"><b style="color:#008000;">Địa chỉ 2: Lầu 3, Tòa nhà Mai Hồng Quế, Số 85 Nguyễn Hữu Cầu, Phường Tân Định, Quận 1</b>
						<br>
					======================================= <br />
                    <b style="color:#008000;"><img src="http://m.funring.vn/frwap/images/bullet.gif">
					 Hotline </b>: 0906 60 1098 - 0933 99 8027<br />
					 <b style="color:#008000;"><img src="http://m.funring.vn/frwap/images/bullet.gif">
					 Fax </b>: 08.38.724.739<br />
					<b style="color:#008000;"><img src="http://m.funring.vn/frwap/images/bullet.gif">
					 Yahoo </b>: ViTinhNhanNghia<br />
					<b style="color:#008000;"><img src="http://m.funring.vn/frwap/images/bullet.gif">
					 Skype </b>: ViTinhNhanNghia<br />
					<b style="color:#008000;"><img src="http://m.funring.vn/frwap/images/bullet.gif">
					 Email </b>: info@NhanNghia.net<br />
					======================================= <br />
					</div>
    				
    			</div>
    		</div>
    	</div>
    </div>
</footer>
	<!--popup quang cao-->
	<div id="redidea_adpo">
		<div class="ra_tool">
			<a id="ra_hide" h="150" w="200" s="1">Hide</a>
		</div>
		<div id="ra_content">
			<a href="<?php echo $objView->baseUrl('bac-si-oi.html');?>">
				<!--  img src="<?php echo $objView->imgUrl.'/QCPopup.jpg';?>"-->
			</a>
		<div class="clr"></div>
		</div>		
	</div>
	<script>
	$(document).ready(function(){
		var h=$("#ra_content").height();
		
		$("#ra_hide").click(function(){
			var ch=$("#ra_content").height();
			
			if(ch==h)
			{
				$("#ra_content").animate({height:"0px"});
				$("#ra_hide").addClass("hide");
			}
			else
			{
				$("#ra_content").animate({height:h+"px"});
				$("#ra_hide").removeClass("hide");
			}
			
		});
	});
	
	</script>
	<!--popup quang cao end-->
	<script>
	$(document).ready(function(){
		$($("ul.footer-menu > li")[3]).append('<img src="<?php echo $objView->imgUrl.'/icon_new.gif';?>">');
		$($("ul.sf-menu > li")[4]).prepend('<img style="position:absolute;right:2px;top:5px;z-index:1;" src="<?php echo $objView->imgUrl.'/icon_new.gif';?>">');
	});
	
	</script>
	