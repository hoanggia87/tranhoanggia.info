<?php 
	$linkGroupManager = $this->baseUrl('/default/admin-group/index/');
	$linkMemberManager = $this->baseUrl('/default/admin-user/index/');
	$linkPermission = $this->baseUrl('/default/admin-permission/index/');
    $linkPrivileges=$this->baseUrl('/default/admin-privileges/index/');
?>
<div id="submenu-box">
    <div style="border:1px solid #CCCCCC; padding:5px">
        <ul id="submenu">
            <li>
                <a href="<?php echo $linkGroupManager;?>" class="active">Group manager</a>
            </li>
            <li>
                <a href="<?php echo $linkMemberManager;?>">Member manager</a>
            </li>
            <li>
                <a href="<?php echo $linkPermission;?>">Permission</a>
            </li>
            <li>
                <a href="<?php echo $linkPrivileges;?>">Privileges</a>
            </li>
        </ul>
        <div class="clr"></div>
    </div>
</div>	