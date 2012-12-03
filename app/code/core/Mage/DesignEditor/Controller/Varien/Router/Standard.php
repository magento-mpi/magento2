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
     * Name of layout class that will be used as main layout
     */
    const LAYOUT_CLASS_NAME = 'Mage_DesignEditor_Model_Layout';

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
    protected $_excludedRouters = array('admin', 'vde', 'default');

    /**
     * Layout factory
     *
     * @var Mage_Core_Model_Layout_Factory
     */
    protected $_layoutFactory;

    /**
     * @param Mage_Core_Controller_Varien_Action_Factory $controllerFactory
     * @param Magento_ObjectManager $objectManager
     * @param string $areaCode
     * @param string $baseController
     * @param Mage_Backend_Model_Auth_Session $backendSession
     * @param Mage_DesignEditor_Helper_Data $helper
     * @param Mage_Core_Model_Layout_Factory $layoutFactory
     */
    public function __construct(
        Mage_Core_Controller_Varien_Action_Factory $controllerFactory,
        Magento_ObjectManager $objectManager,
        $areaCode,
        $baseController,
        Mage_Backend_Model_Auth_Session $backendSession,
        Mage_DesignEditor_Helper_Data $helper,
        Mage_Core_Model_Layout_Factory $layoutFactory
    ) {
        parent::__construct($controllerFactory, $objectManager, $areaCode, $baseController);

        $this->_backendSession = $backendSession;
        $this->_helper         = $helper;
        $this->_layoutFactory  = $layoutFactory;
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
        if (!$this->_helper->isVdeRequest($request)) {
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
            /** @var $controller Mage_Core_Controller_Front_Action */
            $controller = $router->match($request);
            if ($controller) {
                /**
                 * Create layout instance that will be used as main layout for whole system
                 */
                $this->_layoutFactory->createLayout(
                    array('area' => $this->_areaCode),
                    self::LAYOUT_CLASS_NAME
                );
                break;
            }
        }

        return $controller;
    }

    /**
     * Modify request path to imitate basic request
     *
     * @param Mage_Core_Controller_Request_Http $request
     * @return Mage_DesignEditor_Controller_Varien_Router_Standard
     */
    protected function _prepareVdeRequest(Mage_Core_Controller_Request_Http $request)
    {
        $noVdePath = substr($request->getPathInfo(), strlen(Mage_DesignEditor_Helper_Data::FRONT_NAME) + 1) ?: '/';
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
