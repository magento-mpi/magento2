<?php
/**
 * Test client for SOAP API testing.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Test_TestCase_Webapi_Adapter_Soap implements Magento_Test_TestCase_Webapi_AdapterInterface
{
    const WSDL_BATH_PATH = '/webapi/soap?wsdl=1';

    /**
     * SOAP client initialized with different WSDLs.
     *
     * @var Zend\Soap\Client[]
     */
    protected $_soapClients = array();

    /**
     * @var Mage_Webapi_Helper_Config
     */
    protected $_soapHelper;

    /**
     * Initialize dependencies.
     */
    public function __construct()
    {
        $this->_soapHelper = Mage::getObjectManager()->get('Mage_Webapi_Helper_Config');
    }

    /**
     * {@inheritdoc}
     */
    public function call($serviceInfo, $arguments = array())
    {
        $soapOperation = $this->_getSoapOperation($serviceInfo);
        $soapResponse = $this->_getSoapClient($serviceInfo)->$soapOperation($arguments);

        // TODO: Check if code below is necessary (when some tests are implemented)
        $result = (is_array($soapResponse) || is_object($soapResponse))
            ? $this->_normalizeResponse($soapResponse)
            : $soapResponse;
        return $result;
    }

    /**
     * Get proper SOAP client instance that is initialized with with WSDL corresponding to requested service interface.
     *
     * @param string $serviceInfo PHP service interface name, should include version if present
     * @return Zend\Soap\Client
     */
    protected function _getSoapClient($serviceInfo)
    {
        $wsdlUrl = $this->_getWsdlUrl(
            $this->_getSoapServiceName($serviceInfo),
            $this->_getSoapServiceVersion($serviceInfo)
        );
        /** Check if there is SOAP client initialized with requested WSDL available */
        if (!isset($this->_soapClients[$wsdlUrl])) {
            $this->_soapClients[$wsdlUrl] = new Zend\Soap\Client($wsdlUrl);
            $this->_soapClients[$wsdlUrl]->setSoapVersion(SOAP_1_2);
        }
        return $this->_soapClients[$wsdlUrl];
    }

    /**
     * Generate WSDL URL.
     *
     * @param string $serviceName
     * @param string $version
     * @return string
     */
    protected function _getWsdlUrl($serviceName, $version)
    {
        /** TESTS_BASE_URL is initialized in PHPUnit configuration */
        return rtrim(TESTS_BASE_URL, '/') . self::WSDL_BATH_PATH . "&resources[{$serviceName}]={$version}";
    }

    /**
     * Retrieve SOAP operation name from available service info.
     *
     * @param array $serviceInfo
     * @return string
     * @throws LogicException
     */
    protected function _getSoapOperation($serviceInfo)
    {
        if (isset($serviceInfo['soap']['operation'])) {
            $soapOperation = $serviceInfo['soap']['operation'];
        } else if (isset($serviceInfo['serviceInterface']) && isset($serviceInfo['method'])) {
            $soapOperation = $this->_soapHelper->getSoapOperation(
                $serviceInfo['serviceInterface'],
                $serviceInfo['method']
            );
        } else {
            throw new LogicException("SOAP operation cannot be identified.");
        }
        return $soapOperation;
    }

    /**
     * Retrieve service version from available service info.
     *
     * @param array $serviceInfo
     * @return string
     * @throws LogicException
     */
    protected function _getSoapServiceVersion($serviceInfo)
    {
        if (isset($serviceInfo['soap']['serviceVersion'])) {
            $version = $serviceInfo['soap']['serviceVersion'];
        } else if (isset($serviceInfo['serviceInterface'])) {
            preg_match('/.+[V](\d+)$/', $serviceInfo['serviceInterface'], $matches);
            if (isset($matches[1])) {
                $version = $matches[1];
            } else {
                throw new LogicException("Service interface name is invalid.");
            }
        } else {
            throw new LogicException("Service version cannot be identified.");
        }
        /** Normalize version */
        $version = 'V' . ltrim($version, 'vV');
        return $version;
    }

    /**
     * Retrieve service name from available service info.
     *
     * @param array $serviceInfo
     * @return string
     * @throws LogicException
     */
    protected function _getSoapServiceName($serviceInfo)
    {
        if (isset($serviceInfo['soap']['service'])) {
            $serviceName = $serviceInfo['soap']['service'];
        } else if (isset($serviceInfo['serviceInterface'])) {
            $serviceName = $this->_soapHelper->getServiceName($serviceInfo['serviceInterface'], false);
        } else {
            throw new LogicException("Service name cannot be identified.");
        }
        return $serviceName;
    }

    /**
     * Convert object to array recursively
     *
     * @param object $soapResult
     * @return array
     */
    public function _normalizeResponse($soapResult)
    {
        return $this->_replaceComplexObjectArray($this->_soapResultToArray($soapResult));
    }

    /**
     * Replace "complexObjectArray" keys from array
     *
     * @param array $arg
     * @return array
     */
    protected function _replaceComplexObjectArray(array $arg)
    {
        $data = array();

        foreach ($arg as $key => $value) {
            if (is_array($value)) {
                $value = $this->_replaceComplexObjectArray($value);
            }
            if ('complexObjectArray' == $key) {
                $key = count($data);
            }
            $data[$key] = $value;
        }
        return 1 === count($data) ? reset($data) : $data;
    }

    /**
     * Convert object to array recursively
     *
     * @param object $soapResult
     * @return array
     */
    protected function _soapResultToArray($soapResult)
    {
        if (is_object($soapResult) && null !== ($_data = get_object_vars($soapResult))) {
            foreach ($_data as $key => $value) {
                if (is_object($value) || is_array($value)) {
                    $_data[$key] = $this->_soapResultToArray($value);
                }
            }
            return $_data;
        } elseif (is_array($soapResult)) {
            $_data = array();
            foreach ($soapResult as $key => $value) {
                if (is_object($value) || is_array($value)) {
                    $_data[$key] = $this->_soapResultToArray($value);
                }
            }
            return $_data;
        }
        return array();
    }
}
