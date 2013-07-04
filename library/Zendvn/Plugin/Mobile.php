<?php
    class Zendvn_Plugin_Mobile extends Zend_Controller_Plugin_Abstract
    {

        public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
        {
            $contextSwitch  =   Zend_Controller_Action_HelperBroker::getStaticHelper('ContextSwitch');
            $contextSwitch->clearContexts()
            ->setContext(
            	'mobile',array(
            		'suffix'	=> 'mobile',
            		'headers'	=> array(
            			'Content-Type' => 'text/html;Charset=UTF-8'
            		)
            	)            
            )
            ->setContext(
            	'html', array(
            		'headers'	=> array(
            			'Content-Type'	=> 'text/html;Charset=UTF-8'
            		)
            	)
            )
            ->setAutoDisableLayout(false)
            ->setDefaultContext('html')
            ->initContext();
			$bootstrap = Zend_Controller_Front::getInstance()->getParam('bootstrap');
	        $userAgent = $bootstrap->getResource('useragent');			
            
			switch($userAgent->getDevice()->getFeature('is_mobile'))
			{
				case 1:
						$request->setParam('format','mobile');
					break;				
				default:
						$request->setParam('format','html');			
			}
            
        }
        public function getFromRequest()
        {}
    }
