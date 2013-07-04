$(document).ready(function(){
    /*Them phan auto show cho dropdown menu*/
      var position = $('#menu_hover').position(0);
      var plus_timeout = 500;var plus_closetimer = 0;  var plus_ddmenuitem = false;
      function jsddm_open()
      {
            jsddm_canceltimer();
            jsddm_close();
            $("#top_pullmenu").show();
            plus_ddmenuitem = true; 
            $('#menu_hover').addClass('menu_hoved');
            $('#top_pullmenu').css('left',position.left-80);
      }
      function jsddm_close()
      {	
        $("#top_pullmenu").hide();
        plus_ddmenuitem = false; 
        $('#menu_hover').removeClass('menu_hoved')
      }
      function jsddm_timer()
      {	
        plus_closetimer = window.setTimeout(jsddm_close, plus_timeout);
      }
      function jsddm_canceltimer()
      {	
        if(plus_closetimer)	
            {window.clearTimeout(plus_closetimer);plus_closetimer = null;}
      }
      $('#menu_hover').bind('mouseover', jsddm_open);
      $('#top_pullmenu').bind('mouseover', jsddm_open);
      $('#menu_hover').bind('mouseout',  jsddm_timer);
      $('#top_pullmenu').bind('mouseout',  jsddm_timer);
      /*End auto dropdown menu*/
})