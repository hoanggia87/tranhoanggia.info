<?php
$tblWebLinkCat=new Weblink_Model_Weblinkcategory();
$arrListCat=$tblWebLinkCat->listItem(null,array('task'=>'front-list-all-cat'));

$html='';
$isFirst=1;
foreach ($arrListCat as $key => $value) {

	$class='';
	if($isFirst==1)
	{
		$isFirst=0;
		$class='data-transition="slidedown" data-collapsed="false"';
		
	}
	$html.='
		<div data-role="collapsible" '.$class.'>
	        <h3>'.$value['name'].'</h3>
	        <ul data-role="listview" data-inset="false">';

	foreach ($value['items'] as $k => $v) {
		$html.='    <li><a href="#panel-fixed-page2" data-transition="slide">'.$v['name'].'</a></li>';
	}	
	           
	$html.='</ul>
	    </div>';
}
?>
<div data-role="panel" data-position="left" data-position-fixed="false" data-theme="c" id="nav-panel">

	<form>
	     <label for="search-1">Tìm kiếm trang của bạn</label>
	     <input name="search-1" id="search-1" value="" type="search">
	</form>

	<div data-role="collapsible-set" data-inset="false" data-iconpos="left" data-collapsed-icon="plus" data-expanded-icon="minus" data-theme="b">
	    <?php echo $html;?>
	</div>
</div><!-- /panel -->