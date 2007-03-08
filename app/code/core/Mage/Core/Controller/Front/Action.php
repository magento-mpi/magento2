<?php

class Mage_Core_Controller_Front_Action extends Mage_Core_Controller_Zend_Action 
{
    /**
     * Dispatches event before action
     */
    function preDispatch()
    {
        if ($this->getFlag('', 'no-preDispatch')) {
            return;
        }

        Mage::dispatchEvent('action_preDispatch');

        Mage::dispatchEvent('action_preDispatch_'.
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

        Mage::dispatchEvent('action_postDispatch'.
            $this->getRequest()->getModuleName().'_'.
            $this->getRequest()->getControllerName().'_'.
            $this->getRequest()->getActionName()
        );

        Mage::dispatchEvent('action_postDispatch');
    }

    function norouteAction()
    {
        //$this->getResponse()->setHeader('HTTP/1.0 404 Not Found');
        Mage::dispatchEvent('action_noRoute');
    }

}