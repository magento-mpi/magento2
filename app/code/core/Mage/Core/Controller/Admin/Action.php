<?php

class Mage_Core_Controller_Admin_Action extends Mage_Core_Controller_Zend_Action 
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

    /**
     * Dispatches event before action
     */
    function preDispatch()
    {
        if ($this->getFlag('', 'no-preDispatch')) {
            return;
        }

        Mage::dispatchEvent('admin_action_preDispatch');

        Mage::dispatchEvent('admin_action_preDispatch_'.
            $this->getRequest()->getModuleName().'_'.
            $this->getRequest()->getControllerName().'_'.
            $this->getRequest()->getActionName()
        );
    }

    /**
     * Dispatches event after action
     */
    function postDispatch()
    {
        if ($this->getFlag('', 'no-postDispatch')) {
            return;
        }

        Mage::dispatchEvent('admin_action_postDispatch'.
            $this->getRequest()->getModuleName().'_'.
            $this->getRequest()->getControllerName().'_'.
            $this->getRequest()->getActionName()
        );

        Mage::dispatchEvent('admin_action_postDispatch');
    }

    function norouteAction()
    {
        //$this->getResponse()->setHeader('HTTP/1.0 404 Not Found');
        Mage::dispatchEvent('admin_action_noRoute');
    }

}