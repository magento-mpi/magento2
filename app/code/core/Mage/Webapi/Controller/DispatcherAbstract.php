<?php
/**
 * Abstract dispatcher for web API requests.
 *
 * @copyright {}
 */
abstract class Mage_Webapi_Controller_DispatcherAbstract
{
    const VERSION_MIN = 1;

    /** @var Mage_Webapi_Controller_Response */
    protected $_response;

    /** @var Mage_Webapi_Model_Config */
    protected $_apiConfig;

    /** @var Mage_Webapi_Helper_Data */
    protected $_helper;

    /**
     * Action controller factory.
     *
     * @var Mage_Webapi_Controller_Action_Factory
     */
    protected $_controllerFactory;

    /** @var Mage_Core_Model_Logger */
    protected $_logger;

    /** @var Mage_Webapi_Model_Authorization */
    protected $_authorization;

    /**
     * Initialize dependencies.
     *
     * @param Mage_Webapi_Helper_Data $helper
     * @param Mage_Webapi_Model_Config $apiConfig
     * @param Mage_Webapi_Controller_Response $response
     * @param Mage_Webapi_Controller_Action_Factory $controllerFactory
     * @param Mage_Core_Model_Logger $logger
     * @param Mage_Webapi_Model_Authorization $authorization
     */
    public function __construct(
        Mage_Webapi_Helper_Data $helper,
        Mage_Webapi_Model_Config $apiConfig,
        Mage_Webapi_Controller_Response $response,
        Mage_Webapi_Controller_Action_Factory $controllerFactory,
        Mage_Core_Model_Logger $logger,
        Mage_Webapi_Model_Authorization $authorization
    ) {
        $this->_helper = $helper;
        $this->_apiConfig = $apiConfig;
        $this->_controllerFactory = $controllerFactory;
        $this->_response = $response;
        $this->_logger = $logger;
        $this->_authorization = $authorization;
    }

    /**
     * Handle request.
     *
     * @return Mage_Webapi_Controller_DispatcherAbstract
     */
    abstract public function handle();

    /**
     * Initialize API configuration.
     *
     * @return Mage_Webapi_Controller_DispatcherAbstract
     */
    public function init()
    {
        $this->_apiConfig->init();
        return $this;
    }

    /**
     * Retrieve config describing resources available in all APIs.
     * The same resource config must be used in all API types.
     *
     * @return Mage_Webapi_Model_Config
     */
    public function getApiConfig()
    {
        return $this->_apiConfig;
    }

    /**
     * Retrieve response object.
     *
     * @return Mage_Webapi_Controller_Response
     */
    public function getResponse()
    {
        return $this->_response;
    }

    /**
     * Find the most appropriate version suffix for the requested action.
     *
     * If there is no action with requested version, fallback mechanism is used.
     * If there is no appropriate action found after fallback - exception is thrown.
     *
     * @param string $operationName
     * @param int $requestedVersion
     * @param Mage_Webapi_Controller_ActionAbstract $controllerInstance
     * @return string
     * @throws Mage_Webapi_Exception
     */
    protected function _identifyVersionSuffix($operationName, $requestedVersion, $controllerInstance)
    {
        $methodName = $this->getApiConfig()->getMethodNameByOperation($operationName, $requestedVersion);
        $methodVersion = $requestedVersion;
        while ($methodVersion >= self::VERSION_MIN) {
            $versionSuffix = Mage_Webapi_Model_Config::VERSION_NUMBER_PREFIX . $methodVersion;
            if ($controllerInstance->hasAction($methodName . $versionSuffix)) {
                return $versionSuffix;
            }
            $methodVersion--;
        }
        throw new Mage_Webapi_Exception($this->getHelper()
                ->__('The "%s" operation is not implemented in version %s', $operationName, $requestedVersion),
            Mage_Webapi_Exception::HTTP_BAD_REQUEST
        );
    }

    /**
     * Check if specified method is deprecated or removed.
     *
     * Throw exception in two cases:<br/>
     * - method is removed<br/>
     * - method is deprecated and developer mode is enabled
     *
     * @param string $resourceName
     * @param string $method
     * @param string $resourceVersion
     * @throws Mage_Webapi_Exception
     * @throws LogicException
     */
    protected function _checkDeprecationPolicy($resourceName, $method, $resourceVersion)
    {
        $deprecationPolicy = $this->getApiConfig()->getDeprecationPolicy($resourceName, $method, $resourceVersion);
        if ($deprecationPolicy) {
            /** Initialize message with information about what method should be used instead of requested one. */
            if (isset($deprecationPolicy['use_resource']) && isset($deprecationPolicy['use_method'])
                && isset($deprecationPolicy['use_version'])
            ) {
                $messageUseMethod = $this->getHelper()
                    ->__('Please use version "%s" of "%s" method in "%s" resource instead.',
                    $deprecationPolicy['use_version'],
                    $deprecationPolicy['use_method'],
                    $deprecationPolicy['use_resource']
                );
            } else {
                $messageUseMethod = '';
            }

            $badRequestCode = Mage_Webapi_Exception::HTTP_BAD_REQUEST;
            if (isset($deprecationPolicy['removed'])) {
                $removalMessage = $this->getHelper()
                    ->__('Version "%s" of "%s" method in "%s" resource was removed.',
                    $resourceVersion,
                    $method,
                    $resourceName
                );
                throw new Mage_Webapi_Exception($removalMessage . ' ' . $messageUseMethod, $badRequestCode);
                // TODO: Replace static call after MAGETWO-4961 implementation
                // TODO: Replace Mage::getIsDeveloperMode() to isDeveloperMode() (Mage_Core_Model_App)
            } elseif (isset($deprecationPolicy['deprecated']) && Mage::getIsDeveloperMode()) {
                $deprecationMessage = $this->getHelper()
                    ->__('Version "%s" of "%s" method in "%s" resource is deprecated.',
                    $resourceVersion,
                    $method,
                    $resourceName
                );
                throw new Mage_Webapi_Exception($deprecationMessage . ' ' . $messageUseMethod, $badRequestCode);
            }
        }
    }

    /**
     * Retrieve Webapi data helper.
     *
     * @return Mage_Webapi_Helper_Data
     */
    public function getHelper()
    {
        return $this->_helper;
    }

    /**
     * Check if version number is from valid range.
     *
     * @param int $version
     * @param string $resourceName
     * @throws Mage_Webapi_Exception
     */
    protected function _validateVersionNumber($version, $resourceName)
    {
        $maxVersion = $this->getApiConfig()->getResourceMaxVersion($resourceName);
        if ((int)$version > $maxVersion) {
            throw new Mage_Webapi_Exception(
                $this->getHelper()->__('The maximum version of the requested resource is "%s".', $maxVersion),
                Mage_Webapi_Exception::HTTP_BAD_REQUEST
            );
        } elseif ((int)$version < self::VERSION_MIN) {
            throw new Mage_Webapi_Exception(
                $this->getHelper()->__('Resource version cannot be lower than "%s".', self::VERSION_MIN),
                Mage_Webapi_Exception::HTTP_BAD_REQUEST
            );
        }
    }
}
