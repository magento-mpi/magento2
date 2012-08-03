<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Front controller for SOAP API. At the same time it is a handler for SOAP server
 */
// TODO: Add profiler calls
class Mage_Api2_Controller_Front_Soap extends Mage_Api2_Controller_FrontAbstract
{
    const FAULT_CODE_SENDER = 'Sender';
    const FAULT_CODE_RECEIVER = 'Receiver';

    const FAULT_REASON_INTERNAL = 'Internal Error.';

    const WEBSERVICE_CACHE_NAME = 'config_webservice';
    const WEBSERVICE_CACHE_TAG = 'WEBSERVICE';
    const WSDL_CACHE_ID = 'WSDL';

    /** @var Zend_Soap_Server */
    protected $_soapServer;

    /**
     * TODO: Change base controller to Generic controller for SOAP API
     *
     * @var string
     */
    protected $_baseActionController = 'Mage_Core_Controller_Varien_Action';

    /** @var Mage_Api2_Model_Config_Soap */
    protected $_soapConfig;

    /**
     * Handler for all SOAP operations
     *
     * @param string $operation
     * @param array $arguments
     * @return stdClass
     */
    // TODO: Think about situations when custom error handler is required for this method (that can throw soap faults)
    public function __call($operation, $arguments)
    {
        $resourceName = $this->getResourceConfig()->getResourceNameByOperation($operation);
        if (!$resourceName) {
            $this->_soapFault(sprintf('Method "%s" is not found.', $operation), self::FAULT_CODE_SENDER);
        }
        $controllerClass = $this->getSoapConfig()->getControllerClassByResourceName($resourceName);
        $controller = $this->_getActionControllerInstance($controllerClass);
        $action = $this->getResourceConfig()->getMethodNameByOperation($operation);
        if (!$controller->hasAction($action)) {
            $this->_soapFault();
        }
        // TODO: ACL check is not implemented yet
        $this->_checkResourceAcl();

        // TODO: Think about the best format for method parameters (objects, arrays)
        $arguments = $arguments[0];
        /** @var Mage_Api_Helper_Data $helper */
        $helper = Mage::helper('Mage_Api_Helper_Data');
        // TODO: Move wsiArrayUnpacker from helper to this class
        $helper->wsiArrayUnpacker($arguments);
        $arguments = get_object_vars($arguments);

        $actionParams = $this->_fetchMethodParams($controllerClass, $action);
        $arguments = $this->_prepareMethodParams($actionParams, $arguments);
        try {
            $result = $controller->$action($arguments);
            // TODO: Move wsiArrayPacker from helper to this class
            $obj = $helper->wsiArrayPacker($result);
            $stdObj = new stdClass();
            $stdObj->result = $obj;
            return $stdObj;
            // TODO: Implement proper exception handling
        } catch (Mage_Api_Exception $e) {
            $this->_soapFault($e->getCustomMessage(), 'Receiver', $e);
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_soapFault();
        }
    }

    /**
     * Extend parent with SOAP specific config initialization
     *
     * @return Mage_Api2_Controller_Front_Soap|Mage_Core_Controller_FrontInterface
     */
    public function init()
    {
        $soapConfigFiles = Mage::getConfig()->getModuleConfigurationFiles('api_soap.xml');
        /** @var Mage_Api2_Model_Config_Soap $soapConfig */
        $soapConfig = Mage::getModel('Mage_Api2_Model_Config_Soap', $soapConfigFiles);
        $this->setSoapConfig($soapConfig);
        return $this;
    }

    /**
     * Dispatch request to SOAP endpoint.
     *
     * @return Mage_Api2_Controller_Front_Soap
     */
    public function dispatch()
    {
        $this->_setResponseContentType('application/soap+xml');
        try {
            if ($this->getRequest()->getParam('wsdl') !== null) {
                $responseBody = $this->_getWsdlContent();
                $this->_setResponseContentType('text/xml');
            } else {
                $responseBody = $this->_getSoapServer()->handle();
            }
        } catch (Exception $e) {
            $responseBody = $this->_getSoapFaultMessage();
        }
        $this->_setResponseBody($responseBody);
        $this->getResponse()->sendResponse();
        return $this;
    }

    /**
     * Generate WSDL content based on resource config.
     *
     * @return string
     */
    protected function _getWsdlContent()
    {
        if (Mage::app()->useCache(self::WEBSERVICE_CACHE_NAME)) {
            $cachedWsdlContent = Mage::app()->getCache()->load(self::WSDL_CACHE_ID);
            if ($cachedWsdlContent !== false) {
                return $cachedWsdlContent;
            }
        }

        /** @var Mage_Api2_Model_Config_Wsdl $wsdlConfig */
        $wsdlConfig = Mage::getModel('Mage_Api2_Model_Config_Wsdl', array(
            'resource_config' => $this->getResourceConfig(),
            'endpoint_url' => $this->_getEndpointUrl(),
        ));
        $wsdlContent = $wsdlConfig->generate();

        if (Mage::app()->useCache(self::WEBSERVICE_CACHE_NAME)) {
            Mage::app()->getCache()->save($wsdlContent, self::WSDL_CACHE_ID, array(self::WEBSERVICE_CACHE_TAG));
        }

        return $wsdlContent;
    }

    /**
     * Identify parameters for the specified method
     *
     * @param string $className
     * @param string $methodName
     * @return array
     */
    protected function _fetchMethodParams($className, $methodName)
    {

        $method = new ReflectionMethod($className, $methodName);
        return $method->getParameters();
    }

    /**
     * Prepares SOAP operation arguments for passing to controller action method: <br/>
     * - sort in correct order <br/>
     * - set default values for omitted arguments
     *
     * @param array $actionParams Action parameters
     * @param array $soapArguments SOAP operation arguments
     * @return array
     */
    public function _prepareMethodParams($actionParams, $soapArguments)
    {
        $preparedParams = array();
        /** @var $parameter ReflectionParameter */
        foreach ($actionParams as $parameter) {
            $parameterName = $parameter->getName();
            if (isset($soapArguments[$parameterName])) {
                $preparedParams[$parameterName] = $soapArguments[$parameterName];
            } else {
                if ($parameter->isOptional()) {
                    $preparedParams[$parameterName] = $parameter->getDefaultValue();
                } else {
                    $errorMessage = "Required parameter \"$parameterName\" is missing.";
                    Mage::logException(new Exception($errorMessage, 0));
                    $this->_soapFault($errorMessage, self::FAULT_CODE_SENDER);
                }
            }
        }
        return $preparedParams;
    }

    /**
     * Retrieve SOAP server. Instantiate it during the first execution
     *
     * @return Zend_Soap_Server
     * @throws SoapFault
     */
    protected function _getSoapServer()
    {
        if (is_null($this->_soapServer)) {
            $this->_initWsdlCache();
            $soapSchemaImportTriesCount = 0;
            do {
                $soapSchemaImportFailed = false;
                try {
                    $this->_soapServer = new Zend_Soap_Server($this->_getWsdlUrl(),
                        array('encoding' => $this->_getApiCharset()));
                } catch (SoapFault $e) {
                    if (false !== strpos($e->getMessage(),
                        "Can't import schema from 'http://schemas.xmlsoap.org/soap/encoding/'")
                    ) {
                        $soapSchemaImportFailed = true;
                        $soapSchemaImportTriesCount++;
                        sleep(1);
                    } else {
                        throw $e;
                    }
                }
            } while ($soapSchemaImportFailed && $soapSchemaImportTriesCount < 5);
            use_soap_error_handler(false);
            // Front controller plays the role of SOAP handler
            $this->_soapServer->setReturnResponse(true)->setObject($this);
        }
        return $this->_soapServer;
    }

    /**
     * Set content type to response object
     *
     * @param string $contentType
     * @return Mage_Api2_Controller_Front_Soap
     */
    protected function _setResponseContentType($contentType = 'text/xml')
    {
        $this->getResponse()->clearHeaders()
            ->setHeader('Content-Type', "$contentType; charset={$this->_getApiCharset()}");
        return $this;
    }

    /**
     * Set body to response object
     *
     * @param string $responseBody
     * @return Mage_Api2_Controller_Front_Soap
     */
    protected function _setResponseBody($responseBody)
    {
        $this->getResponse()->setBody(preg_replace(
                '/<\?xml version="([^\"]+)"([^\>]+)>/i',
                '<?xml version="$1" encoding="' . $this->_getApiCharset() . '"?>',
                $responseBody
            )
        );
        return $this;
    }

    /**
     * Retrieve charset used in API
     *
     * @return string
     */
    protected function _getApiCharset()
    {
        // TODO: What do we need this charset for?
        return Mage::getStoreConfig('api/config/charset');
    }

    /**
     * Enable or disable SOAP extension WSDL cache depending on Magento configuration
     */
    protected function _initWsdlCache()
    {
        $wsdlCacheEnabled = (bool)Mage::getStoreConfig('api/config/wsdl_cache_enabled');
        if ($wsdlCacheEnabled) {
            ini_set('soap.wsdl_cache_enabled', '1');
        } else {
            ini_set('soap.wsdl_cache_enabled', '0');
        }
    }

    /**
     * Get WSDL file URL
     *
     * @return string
     */
    protected function _getWsdlUrl()
    {
        return $this->_getEndpointUrl() . '?wsdl';
    }

    /**
     * Get SOAP endpoint URL
     *
     * @return string
     */
    protected function _getEndpointUrl()
    {
        // @TODO: Implement proper endpoint URL retrieval mechanism in APIA-718 story
        return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . 'api/soap';
    }

    /**
     * Generate SOAP fault
     *
     * @param string $reason Human-readable explanation of the fault
     * @param string $code SOAP fault code
     * @param Exception $e Exception can be used to add information to Detail node of SOAP message
     * @throws SoapFault
     */
    protected function _soapFault($reason = self::FAULT_REASON_INTERNAL, $code = self::FAULT_CODE_RECEIVER,
        Exception $e = null
    ) {
        if ($this->_isSoapExtensionLoaded()) {
            $details = null;
            if (!is_null($e)) {
                $details = (object)array('ExceptionCode' => $e->getCode());
                // add detailed message only if it differs from fault reason
                if ($e->getMessage() != $reason) {
                    $details->ExceptionMessage = $e->getMessage();
                }
            }
            throw new SoapFault($code, $reason, null, $details);
        } else {
            die($this->_getSoapFaultMessage(self::FAULT_CODE_RECEIVER, 'SOAP extension is not loaded.'));
        }
    }

    /**
     * Generate SOAP fault message in xml format
     *
     * @param string $reason Human-readable explanation of the fault
     * @param string $code SOAP fault code
     * @return string
     */
    protected function _getSoapFaultMessage($reason = self::FAULT_REASON_INTERNAL, $code = self::FAULT_CODE_RECEIVER)
    {
        $message = <<<FAULT_MESSAGE
<env:Envelope xmlns:env="http://www.w3.org/2003/05/soap-envelope">
   <env:Body>
      <env:Fault>
         <env:Code>
            <env:Value>$code</env:Value>
         </env:Code>
         <env:Reason>
            <env:Text>$reason</env:Text>
         </env:Reason>
      </env:Fault>
   </env:Body>
</env:Envelope>
FAULT_MESSAGE;
        return $message;
    }

    /**
     * Check whether SOAP extension is loaded or not
     *
     * @return boolean
     */
    protected function _isSoapExtensionLoaded()
    {
        return class_exists('SoapServer', false);
    }

    /**
     * Set SOAP config
     *
     * @param Mage_Api2_Model_Config_Soap $config
     * @return Mage_Api2_Model_Config_Soap
     */
    public function setSoapConfig(Mage_Api2_Model_Config_Soap $config)
    {
        $this->_soapConfig = $config;
        return $this;
    }

    /**
     * Retrieve SOAP specific config
     *
     * @return Mage_Api2_Model_Config_Soap
     */
    public function getSoapConfig()
    {
        return $this->_soapConfig;
    }
}
