<?php

#include_once 'Ecom/Core/Controller/Zend/Action.php';

class IndexController extends Ecom_Core_Controller_Action {

    function indexAction()
    {
        #$this->_forward('index', 'index', $this->getModuleInfo()->getConfig('controller')->default);
    }
    
    function __call($action, $args) 
    {
//        $request = $this->getRequest();
//        $params = $request->getParams();
    }


}