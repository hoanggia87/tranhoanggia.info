<?php
class Zendvn_Plugin_Uploadify extends Zend_Controller_Plugin_Abstract
{
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $phpSessId = $request->getParam('PHPSESSID');
        if (!empty($phpSessId) && session_id() != $phpSessId) {
            session_destroy();
            session_id($phpSessId);
            session_start();
        }
    }
}