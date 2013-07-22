<?php
use Zend\Server\Reflection\ReflectionMethod;

/**
 * Web API configuration.
 *
 * This class is responsible for collecting web API configuration using reflection
 * as well as for implementing interface to provide access to collected configuration.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
abstract class Mage_Webapi_Model_ConfigAbstract
{
    /**#@+
     * Cache parameters.
     */
    const WEBSERVICE_CACHE_NAME = Mage_Webapi_Model_Cache_Type::TYPE_IDENTIFIER;
    const WEBSERVICE_CACHE_TAG = Mage_Webapi_Model_Cache_Type::CACHE_TAG;
    /**#@-*/

    /**#@+
     * Version parameters.
     */
    const VERSION_NUMBER_PREFIX = 'V';
    const VERSION_MIN = 1;
    /**#@-*/

    /** @var Mage_Webapi_Model_Config_ReaderAbstract */
    protected $_reader;

    /** @var Mage_Webapi_Helper_Config */
    protected $_helper;

    /** @var Mage_Core_Model_App */
    protected $_application;

    /**
     * Services configuration data.
     *
     * @var array
     */
    protected $_data;

    /**
     * Initialize dependencies. Initialize data.
     *
     * @param Mage_Webapi_Model_Config_ReaderAbstract $reader
     * @param Mage_Webapi_Helper_Config $helper
     * @param Mage_Core_Model_App $application
     */
    public function __construct(
        Mage_Webapi_Model_Config_ReaderAbstract $reader,
        Mage_Webapi_Helper_Config $helper,
        Mage_Core_Model_App $application
    ) {
        $this->_reader = $reader;
        $this->_helper = $helper;
        $this->_application = $application;
        $this->_data = /*$this->_reader->getData()*/ array();
    }

    /**
     * Retrieve data type details for the given type name.
     *
     * @param string $typeName
     * @return array
     * @throws InvalidArgumentException
     */
    public function getTypeData($typeName)
    {
        if (!isset($this->_data['types'][$typeName])) {
            throw new InvalidArgumentException(sprintf('Data type "%s" was not found in config.', $typeName));
        }
        return $this->_data['types'][$typeName];
    }

    /**
     * Add or update type data in config.
     *
     * @param string $typeName
     * @param array $data
     */
    public function setTypeData($typeName, $data)
    {
        if (!isset($this->_data['types'][$typeName])) {
            $this->_data['types'][$typeName] = $data;
        } else {
            $this->_data['types'][$typeName] = array_merge_recursive($this->_data['types'][$typeName], $data);
        }
    }

    /**
     * Identify method name by operation name.
     *
     * @param string $operationName
     * @param string $serviceVersion Two formats are acceptable: 'v1' and '1'
     * @return string|bool Method name on success; false on failure
     */
    public function getMethodNameByOperation($operationName, $serviceVersion = null)
    {
        list($serviceName, $methodName) = $this->_parseOperationName($operationName);
        $versionCheckRequired = is_string($serviceVersion);
        if (!$versionCheckRequired) {
            return $methodName;
        }
        /** Allow to take service version in two formats: with prefix and without it */
        $serviceVersion = is_numeric($serviceVersion)
            ? self::VERSION_NUMBER_PREFIX . $serviceVersion
            : ucfirst($serviceVersion);
        return isset($this->_data['services'][$serviceName]['versions'][$serviceVersion]['methods'][$methodName])
            ? $methodName : false;
    }

    /**
     * Parse operation name to separate service name from method name.
     *
     * <pre>Result format:
     * array(
     *      0 => 'serviceName',
     *      1 => 'methodName'
     * )</pre>
     *
     * @param string $operationName
     * @return array
     * @throws InvalidArgumentException In case when the specified operation name is invalid.
     */
    protected function _parseOperationName($operationName)
    {
        /** Note that '(.*?)' must not be greedy to allow regexp to match 'multiUpdate' method before 'update' */
        $regEx = sprintf('/(.*?)(%s)$/i', implode('|', Mage_Webapi_Controller_ActionAbstract::getAllowedMethods()));
        if (preg_match($regEx, $operationName, $matches)) {
            $serviceName = $matches[1];
            $methodName = lcfirst($matches[2]);
            $result = array($serviceName, $methodName);
            return $result;
        }
        throw new InvalidArgumentException(sprintf(
            'The "%s" is not a valid API service operation name.',
            $operationName
        ));
    }

    /**
     * Identify controller class by operation name.
     *
     * @param string $operationName
     * @return string Service name on success
     * @throws LogicException
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function getControllerClassByOperationName($operationName)
    {
        list($serviceName, $methodName) = $this->_parseOperationName($operationName);
        if (isset($this->_data['services'][$serviceName]['controller'])) {
            return $this->_data['services'][$serviceName]['controller'];
        }
        throw new LogicException(sprintf('Service "%s" must have associated controller class.', $serviceName));
    }

    /**
     * Retrieve method metadata.
     *
     * @param Zend\Server\Reflection\ReflectionMethod $methodReflection
     * @return array
     * @throws InvalidArgumentException If specified method was not previously registered in API config.
     */
    public function getMethodMetadata(ReflectionMethod $methodReflection)
    {
        $serviceName = $this->_helper->getServiceName($methodReflection->getDeclaringClass()->getName());
        $serviceVersion = $this->_getMethodVersion($methodReflection);
        $methodName = $this->_helper->getMethodNameWithoutVersionSuffix($methodReflection);

        if (!isset($this->_data['services'][$serviceName]['versions'][$serviceVersion]['methods'][$methodName])) {
            throw new InvalidArgumentException(sprintf(
                'The "%s" method of "%s" service in version "%s" is not registered.',
                $methodName,
                $serviceName,
                $serviceVersion
            ));
        }
        return $this->_data['services'][$serviceName]['versions'][$serviceVersion]['methods'][$methodName];
    }

    /**
     * Retrieve mapping of complex types defined in WSDL to real data classes.
     *
     * @return array
     */
    public function getTypeToClassMap()
    {
        return isset($this->_data['type_to_class_map']) && !is_null($this->_data['type_to_class_map'])
            ? $this->_data['type_to_class_map']
            : array();
    }

    /**
     * Identify deprecation policy for the specified operation.
     *
     * Return result in the following format:<pre>
     * array(
     *     'removed'      => true,            // either 'deprecated' or 'removed' item must be specified
     *     'deprecated'   => true,
     *     'use_service' => 'operationName'  // service to be used instead
     *     'use_method'   => 'operationName'  // method to be used instead
     *     'use_version'  => N,               // version of method to be used instead
     * )
     * </pre>
     *
     * @param string $serviceName
     * @param string $method
     * @param string $serviceVersion
     * @return array|bool On success array with policy details; false otherwise.
     * @throws InvalidArgumentException
     */
    public function getDeprecationPolicy($serviceName, $method, $serviceVersion)
    {
        $deprecationPolicy = false;
        $serviceData = $this->_getServiceData($serviceName, $serviceVersion);
        if (!isset($serviceData['methods'][$method])) {
            throw new InvalidArgumentException(sprintf(
                'Method "%s" does not exist in "%s" version of service "%s".',
                $method,
                $serviceVersion,
                $serviceName
            ));
        }
        $methodData = $serviceData['methods'][$method];
        if (isset($methodData['deprecation_policy']) && is_array($methodData['deprecation_policy'])) {
            $deprecationPolicy = $methodData['deprecation_policy'];
        }
        return $deprecationPolicy;
    }

    /**
     * Check if specified method is deprecated or removed.
     *
     * Throw exception in two cases:<br/>
     * - method is removed<br/>
     * - method is deprecated and developer mode is enabled
     *
     * @param string $serviceName
     * @param string $method
     * @param string $serviceVersion
     * @throws Mage_Webapi_Exception
     * @throws LogicException
     */
    public function checkDeprecationPolicy($serviceName, $method, $serviceVersion)
    {
        $deprecationPolicy = $this->getDeprecationPolicy($serviceName, $method, $serviceVersion);
        if ($deprecationPolicy) {
            /** Initialize message with information about what method should be used instead of requested one. */
            if (isset($deprecationPolicy['use_service']) && isset($deprecationPolicy['use_method'])
                && isset($deprecationPolicy['use_version'])
            ) {
                $messageUseMethod = $this->_helper
                    ->__('Please use version "%s" of "%s" method in "%s" service instead.',
                    $deprecationPolicy['use_version'],
                    $deprecationPolicy['use_method'],
                    $deprecationPolicy['use_service']
                );
            } else {
                $messageUseMethod = '';
            }

            $badRequestCode = Mage_Webapi_Exception::HTTP_BAD_REQUEST;
            if (isset($deprecationPolicy['removed'])) {
                $removalMessage = $this->_helper
                    ->__('Version "%s" of "%s" method in "%s" service was removed.',
                    $serviceVersion,
                    $method,
                    $serviceName
                );
                throw new Mage_Webapi_Exception($removalMessage . ' ' . $messageUseMethod, $badRequestCode);
            } elseif (isset($deprecationPolicy['deprecated']) && $this->_application->isDeveloperMode()) {
                $deprecationMessage = $this->_helper
                    ->__('Version "%s" of "%s" method in "%s" service is deprecated.',
                    $serviceVersion,
                    $method,
                    $serviceName
                );
                throw new Mage_Webapi_Exception($deprecationMessage . ' ' . $messageUseMethod, $badRequestCode);
            }
        }
    }

    /**
     * Identify the maximum version of the specified service available.
     *
     * @param string $serviceName
     * @return int
     * @throws InvalidArgumentException When service with the specified name does not exist.
     */
    public function getServiceMaxVersion($serviceName)
    {
        if (!isset($this->_data['services'][$serviceName])) {
            throw new InvalidArgumentException(sprintf('Service "%s" does not exist.', $serviceName));
        }
        $serviceVersions = array_keys($this->_data['services'][$serviceName]['versions']);
        foreach ($serviceVersions as &$version) {
            $version = str_replace(self::VERSION_NUMBER_PREFIX, '', $version);
        }
        $maxVersion = max($serviceVersions);
        return (int)$maxVersion;
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
    public function identifyVersionSuffix($operationName, $requestedVersion, $controllerInstance)
    {
        $methodName = $this->getMethodNameByOperation($operationName, $requestedVersion);
        $methodVersion = $requestedVersion;
        while ($methodVersion >= self::VERSION_MIN) {
            $versionSuffix = Mage_Webapi_Model_ConfigAbstract::VERSION_NUMBER_PREFIX . $methodVersion;
            if ($controllerInstance->hasAction($methodName . $versionSuffix)) {
                return $versionSuffix;
            }
            $methodVersion--;
        }
        throw new Mage_Webapi_Exception($this->_helper
                ->__('The "%s" operation is not implemented in version %s', $operationName, $requestedVersion),
            Mage_Webapi_Exception::HTTP_BAD_REQUEST
        );
    }

    /**
     * Check if version number is from valid range.
     *
     * @param int $version
     * @param string $serviceName
     * @throws Mage_Webapi_Exception
     */
    public function validateVersionNumber($version, $serviceName)
    {
        $maxVersion = $this->getServiceMaxVersion($serviceName);
        if ((int)$version > $maxVersion) {
            throw new Mage_Webapi_Exception(
                $this->_helper->__('The maximum version of the requested service is "%s".', $maxVersion),
                Mage_Webapi_Exception::HTTP_BAD_REQUEST
            );
        } elseif ((int)$version < self::VERSION_MIN) {
            throw new Mage_Webapi_Exception(
                $this->_helper->__('Service version cannot be lower than "%s".', self::VERSION_MIN),
                Mage_Webapi_Exception::HTTP_BAD_REQUEST
            );
        }
    }

    /**
     * Retrieve the list of all services with their versions.
     *
     * @return array
     */
    public function getAllServicesVersions()
    {
        $services = array();
        if (isset($this->_data['services'])) {
            foreach ($this->_data['services'] as $serviceName => $data) {
                $services[$serviceName] = array_keys($data['versions']);
            }
        }

        return $services;
    }

    /**
     * Identify API method version by its reflection.
     *
     * @param ReflectionMethod $methodReflection
     * @return string|bool Method version with prefix on success.
     *      false is returned in case when method should not be exposed via API.
     */
    protected function _getMethodVersion(ReflectionMethod $methodReflection)
    {
        $methodVersion = false;
        $methodNameWithSuffix = $methodReflection->getName();
        $regularExpression = $this->_helper->getMethodNameRegularExpression();
        if (preg_match($regularExpression, $methodNameWithSuffix, $methodMatches)) {
            $serviceNamePosition = 2;
            $methodVersion = ucfirst($methodMatches[$serviceNamePosition]);
        }
        return $methodVersion;
    }

    /**
     * Retrieve service description for specified version.
     *
     * @param string $serviceName
     * @param string $serviceVersion Two formats are acceptable: 'v1' and '1'
     * @return array
     * @throws InvalidArgumentException When the specified service version does not exist.
     */
    protected function _getServiceData($serviceName, $serviceVersion)
    {
        /** Allow to take service version in two formats: with prefix and without it */
        $serviceVersion = is_numeric($serviceVersion)
            ? self::VERSION_NUMBER_PREFIX . $serviceVersion
            : ucfirst($serviceVersion);
        try {
            $this->_checkIfServiceVersionExists($serviceName, $serviceVersion);
        } catch (RuntimeException $e) {
            throw new InvalidArgumentException($e->getMessage());
        }
        return $this->_data['services'][$serviceName]['versions'][$serviceVersion];
    }

    /**
     * Check if specified version of service exists. If not - exception is thrown.
     *
     * @param string $serviceName
     * @param string $serviceVersion
     * @throws RuntimeException When service does not exist.
     */
    protected function _checkIfServiceVersionExists($serviceName, $serviceVersion)
    {
        if (!isset($this->_data['services'][$serviceName])) {
            throw new RuntimeException($this->_helper->__('Unknown service "%s".', $serviceName));
        }
        if (!isset($this->_data['service'][$serviceName]['versions'][$serviceVersion])) {
            throw new RuntimeException($this->_helper->__(
                'Unknown version "%s" for service "%s".',
                $serviceVersion,
                $serviceName
            ));
        }
    }
}
