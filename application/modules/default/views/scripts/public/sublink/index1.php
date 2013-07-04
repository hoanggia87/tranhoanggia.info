<?php 
	$linkChangePassword = $this->baseUrl('/default/public/change-password/');
	$linkChangeProfile = $this->baseUrl('/default/public/change-profile/');
?>
<div id="submenu-box">
                            <div style="border:1px solid #CCCCCC; padding:5px">
                                <ul id="submenu">
                                    <li>
                                        <a href="<?php echo $linkChangePassword;?>" class="active">Change Password</a>
                                    </li>
                                    <li>
                                        <a href="<?php echo $linkChangeProfile;?>">Change Profile</a>
                                    </li>
                                   
                                </ul>
                                <div class="clr"></div>
                            </div>
                        </div>	