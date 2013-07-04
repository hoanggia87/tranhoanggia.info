<?php
class Block_ShowCopyRight extends Zend_View_Helper_Abstract{
	
	public function ShowCopyRight($objView)
    {
        echo '<div class="copyright box_shadow">
                <h3 class="blue">Copyright © LaBàn.vn 2013</h3>
                <ul>
                    <li><a href="http://laban.vn/support/about" title="Giới thiệu về LaBàn.vn">Giới thiệu LaBàn.vn</a></li>
                    <li><a href="http://laban.vn/support/tos" title="Thỏa thuận sử dụng">Thỏa thuận sử dụng</a></li>
                    <li><a href="http://laban.vn/support/removehp" title="Hướng dẫn gỡ cài đặt">Gỡ cài đặt</a></li>
                    <li><a href="http://laban.vn/support/sethomepage" title="Hướng dẫn cài đặt công cụ La Bàn">Đặt làm trang chủ như thế nào?</a></li>
                    <li><a href="" title="">Đặt bookmark</a></li>
                    <li>
                        <a href="" title="">Chia sẻ trang</a>&nbsp;
                        <a href="https://www.facebook.com/sharer/sharer.php?s=100&amp;p[title]=LaBàn.vn%20-%20Danh%20bạ%20Internet%20Việt%20Nam&amp;p[url]=http://www.laban.vn/&amp;p[images][0]=http://s180.avatar.zdn.vn/180/2/f/f/c/laban_180_4.jpg&amp;p[summary]=LaBàn.vn là công cụ định hướng tìm kiếm web trên internet giúp bạn dễ dàng tìm thấy website mình cần một cách nhanh chóng, tiện lợi và đáng tin cậy." target="_blank" title="Chia sẻ trên Facebook">
                            <img width="16" height="16" src="http://stc.laban.vn/v2/images/icon_facebook.png" alt="Facebook">
                        </a>
                        <a href="https://plus.google.com/share?url=http://www.laban.vn" target="_blank" title="Chia sẻ trên Google">
                            <img width="24" height="15" src="http://stc.laban.vn/v2/images/icon_googleplus.jpg" alt="Google Plus">
                        </a>
                        <a href="http://link.apps.zing.vn/share?u=http://www.laban.vn&amp;t=LaBàn.vn%20-%20Danh%20bạ%20Internet%20Việt%20Nam&amp;desc=LaBàn.vn là công cụ định hướng tìm kiếm web trên internet giúp bạn dễ dàng tìm thấy website mình cần một cách nhanh chóng, tiện lợi và đáng tin cậy.&amp;images=http://s180.avatar.zdn.vn/180/2/f/f/c/laban_180_4.jpg" target="_blank" title="Chia sẻ trên Zing Me">
                            <img width="16" height="16" src="http://stc.laban.vn/v2/images/small_zing_icon.png" alt="Zing Me">
                        </a>
                    </li>
                </ul>
             </div>';
	}
}