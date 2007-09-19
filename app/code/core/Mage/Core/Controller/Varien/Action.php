<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Custom Zend_Controller_Action class (formally)
 *
 * Allows dispatching before and after events for each controller action
 *
 * @author Moshe Gurvich <moshe@varien.com>
 */
abstract class Mage_Core_Controller_Varien_Action
{
    const FLAG_NO_CHECK_INSTALLATION    = 'no-install-check';
    const FLAG_NO_DISPATCH              = 'no-dispatch';
    const FLAG_NO_PRE_DISPATCH          = 'no-preDispatch';
    const FLAG_NO_POST_DISPATCH         = 'no-postDispatch';
    const FLAG_NO_DISPATCH_BLOCK_EVENT  = 'no-beforeGenerateLayoutBlocksDispatch';
    
    /**
     * Request object
     *
     * @var Zend_Controller_Request_Abstract
     */
    protected $_request;
    
    /**
     * Response object
     *
     * @var Zend_Controller_Response_Abstract
     */
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
    public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array())
    {
        $this->_request = $request;
        $this->_response= $response;

        if (!Mage::registry('action')) {
            Mage::register('action', $this);
        }

        $this->getLayout()->setArea('frontend');

        $this->_construct();
        
		Varien_Profiler::start('init/session');
		Mage::getSingleton('core/session');
		Varien_Profiler::stop('init/session');

		Mage::getConfig()->loadEventObservers($this->getLayout()->getArea());
		
        Varien_Profiler::start('translate/init');
        Mage::getSingleton('core/translate')->init($this->getLayout()->getArea());
        Varien_Profiler::stop('translate/init');
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
    
    /**
     * Retrieve flag value
     *
     * @param   string $action
     * @param   string $flag
     * @return  bool
     */
    function getFlag($action, $flag='')
    {
        if (''===$action) {
            $action = $this->getRequest()->getActionName();
        }
        if (''===$flag) {
            return $this->_flags;
        } 
        elseif (isset($this->_flags[$action][$flag])) {
            return $this->_flags[$action][$flag];
        } 
        else {
            return false;
        }
    }
    
    /**
     * Setting flag value
     *
     * @param   string $action
     * @param   string $flag
     * @param   string $value
     * @return  Mage_Core_Controller_Varien_Action
     */
    function setFlag($action, $flag, $value)
    {
        if (''===$action) {
            $action = $this->getRequest()->getActionName();
        }
        $this->_flags[$action][$flag] = $value;
        return $this;
    }
    
    /**
     * Retrieve full bane of current action current controller and
     * current module
     *
     * @param   string $delimiter
     * @return  string
     */
    function getFullActionName($delimiter='_')
    {
        return $this->getRequest()->getModuleName().$delimiter.
            $this->getRequest()->getControllerName().$delimiter.
            $this->getRequest()->getActionName();
    }

    /**
     * Retrieve current layout object
     *
     * @return Mage_Core_Model_Layout
     */
    function getLayout()
    {
        return Mage::getSingleton('core/layout');
    }
    
    /**
     * Load layout by identifier
     *
     * @param   string $ids
     * @param   string $key
     * @param   boolean $generateBlocks
     * @return  Mage_Core_Controller_Varien_Action
     */
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
        
        $key = Mage::getDesign()->getPackageName().'_'.$key;

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
        
		if(!$this->getFlag('', self::FLAG_NO_DISPATCH_BLOCK_EVENT)) {
        	Mage::dispatchEvent('beforeGenerateLayoutBlocks', array('layout'=>$layout));
		}
				
        if ($generateBlocks) {
            Varien_Profiler::start("$_profilerKey/blocks");
            $layout->generateBlocks();
            Varien_Profiler::stop("$_profilerKey/blocks");
        }

        return $this;
    }
    
    /**
     * Rendering layout
     *
     * @param   string $output
     * @return  Mage_Core_Controller_Varien_Action
     */
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

        $this->preDispatch();

        if ($this->getRequest()->isDispatched()) {
            // preDispatch() didn't change the action, so we can continue
            if (!$this->getFlag('', self::FLAG_NO_DISPATCH)) {
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
        if ($this->getFlag('', self::FLAG_NO_PRE_DISPATCH)) {
            return;
        }
        
        if (!$this->getFlag('', self::FLAG_NO_CHECK_INSTALLATION)) {
            if (!Mage::getSingleton('install/installer')->isApplicationInstalled()) {
                $this->setFlag('', self::FLAG_NO_DISPATCH, true);
                $this->_redirect('install');
                return;
            }
        }
        
        $_profilerKey = 'ctrl/dispatch/'.$this->getFullActionName().'/pre';
        Varien_Profiler::start($_profilerKey);
        Mage::dispatchEvent('action_preDispatch', array('controller_action'=>$this));
        Mage::dispatchEvent('action_preDispatch_'.$this->getRequest()->getModuleName(), array('controller_action'=>$this));
        Mage::dispatchEvent('action_preDispatch_'.$this->getFullActionName(), array('controller_action'=>$this));
        Varien_Profiler::stop($_profilerKey);
    }

    /**
     * Dispatches event after action
     */
    function postDispatch()
    {
        if ($this->getFlag('', self::FLAG_NO_POST_DISPATCH)) {
            return;
        }
        $_profilerKey = 'ctrl/dispatch/'.$this->getFullActionName().'/post';
        Varien_Profiler::start($_profilerKey);
        
        Mage::dispatchEvent('action_postDispatch_'.$this->getFullActionName(), array('controller_action'=>$this));
        Mage::dispatchEvent('action_postDispatch_'.$this->getRequest()->getModuleName(), array('controller_action'=>$this));
        Mage::dispatchEvent('action_postDispatch', array('controller_action'=>$this));
        
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
            $this->getLayout()->getMessagesBlock()->addMessages($storage->getMessages(true));
        }
        else {
            Mage::throwException('Invalid messages storage');
        }
        return $this;
    }
}