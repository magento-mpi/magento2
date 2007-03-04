<?php

#include_once 'Zend/Controller/Action.php';

/**
 * Custom Zend_Controller_Action class
 *
 * Allows dispatching before and after events for each controller action
 *
 * @author Moshe Gurvich <moshe@varien.com>
 */
abstract class Ecom_Core_Controller_Zend_Action extends Zend_Controller_Action
{
     /**
      * Action flags
      *
      * for example used to disable rendering default layout
      *
      * @var array
      */
     protected $_flags = array();

     function getFlag($action, $flag='')
     {
         if (''===$action) {
             $action = $this->getRequest()->getActionName();
         }
         if (''===$flag) {
             return $this->_flags;
         } elseif (isset($this->_flags[$action][$flag])) {
             return $this->_flags[$action][$flag];
         } else {
             return false;
         }
     }

     function setFlag($action, $flag, $value)
     {
         if (''===$action) {
             $action = $this->getRequest()->getActionName();
         }
         $this->_flags[$action][$flag] = $value;
         return $this;
     }

	/**
     * Dispatches event before action
     */
    function preDispatch()
    {
        if ($this->getFlag('', 'no-preDispatch')) {
            return;
        }

        Ecom::dispatchEvent('controllerAction.preDispatch');

        Ecom::dispatchEvent(
            $this->getRequest()->getModuleName().'/'.
            $this->getRequest()->getControllerName().'/'.
            $this->getRequest()->getActionName().'/before'
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

        Ecom::dispatchEvent(
            $this->getRequest()->getModuleName().'/'.
            $this->getRequest()->getControllerName().'/'.
            $this->getRequest()->getActionName().'/after'
        );

        Ecom::dispatchEvent('controllerAction.postDispatch');
    }

    function norouteAction()
    {
        //$this->getResponse()->setHeader('HTTP/1.0 404 Not Found');
        Ecom::dispatchEvent('controllerAction.noRoute');
    }

}