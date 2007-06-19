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

     /**
      * Cunstructor
      *
      * @param Zend_Controller_Request_Abstract $request
      * @param Zend_Controller_Response_Abstract $response
      * @param array $invokeArgs
      * @todo  remove Mage::register('action', $this);
      */
     public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array())
     {
         Zend_Controller_Action_HelperBroker::resetHelpers();
         parent::__construct($request, $response, $invokeArgs);

         if (!Mage::registry('action')) {
             Mage::register('action', $this);
         }

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
         return Mage::getSingleton('core/layout');
     }

     function loadLayout($ids=null, $key='', $generateBlocks=true)
     {
        $area = 'front';
        Varien_Profiler::start('loadLayout');

        if (''===$key) {
            if (is_array($ids)) {
                Mage::exception('Please specify key for loadLayout('.$area.', Array('.join(',',$ids).'))');
            }
            $key = $area.'_'.$ids;
        }

        if (is_null($ids)) {
            $ids = array('default', $this->getFullActionName());
        } elseif (is_string($ids)) {
            $ids = array($ids);
        }

        $layout = $this->getLayout()->init($key);

        if (!$layout->getCache()->getIsLoaded()) {
            foreach ($ids as $id) {
                $layout->loadUpdatesFromConfig($area, $id);
            }
            $layout->getCache()->save();
        }
        Varien_Profiler::stop('loadLayout');

        if ($generateBlocks) {
            Varien_Profiler::start('generateBlocks');
            $layout->generateBlocks();
            Varien_Profiler::stop('generateBlocks');
        }

        return $this;
     }

     function renderLayout($output='')
     {
         Varien_Profiler::start('renderLayout');

         if ($this->getFlag('', 'no-renderLayout')) {
             return;
         }

         if (''!==$output) {
             $this->getLayout()->addOutputBlock($output);
         }

         Mage::dispatchEvent('beforeRenderLayout');
         Mage::dispatchEvent('beforeRenderLayout_'.$this->getFullActionName());

         $output = $this->getLayout()->getOutput();

         $this->getResponse()->appendBody($output);
         Varien_Profiler::stop('renderLayout');

         return $this;
     }

    public function dispatch($action)
    {
        Mage::log('Request Uri:'.$this->getRequest()->getRequestUri());
        Mage::log('Request Params:');
        Mage::log($this->getRequest()->getParams());
        Varien_Profiler::start('preDispatch');
        $this->preDispatch();
        Varien_Profiler::stop('preDispatch');
        if ($this->getRequest()->isDispatched()) {
            // preDispatch() didn't change the action, so we can continue
            if (!$this->getFlag('', 'no-dispatch')) {
                Varien_Profiler::start('actionDispatch');
                $this->$action();
                Varien_Profiler::stop('actionDispatch');
            }
            Varien_Profiler::start('postDispatch');
            $this->postDispatch();
            Varien_Profiler::stop('postDispatch');
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

        Mage::dispatchEvent('action_preDispatch', array('controller_action'=>$this));
        Mage::dispatchEvent('action_preDispatch_'.$this->getRequest()->getModuleName());
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
        Mage::dispatchEvent('action_postDispatch_'.$this->getRequest()->getModuleName());
        Mage::dispatchEvent('action_postDispatch', array('controller_action'=>$this));
    }

    function norouteAction($coreRoute = null)
    {
        $status = ( $this->getRequest()->getParam('__status__') ) ? $this->getRequest()->getParam('__status__') : new Varien_Object();
        Mage::dispatchEvent('action_noRoute', array('action'=>$this, 'status'=>$status));
        if( $status->getLoaded() !== true || $status->getForwarded() === true || !is_null($coreRoute) ) {
            $this->loadLayout(array('default', 'noRoute'), 'noRoute');
            $this->renderLayout();
        } else {
            $status->setForwarded(true);
            $this->_forward($status->getForwardAction(), $status->getForwardController(), $status->getForwardModule(), array('__status__' => $status));
        }
    }
}