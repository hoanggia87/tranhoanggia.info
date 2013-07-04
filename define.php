<?php
//Duong dan den thu muc chua ung dung
defined('')
	|| define('APPLICATION_PATH', 
			  realpath(dirname(__FILE__) . '/application'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV',
              (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV')
                                         : 'developer'));
			  
//Nap duong dan den cac thu vien se su dung trong ung dung
set_include_path(implode(PATH_SEPARATOR, array(
    APPLICATION_PATH.'/../library'.PATH_SEPARATOR.
    get_include_path(),
)));

define('DOMAIN_NAME','http://www.tranhoanggia.info');
//url applycation
define('URL_APPLICATION',DOMAIN_NAME.'');
define('MODULES_PATH',APPLICATION_PATH.'/modules');
//Duong dan den thu muc /public
define('PUBLIC_PATH', realpath(dirname(__FILE__) . '/public'));
define('PUBLIC_URL',URL_APPLICATION . '/public');
define('FILES_PATH',PUBLIC_PATH.'/files');



define('DEFAULT_TEMPLATE','shockvl');
//Duong dan den thu muc /templates
define('TEMPLATE_PATH', PUBLIC_PATH . '/templates');
define('DEFAULT_TEMPLATE','shockvl');
define('DEFAULT_TEMPLATE_MOBILE','shockvl_mobile');
//Duong dan den thu muc /templates admin
define('DEFAULT_ADMIN_TEMPLATE','katniss');


//Duong dan den thu muc /templates
define('TEMPLATE_URL', '/public/templates');
define('SENDER_EMAIL','saritvn@gmail.com');

//slogan đính dưới mỗi hình
define('SLOGAN', '');

//link fanpage
define('FANPAGE', 'https://www.facebook.com/shockvl.shockdentanoc');
