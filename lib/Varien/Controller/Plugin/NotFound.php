<?php

Zend::loadClass('Zend_Controller_Plugin_Abstract');

class Varien_Controller_Plugin_NotFound extends Zend_Controller_Plugin_Abstract
{
    public function preDispatch(Zend_Controller_Request_Abstract $request) {
        $dispatcher = Zend_Controller_Front::getInstance()->getDispatcher();
        if (!$dispatcher->isDispatchable($request)) {
            $request->setControllerName(Zend_Controller_Front::getInstance()->getDefaultControllerName())
                    ->setModuleName('default')
                    ->setActionName('noRoute')
                    ->setDispatched(false);
        }
    }
}