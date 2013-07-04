<?php
class Block_ShowMenu extends Zend_View_Helper_Abstract{
	
	public function ShowMenu()
    {
        echo '<div id="nav-top">
                <div class="wrapper">
                    <div class="content">
                        <ul class="menuchild">
                        <li id="weather_location" class="weather_location">
                            <a href="javascript:;" title="" class="city" id="cityName">ĐIỆN BIÊN</a>
                            <ul id="cityList" style="display: none;"><li><a href="javascript:;" rel="1">HỒ CHÍ MINH</a></li><li><a href="javascript:;" rel="2">HÀ NỘI</a></li><li><a href="javascript:;" rel="3">HẢI PHÒNG</a></li><li><a href="javascript:;" rel="4">TT HUẾ</a></li><li><a href="javascript:;" rel="5">ĐÀ NẴNG</a></li><li><a href="javascript:;" rel="6">CẦN THƠ</a></li><li><a href="javascript:;" rel="7">AN GIANG</a></li><li><a href="javascript:;" rel="8">BR-VŨNG TÀU</a></li><li><a href="javascript:;" rel="9">BẮC CẠN</a></li><li><a href="javascript:;" rel="10">BẮC GIANG</a></li><li><a href="javascript:;" rel="11">BẠC LIÊU</a></li><li><a href="javascript:;" rel="12">BẮC NINH</a></li><li><a href="javascript:;" rel="13">BẾN TRE</a></li><li><a href="javascript:;" rel="14">BÌNH ĐỊNH</a></li><li><a href="javascript:;" rel="15">BÌNH DƯƠNG</a></li><li><a href="javascript:;" rel="17">BÌNH THUẬN</a></li><li><a href="javascript:;" rel="16">BÌNH PHƯỚC</a></li><li><a href="javascript:;" rel="19">CAO BẰNG</a></li><li><a href="javascript:;" rel="18">CÀ MAU</a></li><li><a href="javascript:;" rel="21">ĐĂK NÔNG</a></li><li><a href="javascript:;" rel="20">ĐĂK LĂK</a></li><li><a href="javascript:;" rel="23">ĐỒNG NAI</a></li><li><a href="javascript:;" rel="22">ĐIỆN BIÊN</a></li><li><a href="javascript:;" rel="25">GIA LAI</a></li><li><a href="javascript:;" rel="24">ĐỒNG THÁP</a></li><li><a href="javascript:;" rel="27">HÀ NAM</a></li><li><a href="javascript:;" rel="26">HÀ GIANG</a></li><li><a href="javascript:;" rel="29">HÀ TĨNH</a></li><li><a href="javascript:;" rel="31">HẬU GIANG</a></li><li><a href="javascript:;" rel="30">HẢI DƯƠNG</a></li><li><a href="javascript:;" rel="34">KHÁNH HÒA</a></li><li><a href="javascript:;" rel="35">KIÊN GIANG</a></li><li><a href="javascript:;" rel="32">HÒA BÌNH</a></li><li><a href="javascript:;" rel="33">HƯNG YÊN</a></li><li><a href="javascript:;" rel="38">LÂM ĐỒNG</a></li><li><a href="javascript:;" rel="39">LẠNG SƠN</a></li><li><a href="javascript:;" rel="36">KON TUM</a></li><li><a href="javascript:;" rel="37">LAI CHÂU</a></li><li><a href="javascript:;" rel="42">NAM ĐỊNH</a></li><li><a href="javascript:;" rel="43">NGHỆ AN</a></li><li><a href="javascript:;" rel="40">LÀO CAI</a></li><li><a href="javascript:;" rel="41">LONG AN</a></li><li><a href="javascript:;" rel="46">PHÚ THỌ</a></li><li><a href="javascript:;" rel="47">PHÚ YÊN</a></li><li><a href="javascript:;" rel="44">NINH BÌNH</a></li><li><a href="javascript:;" rel="45">NINH THUẬN</a></li><li><a href="javascript:;" rel="51">QUẢNG NINH</a></li><li><a href="javascript:;" rel="50">QUẢNG NGÃI</a></li><li><a href="javascript:;" rel="49">QUẢNG NAM</a></li><li><a href="javascript:;" rel="48">QUẢNG BÌNH</a></li><li><a href="javascript:;" rel="55">TÂY NINH</a></li><li><a href="javascript:;" rel="54">SƠN LA</a></li><li><a href="javascript:;" rel="53">SÓC TRĂNG</a></li><li><a href="javascript:;" rel="52">QUẢNG TRỊ</a></li><li><a href="javascript:;" rel="59">TIỀN GIANG</a></li><li><a href="javascript:;" rel="58">THANH HÓA</a></li><li><a href="javascript:;" rel="57">THÁI NGUYÊN</a></li><li><a href="javascript:;" rel="56">THÁI BÌNH</a></li><li><a href="javascript:;" rel="63">VĨNH PHÚC</a></li><li><a href="javascript:;" rel="62">VĨNH LONG</a></li><li><a href="javascript:;" rel="61">TUYÊN QUANG</a></li><li><a href="javascript:;" rel="60">TRÀ VINH</a></li><li><a href="javascript:;" rel="64">YÊN BÁI</a></li></ul>
                        </li>
                        <li class="weather"><a href="javascript:;" title="" id="weather_info"><img src="http://stc.laban.vn/v2/images/weather/300_2.png" width="21" height="21" alt=""> 20 ~ 33°C</a></li>
                        <li class="gold">
                            <a title="Click vào để biết thêm thông tin" href="javascript:;">
                                <span id="rate_gold">4.32</span> triệu/chỉ
                            </a>
                        </li>
                        <li class="money">
                            <a title="Click vào để biết thêm thông tin" href="javascript:void(0);"> 1 USD = <span id="rate_currency">20,860</span> </a>
                        </li>
                        <li class="mail">
                            <a href="#" title="" class="checkmail">Duyệt Mail</a>
        
                            <ul id="mailList">
                                <li><a target="_blank" href="http://mail.yahoo.com">Yahoo</a></li>
                                <li><a target="_blank" href="http://mail.google.com">Gmail</a></li>
                                <li><a target="_blank" href="http://mail.zing.vn">Zing Mail</a></li>
                                <li><a target="_blank" href="http://www.hotmail.com">Hotmail</a></li>
                            </ul>
        
                        </li>
                        <li class="date"><a href="http://www.thoigian.com.vn" target="_blank"><span id="todayDate">Thứ Năm ngày 18 tháng 04 (9/3 ÂL)</span></a></li>
                        <li class="bookmark"><a id="addBookmark" rel="sidebar" href="/" class="blue">Đặt Bookmark</a></li>
                    </ul>
                    </div>
                </div>
            </div>';
	}
}