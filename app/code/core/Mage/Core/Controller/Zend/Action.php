<?php

/**
 * Custom Zend_Controller_Action class
 *
 * Allows dispatching before and after events for each controller action
 *
 * @author Moshe Gurvich <moshe@varien.com>
 */
abstract class Mage_Core_Controller_Zend_Action extends Zend_Controller_Action
{
     /**
      * Action flags
      *
      * for example used to disable rendering default layout
      *
      * @var array
      */
     protected $_flags = array();
     protected $_view = null;

     public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array())
     {
         parent::__construct($request, $response, $invokeArgs);
         $this->_view = new Zend_View();
     }
     
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
     
     function getFullActionName()
     {
         return $this->getRequest()->getModuleName().'_'.
            $this->getRequest()->getControllerName().'_'.
            $this->getRequest()->getActionName();
     }
     
     function renderLayout($name='root', $method='toString')
     {
        Mage::dispatchEvent('beforeRenderLayout');
        Mage::dispatchEvent('beforeRenderLayout_'.$this->getFullActionName());
        
        $this->getResponse()->setBody(Mage::getBlock($name)->$method());
     }
     
     static function renderLayoutStatic($name='root', $method='toString')
     {
        $request = Mage::getController()->getRequest();
        Mage::dispatchEvent('beforeRenderLayout');
        Mage::dispatchEvent('beforeRenderLayout_'.$request->getModuleName().'_'.$request->getControllerName().'_'.$request->getActionName());
        
        Mage::getController()->getFront()->getResponse()->setBody(Mage::getBlock($name)->$method());
     }
     
    /**
     * Dispatches event before action
     */
    function preDispatch()
    {
        if ($this->getFlag('', 'no-preDispatch')) {
            return;
        }

        Mage::dispatchEvent('action_preDispatch');
        Mage::dispatchEvent('action_preDispatch_'.$this->getFullActionName());
    }

    /**
     * Dispatches event after action
     */
    function postDispatch()
    {
        if ($this->getFlag('', 'no-postDispatch')) {
            return;
        }

        Mage::dispatchEvent('action_postDispatch'.$this->getFullActionName());
        Mage::dispatchEvent('action_postDispatch');
    }

    function norouteAction()
    {
        //$this->getResponse()->setHeader('HTTP/1.0 404 Not Found');
        Mage::dispatchEvent('action_noRoute');
    }
}