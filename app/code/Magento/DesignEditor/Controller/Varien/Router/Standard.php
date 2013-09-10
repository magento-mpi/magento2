<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_DesignEditor_Controller_Varien_Router_Standard extends Magento_Core_Controller_Varien_Router_Base
{
    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * Routers that must not been matched
     *
     * @var array
     */
    protected $_excludedRouters = array('admin', 'vde');

    /**
     * @param Magento_Core_Controller_Varien_Action_Factory $controllerFactory
     * @param Magento_ObjectManager $objectManager
     * @param Magento_Filesystem $filesystem
     * @param Magento_Core_Model_App $app
     * @param Magento_Core_Model_Config_Scope $configScope
     * @param Magento_Core_Model_Route_Config $routeConfig
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param string $areaCode
     * @param string $baseController
     * @param string $routerId
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Magento_Core_Controller_Varien_Action_Factory $controllerFactory,
        Magento_ObjectManager $objectManager,
        Magento_Filesystem $filesystem,
        Magento_Core_Model_App $app,
        Magento_Core_Model_Config_Scope $configScope,
        Magento_Core_Model_Route_Config $routeConfig,
        Magento_Core_Model_Store_Config $coreStoreConfig,
        $areaCode,
        $baseController,
        $routerId
    ) {
        parent::__construct(
            $controllerFactory,
            $filesystem,
            $app,
            $configScope,
            $coreStoreConfig,
            $routeConfig,
            $areaCode,
            $baseController,
            $routerId
        );
        $this->_objectManager = $objectManager;
    }

    /**
     * Match provided request and if matched - return corresponding controller
     *
     * @param Magento_Core_Controller_Request_Http $request
     * @return Magento_Core_Controller_Front_Action|null
     */
    public function match(Magento_Core_Controller_Request_Http $request)
    {
        // if URL has VDE prefix
        if (!$this->_objectManager->get('Magento_DesignEditor_Helper_Data')->isVdeRequest($request)) {
            return null;
        }

        // user must be logged in admin area
        if (!$this->_objectManager->get('Magento_Backend_Model_Auth_Session')->isLoggedIn()) {
            return null;
        }

        // override VDE configuration
        $this->_overrideConfiguration();

        // prepare request to imitate
        $this->_prepareVdeRequest($request);

        // apply rewrites
        $this->getFront()->applyRewrites($request);

        // match routers
        $controller = null;
        $routers = $this->_getMatchedRouters();
        /** @var $router Magento_Core_Controller_Varien_Router_Abstract */
        foreach ($routers as $router) {
            /** @var $controller Magento_Core_Controller_Varien_ActionAbstract */
            $controller = $router->match($request);
            if ($controller) {
                $this->_objectManager->get('Magento_DesignEditor_Model_State')
                    ->update($this->_areaCode, $request, $controller);
                break;
            }
        }

        // set inline translation mode
        $this->_objectManager->get('Magento_DesignEditor_Helper_Data')->setTranslationMode($request);

        return $controller;
    }

    /**
     * Modify request path to imitate basic request
     *
     * @param Magento_Core_Controller_Request_Http $request
     * @return Magento_DesignEditor_Controller_Varien_Router_Standard
     */
    protected function _prepareVdeRequest(Magento_Core_Controller_Request_Http $request)
    {
        list($vdeFrontName, $designMode, $themeId) = explode('/', trim($request->getPathInfo(), '/'));
        $request->setAlias('editorMode', $designMode);
        $request->setAlias('themeId', (int)$themeId);
        $vdePath = implode('/', array($vdeFrontName, $designMode, $themeId));
        $noVdePath = substr($request->getPathInfo(), strlen($vdePath) + 1) ?: '/';
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

    /**
     * Override frontend configuration with VDE area data
     */
    protected function _overrideConfiguration()
    {
        $vdeNode = $this->_objectManager->get('Magento_Core_Model_Config')
            ->getNode(Magento_DesignEditor_Model_Area::AREA_VDE);
        if ($vdeNode) {
            $this->_objectManager->get('Magento_Core_Model_Config')->getNode(Magento_Core_Model_App_Area::AREA_FRONTEND)
                ->extend($vdeNode, true);
        }
    }
}
