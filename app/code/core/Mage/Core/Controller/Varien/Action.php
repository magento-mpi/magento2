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

    const PARAM_NAME_SUCCESS_URL        = 'success_url';
    const PARAM_NAME_ERROR_URL          = 'error_url';
    const PARAM_NAME_REFERER_URL        = 'referer_url';
    const PARAM_NAME_BASE64_URL         = 'referer_64';

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
     * Constructor
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

        Mage::app()->loadArea($this->getLayout()->getArea());
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
     * Load layout by handles(s)
     *
     * @param   string $handles
     * @param   string $cacheId
     * @param   boolean $generateBlocks
     * @return  Mage_Core_Controller_Varien_Action
     */
    public function loadLayout($handles=null, $generateBlocks=true, $generateXml=true)
    {
        $_profilerKey = 'ctrl/dispatch/'.$this->getFullActionName();

        // if handles were specified in arguments load them first
        $this->getLayout()->getUpdate()->addHandle($handles ? $handles : 'default');

        // add default layout handles for this action
        $this->addActionLayoutHandles();

        $this->loadLayoutUpdates();

		if (!$generateXml) {
		    return $this;
		}
        $this->generateLayoutXml();

        if (!$generateBlocks) {
            return $this;
        }
        $this->generateLayoutBlocks();

        return $this;
    }

    public function addActionLayoutHandles()
    {
        $update = $this->getLayout()->getUpdate();

        // load store handle
        $update->addHandle('STORE_'.Mage::app()->getStore()->getCode());

        // load theme handle
        $package = Mage::getSingleton('core/design_package');
        $update->addHandle('THEME_'.$package->getArea().'_'.$package->getPackageName().'_'.$package->getTheme('layout'));

        // load action handle
        $update->addHandle($this->getFullActionName());

        return $this;
    }

    public function loadLayoutUpdates()
    {
        $_profilerKey = 'ctrl/dispatch/'.$this->getFullActionName();

        // dispatch event for adding handles to layout update
    	Mage::dispatchEvent('beforeLoadLayout', array('action'=>$this, 'layout'=>$this->getLayout()));

    	// load layout updates by specified handles
        Varien_Profiler::start("$_profilerKey/load");
        $this->getLayout()->getUpdate()->load();
        Varien_Profiler::stop("$_profilerKey/load");

        return $this;
    }

    public function generateLayoutXml()
    {
        $_profilerKey = 'ctrl/dispatch/'.$this->getFullActionName();
        // dispatch event for adding text layouts
		if(!$this->getFlag('', self::FLAG_NO_DISPATCH_BLOCK_EVENT)) {
        	Mage::dispatchEvent('beforeGenerateLayoutXml', array('action'=>$this, 'layout'=>$this->getLayout()));
		}

		// generate xml from collected text updates
		Varien_Profiler::start("$_profilerKey/xml");
		$this->getLayout()->generateXml();
		Varien_Profiler::stop("$_profilerKey/xml");

        return $this;
    }

    public function generateLayoutBlocks()
    {
        $_profilerKey = 'ctrl/dispatch/'.$this->getFullActionName();
		// dispatch event for adding xml layout elements
		if(!$this->getFlag('', self::FLAG_NO_DISPATCH_BLOCK_EVENT)) {
        	Mage::dispatchEvent('beforeGenerateLayoutBlocks', array('action'=>$this, 'layout'=>$this->getLayout()));
		}

        // generate blocks from xml layout
        Varien_Profiler::start("$_profilerKey/blocks");
        $this->getLayout()->generateBlocks();
        Varien_Profiler::stop("$_profilerKey/blocks");

        return $this;
    }

    /**
     * Rendering layout
     *
     * @param   string $output
     * @return  Mage_Core_Controller_Varien_Action
     */
    public function renderLayout($output='')
    {
        $_profilerKey = 'ctrl/dispatch/'.$this->getFullActionName();
        Varien_Profiler::start("$_profilerKey/render");

        if ($this->getFlag('', 'no-renderLayout')) {
            Varien_Profiler::stop("$_profilerKey/render");
            return;
        }

        if (''!==$output) {
            $this->getLayout()->addOutputBlock($output);
        }

        Mage::dispatchEvent('beforeRenderLayout');
        Mage::dispatchEvent('beforeRenderLayout_'.$this->getFullActionName());

        #ob_implicit_flush();
        $this->getLayout()->setDirectOutput(false);

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
                $this->postDispatch();
            }
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
            if (!Mage::app()->isInstalled()) {
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
            $this->loadLayout(array('default', 'noRoute'));
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

    protected function _initLayoutMessages($messagesStorage)
    {
        if ($storage = Mage::getSingleton($messagesStorage)) {
            $this->getLayout()->getMessagesBlock()->addMessages($storage->getMessages(true));
        }
        else {
            Mage::throwException(__('Invalid messages storage "%s" for layout messages initialization', (string)$messagesStorage));
        }
        return $this;
    }

    /**
     * Set redirect into responce
     *
     * @param   string $path
     * @param   array $arguments
     */
    protected function _redirect($path, $arguments=array())
    {
        $this->getResponse()->setRedirect(Mage::getUrl($path, $arguments));
        return $this;
    }

    /**
     * Redirect to success page
     *
     * @param string $defaultUrl
     */
    protected function _redirectSuccess($defaultUrl)
    {
        $successUrl = $this->getRequest()->getParam(self::PARAM_NAME_SUCCESS_URL);
        if (empty($successUrl)) {
            $successUrl = $defaultUrl;
        }
        $this->getResponse()->setRedirect($successUrl);
        return $this;
    }

    /**
     * Redirect to error page
     *
     * @param string $defaultUrl
     */
    protected function _redirectError($defaultUrl)
    {
        $errorUrl = $this->getRequest()->getParam(self::PARAM_NAME_ERROR_URL);
        if (empty($errorUrl)) {
            $errorUrl = $defaultUrl;
        }
        $this->getResponse()->setRedirect($errorUrl);
        return $this;
    }

    /**
     * Set referer url for redirect in responce
     *
     * @param   string $defaultUrl
     * @return  Mage_Core_Controller_Varien_Action
     */
    protected function _redirectReferer($defaultUrl)
    {
        $refererUrl = $this->getRequest()->getServer('HTTP_REFERER');
        if (empty($refererUrl)) {
            $refererUrl = $this->getRequest()->getParam(self::PARAM_NAME_REFERER_URL);
        }
        if (empty($refererUrl)) {
            $refererUrl = $this->getRequest()->getParam(self::PARAM_NAME_BASE64_URL);
            if (!empty($refererUrl)) {
                $refererUrl = base64_decode($refererUrl);
            }
        }
        if (empty($refererUrl)) {
            $refererUrl = $defaultUrl;
        }
        $this->getResponse()->setRedirect($refererUrl);
        return $this;
    }
}
