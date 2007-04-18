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
     protected $_blocks = null;

     public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array())
     {
         parent::__construct($request, $response, $invokeArgs);
          
         $this->_construct();
     }
     
     protected function _construct()
     {
          
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
     
     function getLayout()
     {
         return Mage::getSingleton('core', 'layout');
     }
     
     function loadLayout($area, $ids, $key='')
     {
        Varien_Profiler::setTimer('loadLayout');
         
		if (''===$key) {
		    if (is_array($ids)) {
		        Mage::exception('Please specify key for loadLayout('.$area.', Array('.join(',',$ids).'))');
		    }
		    $key = $area.'_'.$ids;
		}
		 
		$layout = $this->getLayout()->init($key);
		
		if (!$layout->isCacheLoaded()) {
		    foreach ((array)$ids as $id) {
		        $layout->loadUpdatesFromConfig($area, $id);
		    }
		    $layout->saveCache();
		}
		Varien_Profiler::setTimer('loadLayout', true);
		
		Varien_Profiler::setTimer('createBlocks');
		$layout->createBlocks();
		Varien_Profiler::setTimer('createBlocks', true);
		
		return $this;
     }
     
     function renderLayout($output='')
     {
         Varien_Profiler::setTimer('renderLayout');
         
         if ($this->getFlag('', 'no-renderLayout')) {
             return;
         }

         if (''!==$output) {
             Mage::registry('blocks')->addOutputBlock($output);
         }
          
         Mage::dispatchEvent('beforeRenderLayout');
         Mage::dispatchEvent('beforeRenderLayout_'.$this->getFullActionName());
         
         $blocks = Mage::registry('blocks')->getOutputBlocks();

         if (!empty($blocks)) {
             $response = $this->getResponse();
             foreach ($blocks as $callback) {
                 $out = Mage::getBlock($callback[0])->$callback[1]();
                 $response->appendBody($out);
             }
         }
         
         Varien_Profiler::setTimer('renderLayout', true);
         
         return $this;
     }
     
    public function dispatch($action)
    {
        Varien_Profiler::setTimer('preDispatch');
        $this->preDispatch();
        Varien_Profiler::setTimer('preDispatch', true);
        if ($this->getRequest()->isDispatched()) {
            // preDispatch() didn't change the action, so we can continue
            if (!$this->getFlag('', 'no-dispatch')) {
                Varien_Profiler::setTimer('actionDispatch');
                $this->$action();
                Varien_Profiler::setTimer('actionDispatch', true);
            }
            Varien_Profiler::setTimer('postDispatch');
            $this->postDispatch();
            Varien_Profiler::setTimer('postDispatch', true);
        }
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
        
        Mage::dispatchEvent('action_postDispatch_'.$this->getFullActionName());
        Mage::dispatchEvent('action_postDispatch');
    }

    function norouteAction()
    {
        //$this->getResponse()->setHeader('HTTP/1.0 404 Not Found');
        Mage::dispatchEvent('action_noRoute');
    }
}