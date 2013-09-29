<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 *
 * @SuppressWarnings(PHPMD.ExcessiveParameterList)
 */

/**
 * Class Magento_Backend_Controller_Router_Default
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Magento_Backend_Controller_Router_Default extends Magento_Core_Controller_Varien_Router_Base
{
    /**
     * List of required request parameters
     * Order sensitive
     * @var array
     */
    protected $_requiredParams = array(
        'areaFrontName',
        'moduleFrontName',
        'controllerName',
        'actionName',
    );

    /**
     * Url key of area
     *
     * @var string
     */
    protected $_areaFrontName;

    /**
     * @var Magento_Backend_Helper_Data
     */
    protected $_backendData;

    /**
     * @param Magento_Backend_Helper_Data $backendData
     * Default routeId for router
     *
     * @var string
     */
    protected $_defaultRouteId;

    /**
     * @var Magento_Core_Model_App_State
     */
    protected $_appState;

    /**
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var Magento_Core_Model_Url
     */
    protected $_url;

    /**
     * @param Magento_Backend_Helper_Data $backendData
     * @param Magento_Core_Controller_Varien_Action_Factory $controllerFactory
     * @param Magento_Filesystem $filesystem
     * @param Magento_Core_Model_App $app
     * @param Magento_Core_Model_App_State $appState
     * @param Magento_Core_Model_Config_Scope $configScope
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param Magento_Core_Model_Route_Config $routeConfig
     * @param Magento_Core_Model_Url_SecurityInfoInterface $securityInfo
     * @param Magento_Core_Model_Config $config
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Model_Url $url
     * @param Magento_Core_Model_App_State $appState
     * @param $areaCode
     * @param $baseController
     * @param $routerId
     * @param $defaultRouteId
     *
     * @throws InvalidArgumentException
     */
    public function __construct(
        Magento_Backend_Helper_Data $backendData,
        Magento_Core_Controller_Varien_Action_Factory $controllerFactory,
        Magento_Filesystem $filesystem,
        Magento_Core_Model_App $app,
        Magento_Core_Model_App_State $appState,
        Magento_Core_Model_Config_Scope $configScope,
        Magento_Core_Model_Store_Config $coreStoreConfig,
        Magento_Core_Model_Route_Config $routeConfig,
        Magento_Core_Model_Url_SecurityInfoInterface $securityInfo,
        Magento_Core_Model_Config $config,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Model_Url $url,
        Magento_Core_Model_App_State $appState,
        $areaCode,
        $baseController,
        $routerId,
        $defaultRouteId
    ) {
        parent::__construct(
            $controllerFactory,
            $filesystem,
            $app,
            $configScope,
            $coreStoreConfig,
            $routeConfig,
            $securityInfo,
            $config,
            $url,
            $storeManager,
            $appState,
            $areaCode,
            $baseController,
            $routerId,
            $defaultRouteId
        );
        $this->_backendData = $backendData;
        $this->_appState = $appState;
        $this->_storeManager = $storeManager;
        $this->_url = $url;
        $this->_areaFrontName = $this->_backendData->getAreaFrontName();
        $this->_defaultRouteId = $defaultRouteId;
        if (empty($this->_areaFrontName)) {
            throw new InvalidArgumentException('Area Front Name should be defined');
        }
    }

    /**
     * Fetch default path
     */
    public function fetchDefault()
    {
        // set defaults
        $pathParts = explode('/', $this->_getDefaultPath());
        $backendRoutes = $this->_getRoutes();
        $moduleFrontName = $backendRoutes[$this->_defaultRouteId]['frontName'];

        $this->getFront()->setDefault(array(
            'area'       => $this->_getParamWithDefaultValue($pathParts, 0, ''),
            'module'     => $this->_getParamWithDefaultValue($pathParts, 1, $moduleFrontName),
            'controller' => $this->_getParamWithDefaultValue($pathParts, 2, 'index'),
            'action'     => $this->_getParamWithDefaultValue($pathParts, 3, 'index'),
        ));
    }

    /**
     * Retrieve array param by key, or default value
     *
     * @param array $array
     * @param string $key
     * @param mixed $defaultValue
     * @return mixed
     */
    protected function _getParamWithDefaultValue($array, $key, $defaultValue)
    {
        return !empty($array[$key]) ? $array[$key] : $defaultValue;
    }

    /**
     * Get router default request path
     * @return string
     */
    protected function _getDefaultPath()
    {
        return (string)$this->_config->getValue('web/default/admin', 'default');
    }

    /**
     * Dummy call to pass through checking
     *
     * @return boolean
     */
    protected function _beforeModuleMatch()
    {
        return true;
    }

    /**
     * checking if we installed or not and doing redirect
     *
     * @return bool
     * @SuppressWarnings(PHPMD.ExitExpression)
     */
    protected function _afterModuleMatch()
    {
        if (!$this->_appState->isInstalled()) {
            $this->_app->getFrontController()
                ->getResponse()
                ->setRedirect($this->_url->getUrl('install'))
                ->sendResponse();
            exit;
        }
        return true;
    }

    /**
     * We need to have noroute action in this router
     * not to pass dispatching to next routers
     *
     * @return bool
     */
    protected function _noRouteShouldBeApplied()
    {
        return true;
    }

    /**
     * Check whether URL for corresponding path should use https protocol
     *
     * @param string $path
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _shouldBeSecure($path)
    {
        return substr((string)$this->_config->getValue('web/unsecure/base_url', 'default'), 0, 5) === 'https'
            || $this->_coreStoreConfig->getConfigFlag(
                'web/secure/use_in_adminhtml',
                Magento_Core_Model_AppInterface::ADMIN_STORE_ID
            ) && substr((string)$this->_config->getValue('web/secure/base_url', 'default'), 0, 5) === 'https';
    }

    /**
     * Retrieve current secure url
     *
     * @param Magento_Core_Controller_Request_Http $request
     * @return string
     */
    protected function _getCurrentSecureUrl($request)
    {
        return $this->_storeManager->getStore(Magento_Core_Model_AppInterface::ADMIN_STORE_ID)
            ->getBaseUrl('link', true) . ltrim($request->getPathInfo(), '/');
    }

    /**
     * Check whether redirect should be used for secure routes
     *
     * @return bool
     */
    protected function _shouldRedirectToSecure()
    {
        return false;
    }

    /**
     * Build controller class name based on moduleName and controllerName
     *
     * @param string $realModule
     * @param string $controller
     * @return string
     */
    public function getControllerClassName($realModule, $controller)
    {
        /**
         * Start temporary block
         * TODO: Sprint#27. Delete after adminhtml refactoring
         */
        if ($realModule == 'Magento_Adminhtml') {
            return parent::getControllerClassName($realModule, $controller);
        }
        /**
         * End temporary block
         */

        $parts = explode('_', $realModule);
        $realModule = implode('_', array_splice($parts, 0, 2));
        return $realModule . '_' . 'Controller' . '_'. ucfirst($this->_areaCode) . '_' . uc_words($controller);
    }

    /**
     * Check whether this router should process given request
     *
     * @param array $params
     * @return bool
     */
    protected function _canProcess(array $params)
    {
        return $params['areaFrontName'] == $this->_areaFrontName;
    }
}
