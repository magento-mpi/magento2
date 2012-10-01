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
class Mage_Webapi_Controller_Front_Soap extends Mage_Webapi_Controller_FrontAbstract
{
    const BASE_ACTION_CONTROLLER = 'Mage_Webapi_Controller_ActionAbstract';

    const FAULT_CODE_SENDER = 'Sender';
    const FAULT_CODE_RECEIVER = 'Receiver';

    const FAULT_REASON_INTERNAL = 'Internal Error.';

    const WEBSERVICE_CACHE_NAME = 'config_webservice';
    const WEBSERVICE_CACHE_TAG = 'WEBSERVICE';
    const WSDL_CACHE_ID = 'WSDL';

    /** @var Zend_Soap_Server */
    protected $_soapServer;

    /** @var Mage_Webapi_Model_Config_Soap */
    protected $_soapConfig;

    /**
     * Handler for all SOAP operations
     *
     * @param string $operation
     * @param array $arguments
     * @return stdClass
     */
    // TODO: Think about situations when custom error handler is required for this method (that can throw SOAP faults)
    public function __call($operation, $arguments)
    {
        $role = $this->_authenticate($this->getRequest());
        $this->_checkOperationDeprecation($operation);
        $resourceName = $this->getResourceConfig()->getResourceNameByOperation($operation);
        if (!$resourceName) {
            $this->_soapFault(sprintf('Method "%s" not found.', $operation), self::FAULT_CODE_SENDER);
        }
        $controllerClass = $this->getSoapConfig()->getControllerClassByResourceName($resourceName);
        $controllerInstance = $this->_getActionControllerInstance($controllerClass);
        $method = $this->getResourceConfig()->getMethodNameByOperation($operation);
        try {
            $this->_checkResourceAcl($role, $resourceName, $method);

            $arguments = reset($arguments);
            /** @var Mage_Api_Helper_Data $apiHelper */
            $apiHelper = Mage::helper('Mage_Api_Helper_Data');
            $this->getHelper()->toArray($arguments);
            $action = $method . $this->_getVersionSuffix($operation, $controllerInstance);
            $arguments = $this->getHelper()->prepareMethodParams($controllerClass, $action, $arguments);
//            $inputData = $this->_presentation->fetchRequestData($operation, $controllerInstance, $action);
            $outputData = call_user_func_array(array($controllerInstance, $action), $arguments);
            // TODO: Implement response preparation according to current presentation
//            $this->_presentation->prepareResponse($operation, $outputData);
            // TODO: Move wsiArrayPacker from helper to this class
            $obj = $apiHelper->wsiArrayPacker($outputData);
            $stdObj = new stdClass();
            $stdObj->result = $obj;
            return $stdObj;
            // TODO: Implement proper exception handling
        } catch (Mage_Api_Exception $e) {
            $this->_soapFault($e->getCustomMessage(), self::FAULT_CODE_RECEIVER, $e);
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_soapFault($e->getMessage());
        }
    }

    /**
     * Authenticate user
     * @todo remove fake authentication code
     *
     * @param Mage_Webapi_Controller_RequestAbstract $request
     * @return string
     */
    protected function _authenticate(Mage_Webapi_Controller_RequestAbstract $request)
    {
        /** @var $collection Mage_Webapi_Model_Resource_Acl_User_Collection */
        $collection = Mage::getResourceModel('Mage_Webapi_Model_Resource_Acl_User_Collection');
        /** @var $user Mage_Webapi_Model_Acl_User */
        $user = $collection->getFirstItem();
        return $user->getRoleId();
    }

    /**
     * Get SOAP request.
     *
     * @return Mage_Webapi_Controller_Request_Soap
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * Extend parent with SOAP specific config initialization
     *
     * @return Mage_Webapi_Controller_Front_Soap|Mage_Core_Controller_FrontInterface
     */
    public function init()
    {
        $soapConfigFiles = Mage::getConfig()->getModuleConfigurationFiles('webapi/soap.xml');
        /** @var Mage_Webapi_Model_Config_Soap $soapConfig */
        $soapConfig = Mage::getModel('Mage_Webapi_Model_Config_Soap', $soapConfigFiles);
        $this->setSoapConfig($soapConfig);
        return $this;
    }

    /**
     * Dispatch request to SOAP endpoint.
     *
     * @return Mage_Webapi_Controller_Front_Soap
     */
    public function dispatch()
    {
        try {
            $this->_initResourceConfig($this->getRequest()->getRequestedModules());
            if ($this->getRequest()->getParam('wsdl') !== null) {
                $this->_setResponseContentType('text/xml');
                $responseBody = $this->_getWsdlContent();
            } else {
                $this->_setResponseContentType('application/soap+xml');
                $responseBody = $this->_getSoapServer()->handle();
            }
            $this->_setResponseBody($responseBody);
        } catch (RuntimeException $e) {
            self::_processBadRequest($e->getMessage());
        } catch (Exception $e) {
            self::_processBadRequest($this->_helper->__('Internal error.'));
        }

        $this->getResponse()->sendResponse();
        return $this;
    }

    /**
     * Process request as HTTP 400 and set error message.
     *
     * @param string $message
     */
    protected function _processBadRequest($message)
    {
        $this->_setResponseContentType('text/plain');
        $this->getResponse()->setHttpResponseCode(400);

        $response = $this->_helper->__('HTTP/1.1 400 Bad Request.') . PHP_EOL . PHP_EOL . $message;
        $this->_setResponseBody($response);
    }

    /**
     * Generate WSDL content based on resource config.
     *
     * @return string
     */
    protected function _getWsdlContent()
    {
        $requestedModules = $this->getRequest()->getRequestedModules();
        $cacheId = self::WSDL_CACHE_ID . hash('md5', serialize($requestedModules));
        if (Mage::app()->getCacheInstance()->canUse(self::WEBSERVICE_CACHE_NAME)) {
            $cachedWsdlContent = Mage::app()->getCacheInstance()->load($cacheId);
            if ($cachedWsdlContent !== false) {
                return $cachedWsdlContent;
            }
        }

        /** @var Mage_Webapi_Model_Config_Wsdl $wsdlConfig */
        $wsdlConfig = Mage::getModel('Mage_Webapi_Model_Config_Wsdl', array(
            'resource_config' => $this->getResourceConfig(),
            'endpoint_url' => $this->_getEndpointUrl(),
        ));
        $wsdlContent = $wsdlConfig->generate();

        if (Mage::app()->getCacheInstance()->canUse(self::WEBSERVICE_CACHE_NAME)) {
            Mage::app()->getCacheInstance()->save($wsdlContent, $cacheId, array(self::WEBSERVICE_CACHE_TAG));
        }

        return $wsdlContent;
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
     * @return Mage_Webapi_Controller_Front_Soap
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
     * @return Mage_Webapi_Controller_Front_Soap
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
        return $this->_getEndpointUrl(true);
    }

    /**
     * Get SOAP endpoint URL
     *
     * @param bool $isWsdl
     * @return string
     */
    protected function _getEndpointUrl($isWsdl = false)
    {
        $params = array(
            'modules' => $this->getRequest()->getRequestedModules()
        );
        if ($isWsdl) {
            $params['wsdl'] = true;
        }
        $query = http_build_query($params, '', '&');
        // @TODO: Implement proper endpoint URL retrieval mechanism in APIA-718 story
        return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . 'api/soap?' . $query;
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
     * @param Mage_Webapi_Model_Config_Soap $config
     * @return Mage_Webapi_Model_Config_Soap
     */
    public function setSoapConfig(Mage_Webapi_Model_Config_Soap $config)
    {
        $this->_soapConfig = $config;
        return $this;
    }

    /**
     * Retrieve SOAP specific config
     *
     * @return Mage_Webapi_Model_Config_Soap
     */
    public function getSoapConfig()
    {
        return $this->_soapConfig;
    }
}
