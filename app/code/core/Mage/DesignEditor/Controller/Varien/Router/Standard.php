<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_DesignEditor_Controller_Varien_Router_Standard extends Mage_Core_Controller_Varien_Router_Base
{
    /**
     * @var Mage_Backend_Model_Auth_Session
     */
    protected $_backendSession;

    /**
     * @var Mage_DesignEditor_Helper_Data
     */
    protected $_helper;

    /**
     * Routers that must not been matched
     *
     * @var array
     */
    protected $_excludedRouters = array('admin', 'vde');

    /**
     * Layout factory
     *
     * @var Mage_DesignEditor_Model_State
     */
    protected $_editorState;

    /**
     * @param Mage_Core_Controller_Varien_Action_Factory $controllerFactory
     * @param Magento_ObjectManager $objectManager
     * @param string $areaCode
     * @param string $baseController
     * @param Mage_Backend_Model_Auth_Session $backendSession
     * @param Mage_DesignEditor_Helper_Data $helper
     * @param Mage_DesignEditor_Model_State $editorState
     */
    public function __construct(
        Mage_Core_Controller_Varien_Action_Factory $controllerFactory,
        Magento_ObjectManager $objectManager,
        $areaCode,
        $baseController,
        Mage_Backend_Model_Auth_Session $backendSession,
        Mage_DesignEditor_Helper_Data $helper,
        Mage_DesignEditor_Model_State $editorState
    ) {
        parent::__construct($controllerFactory, $objectManager, $areaCode, $baseController);

        $this->_backendSession = $backendSession;
        $this->_helper         = $helper;
        $this->_editorState    = $editorState;
    }

    /**
     * Match provided request and if matched - return corresponding controller
     *
     * @param Mage_Core_Controller_Request_Http $request
     * @return Mage_Core_Controller_Front_Action|null
     */
    public function match(Mage_Core_Controller_Request_Http $request)
    {
        // if URL has VDE prefix
        if (!$this->_isVdeRequest($request)) {
            return null;
        }

        // user must be logged in admin area
        if (!$this->_backendSession->isLoggedIn()) {
            return null;
        }

        // prepare request to imitate
        $this->_prepareVdeRequest($request);

        // apply rewrites
        $this->getFront()->applyRewrites($request);

        // match routers
        $controller = null;
        $routers = $this->_getMatchedRouters();
        /** @var $router Mage_Core_Controller_Varien_Router_Abstract */
        foreach ($routers as $router) {
            /** @var $controller Mage_Core_Controller_Varien_ActionAbstract */
            $controller = $router->match($request);
            if ($controller) {
                $this->_editorState->update($this->_areaCode, $request, $controller);
                break;
            }
        }

        return $controller;
    }

    /**
     * Check if URL has vde prefix
     *
     * @param Mage_Core_Controller_Request_Http $request
     * @return bool
     */
    protected function _isVdeRequest(Mage_Core_Controller_Request_Http $request)
    {
        $url = trim($request->getOriginalPathInfo(), '/');
        $vdeFrontName = $this->_helper->getFrontName();
        return $url == $vdeFrontName || strpos($url, $vdeFrontName . '/') === 0;
    }

    /**
     * Modify request path to imitate basic request
     *
     * @param Mage_Core_Controller_Request_Http $request
     * @return Mage_DesignEditor_Controller_Varien_Router_Standard
     */
    protected function _prepareVdeRequest(Mage_Core_Controller_Request_Http $request)
    {
        $vdeFrontName = $this->_helper->getFrontName();
        $noVdePath = substr($request->getPathInfo(), strlen($vdeFrontName) + 1) ?: '/';
        $request->setPathInfo($noVdePath);
        return $this;
    }

    /**
     * Returns list of routers that must been tried to match
     *
     * @return array
     */
    protected function _getMatchedRouters()
    {
        $routers = $this->getFront()->getRouters();
        foreach (array_keys($routers) as $name) {
            if (in_array($name, $this->_excludedRouters)) {
                unset($routers[$name]);
            }
        }
        return $routers;
    }
}
