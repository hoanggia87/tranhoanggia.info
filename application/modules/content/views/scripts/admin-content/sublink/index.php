<?php 
    //var_dump($this->arrParam);
	$abount = $this->baseUrl('/content/admin-content/about/');
	$license = $this->baseUrl('/content/admin-content/noiquy/');
    $homepage = $this->baseUrl('/content/admin-content/lienhe/');
    $hoidap = $this->baseUrl('/content/admin-content/hoidap/');
    if($this->arrParam['action'] == 'about')
    {
        $t1 = 'class="active"';
    }
    elseif($this->arrParam['action'] == 'noiquy')
    {
        $t2 = 'class="active"';
    }
    elseif($this->arrParam['action'] == 'lienhe')
    {
        $t3 = 'class="active"';
    }
    elseif($this->arrParam['action'] == 'hoidap')
    {
        $t4 = 'class="active"';
    }
?>
<div id="submenu-box">
                            <div style="border:1px solid #CCCCCC; padding:5px">
                                <ul id="submenu">
                               	 	<li>
                                        <a href="<?php echo $abount;?>" <?php echo $t1 ?>>About ShockVL</a>
                                    </li>                                   
                                    <li>
                                        <a href="<?php echo $license;?>" <?php echo $t2?>>Nội quy đăng bài</a>
                                    </li>
                                    <li>
                                        <a href="<?php echo $homepage;?>" <?php echo $t3 ?>>Liên hệ</a>
                                    </li>
                                    <li>
                                        <a href="<?php echo $hoidap;?>" <?php echo $t4 ?>>Hỏi đáp</a>
                                    </li>      
                                </ul>
                                <div class="clr"></div>
                            </div>
                        </div>	