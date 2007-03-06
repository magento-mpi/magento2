<?php

abstract class Mage_Core_Controller_Zend_Admin_Action extends Zend_Controller_Action
{
    /**
     * Enter description here...
     *
     * @var Zend_view
     */
    protected $_view;

    public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array())
    {
        parent::__construct($request, $response, $invokeArgs);
        $this->_view = Zend::registry('view');
    }
}