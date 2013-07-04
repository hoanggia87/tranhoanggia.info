<?php
class Block_ShowMenuRight extends Zend_View_Helper_Abstract{
	
	public function ShowMenuRight($objView)
    {
        echo '<ul id="utility_box" class="menu_left box_shadow">
                <li><h3><a rel="1" href="http://mp3.zing.vn/radio" target="_blank" title="Nghe radio" class="listen_radio">Nghe radio</a></h3></li>
                <li><h3><a rel="4" href="/cinemaschedule.html" title="Lịch phim chiếu rạp" class="cinema">Lịch phim chiếu rạp</a></h3></li>
                <li><h3><a rel="3" href="/tvschedule.html" title="Lịch phát sóng truyền hình" class="tivi_show">Lịch phát sóng truyền hình</a></h3></li>
                <li><h3><a rel="2" href="/lottery.html" title="Kết quả xổ số" class="ticket">Kết quả xổ số</a></h3></li>
                <li><h3><a rel="5" href="/football.html?type=1" title="Bóng đá" class="football">Bóng đá</a></h3></li>
             </ul>';
	}
}