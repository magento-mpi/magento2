<?php

/**
 * Custom Zend_Controller_Action class (formally)
 *
 * Allows dispatching before and after events for each controller action
 *
 * @author Moshe Gurvich <moshe@varien.com>
 */
abstract class Mage_Core_Controller_Varien_Action
	# extends Zend_Controller_Action
{
    protected $_request;
    protected $_response;

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
     public function __construct(
     	Zend_Controller_Request_Abstract $request,
     	Zend_Controller_Response_Abstract $response,
     	array $invokeArgs = array())
     {
         $this->_request = $request;
         $this->_response = $response;
         #Zend_Controller_Action_HelperBroker::resetHelpers();
         #parent::__construct($request, $response, $invokeArgs);

         if (!Mage::registry('action')) {
             Mage::register('action', $this);
         }

         $this->getLayout()->setArea('frontend');

         $this->_construct();

		 Mage::getConfig()->loadEventObservers($this->getLayout()->getArea());
     }

     protected function _construct()
     {

     }
     
    /**
     * Retrieve request object
     *
     * @return Zend_Controller_Request_Abstract
     */
    function getRequest()
    {
        return $this->_request;
    }

     /**
      * Retrieve response object
      *
      * @return Zend_Controller_Response_Abstract
      */
     function getResponse()
     {
         return $this->_response;
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

     function getFullActionName($delimiter='_')
     {
         return $this->getRequest()->getModuleName().$delimiter.
         $this->getRequest()->getControllerName().$delimiter.
         $this->getRequest()->getActionName();
     }

     /**
      * Get current layout
      *
      * @return Mage_Core_Model_Layout
      */
     function getLayout()
     {
         return Mage::getSingleton('core/layout');
     }

     function loadLayout($ids=null, $key='', $generateBlocks=true)
     {
        $area = $this->getLayout()->getArea();
        $_profilerKey = 'ctrl/dispatch/'.$this->getFullActionName();
        Varien_Profiler::start("$_profilerKey/load");

        if (''===$key) {
            if (is_array($ids)) {
                Mage::exception('Mage_Core', 'Please specify key for loadLayout('.$area.', Array('.join(',',$ids).'))');
            }
            $key = $area.'_'.$ids;
        }

        if (is_null($ids)) {
            $ids = array('default', $this->getFullActionName());
        } elseif (is_string($ids)) {
            $ids = array($ids);
        }

        $layout = $this->getLayout()->init($key);

        if (!$layout->getNode()) {
            foreach ($ids as $id) {
                $layout->loadUpdatesFromConfig($area, $id);
            }
            $layout->saveCache();
        }
        Varien_Profiler::stop("$_profilerKey/load");

        Mage::dispatchEvent('beforeGenerateLayoutBlocks', array('layout'=>$layout));

        if ($generateBlocks) {
            Varien_Profiler::start("$_profilerKey/blocks");
            $layout->generateBlocks();
            Varien_Profiler::stop("$_profilerKey/blocks");
        }

        return $this;
     }

     function renderLayout($output='')
     {
         $_profilerKey = 'ctrl/dispatch/'.$this->getFullActionName();
         Varien_Profiler::start("$_profilerKey/render");

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
         Varien_Profiler::stop("$_profilerKey/render");

         return $this;
     }

    public function dispatch($action)
    {
    	$actionMethodName = $this->getActionMethodName($action);

        if (!is_callable(array($this, $actionMethodName))) {
            $actionMethodName = 'norouteAction';
        }

        Mage::log('Request Uri:'.$this->getRequest()->getRequestUri());
        Mage::log('Request Params:');
        Mage::log($this->getRequest()->getParams());

        $this->preDispatch();

        if ($this->getRequest()->isDispatched()) {
            // preDispatch() didn't change the action, so we can continue
            if (!$this->getFlag('', 'no-dispatch')) {
                $_profilerKey = 'ctrl/dispatch/'.$this->getFullActionName();
                Varien_Profiler::start($_profilerKey);
                $this->$actionMethodName();
                Varien_Profiler::stop($_profilerKey);
            }

            $this->postDispatch();

        }
    }

    public function getActionMethodName($action)
    {
        $method = $action.'Action';
        return $method;
    }

    /**
     * Dispatches event before action
     */
    function preDispatch()
    {
        if ($this->getFlag('', 'no-preDispatch')) {
            return;
        }
        $_profilerKey = 'ctrl/dispatch/'.$this->getFullActionName().'/pre';
        Varien_Profiler::start($_profilerKey);
        Mage::dispatchEvent('action_preDispatch', array('controller_action'=>$this));
        Mage::dispatchEvent('action_preDispatch_'.$this->getRequest()->getModuleName(),
        	array('controller_action'=>$this));
        Mage::dispatchEvent('action_preDispatch_'.$this->getFullActionName(),
        	array('controller_action'=>$this));
        Varien_Profiler::stop($_profilerKey);
    }

    /**
     * Dispatches event after action
     */
    function postDispatch()
    {
        if ($this->getFlag('', 'no-postDispatch')) {
            return;
        }
        $_profilerKey = 'ctrl/dispatch/'.$this->getFullActionName().'/post';
        Varien_Profiler::start($_profilerKey);
        Mage::dispatchEvent('action_postDispatch_'.$this->getFullActionName(),
        	array('controller_action'=>$this));
        Mage::dispatchEvent('action_postDispatch_'.$this->getRequest()->getModuleName(),
        	array('controller_action'=>$this));
        Mage::dispatchEvent('action_postDispatch',
        	array('controller_action'=>$this));
        Varien_Profiler::stop($_profilerKey);
    }

    function norouteAction($coreRoute = null)
    {
        $status = ( $this->getRequest()->getParam('__status__') )
        	? $this->getRequest()->getParam('__status__')
        	: new Varien_Object();

        Mage::dispatchEvent('action_noRoute', array('action'=>$this, 'status'=>$status));
        if ($status->getLoaded() !== true
        	|| $status->getForwarded() === true
        	|| !is_null($coreRoute) ) {
            $this->loadLayout(array('default', 'noRoute'), 'noRoute');
            $this->renderLayout();
        } else {
            $status->setForwarded(true);
            #$this->_forward('cmsNoRoute', 'index', 'cms');
            $this->_forward(
            	$status->getForwardAction(),
            	$status->getForwardController(),
            	$status->getForwardModule(),
            	array('__status__' => $status));
        }
    }

    protected function _forward($action, $controller = null, $module = null, array $params = null)
    {
        $request = $this->getRequest();

        if (!is_null($params)) {
            $request->setParams($params);
        }

        if (!is_null($controller)) {
            $request->setControllerName($controller);

            // Module should only be reset if controller has been specified
            if (!is_null($module)) {
                $request->setModuleName($module);
            }
        }

        $request->setActionName($action)
                ->setDispatched(false);

    }

    protected function _redirect($path, $arguments=array())
    {
        $this->getResponse()->setRedirect(Mage::getUrl($path, $arguments));
    }

    /**
     * Redirect to success page
     *
     * @param string $defaultUrl
     */
    protected function _redirectSuccess($defaultUrl)
    {
        $successUrl = $this->getRequest()->getParam('success_url');
        if (empty($successUrl)) {
            $successUrl = $defaultUrl;
        }
        $this->getResponse()->setRedirect($successUrl);
    }

    /**
     * Redirect to error page
     *
     * @param string $defaultUrl
     */
    protected function _redirectError($defaultUrl)
    {
        $errorUrl = $this->getRequest()->getParam('error_url');
        if (empty($errorUrl)) {
            $errorUrl = $defaultUrl;
        }
        $this->getResponse()->setRedirect($errorUrl);
    }

    protected function _initLayoutMessages($messagesStorage)
    {
        if ($storage = Mage::getSingleton($messagesStorage)) {
            $this->getLayout()->getMessagesBlock()->setMessages(
                $storage->getMessages(true)
            );
        }
        else {
            Mage::throwException('Invalid messages storage');
        }
        return $this;
    }
}