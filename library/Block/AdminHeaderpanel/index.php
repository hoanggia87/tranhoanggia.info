<?php 
$info =	new Zendvn_System_Info();
$infoUser=	$info->getInfo();
$infoUser=$infoUser['member'];



?>

<div class="headerpanel">
	<a href="" class="showmenu"></a>
	
	<div class="headerright">
		<div class="dropdown notification">
			<a class="dropdown-toggle" data-toggle="dropdown" data-target="#" href="/page.html">
				<span class="iconsweets-globe iconsweets-white"></span>
			</a>
			<ul class="dropdown-menu">
				<li class="nav-header">Notifications</li>
				<li>
					<a href="">
					<strong>3 people viewed your profile</strong><br />
					<img src="<?php echo $objView->imgUrl;?>/thumbs/thumb1.png" alt="" />
					<img src="<?php echo $objView->imgUrl;?>/thumbs/thumb2.png" alt="" />
					<img src="<?php echo $objView->imgUrl;?>/thumbs/thumb3.png" alt="" />
					</a>
				</li>
				<li><a href=""><span class="icon-envelope"></span> New message from <strong>Jack</strong> <small class="muted"> - 19 hours ago</small></a></li>
				<li><a href=""><span class="icon-envelope"></span> New message from <strong>Daniel</strong> <small class="muted"> - 2 days ago</small></a></li>
				<li><a href=""><span class="icon-user"></span> <strong>Bruce</strong> is now following you <small class="muted"> - 2 days ago</small></a></li>
				<li class="viewmore"><a href="">View More Notifications</a></li>
			</ul>
		</div><!--dropdown-->
		
		<div class="dropdown userinfo">
			<a class="dropdown-toggle" data-toggle="dropdown" data-target="#" href="/page.html">Hi, <?php echo $infoUser['full_name'];?>! <b class="caret"></b></a>
			<ul class="dropdown-menu">
				<li><a href="<?php echo $objView->baseUrl('/default/admin/edit-profile');?>"><span class="icon-edit"></span> Edit Profile</a></li>
				<!--li><a href=""><span class="icon-wrench"></span> Account Settings</a></li>
				<li><a href=""><span class="icon-eye-open"></span> Privacy Settings</a></li-->
				<li class="divider"></li>
				<li><a href="<?php echo $objView->baseUrl('/default/public/logout');?>"><span class="icon-off"></span> Sign Out</a></li>
			</ul>
		</div><!--dropdown-->
	
	</div><!--headerright-->
	
</div><!--headerpanel-->