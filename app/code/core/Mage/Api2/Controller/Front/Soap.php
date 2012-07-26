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
    /**
     * @var Zend_Soap_Server
     */
    protected $_soapServer;

    /**
     * Handler for all SOAP operations
     *
     * @param string $method
     * @param array $arguments
     */
    public function __call($method, $arguments)
    {
        // TODO: Routing

        // TODO: ACL check

        // TODO: Method invocation
    }

    public function init()
    {
    }

    public function dispatch()
    {
        try {
            if ($this->getRequest()->getParam('wsdl') !== null) {
                // TODO: Load WSDL
                $wsdl = '<?xml version="1.0" encoding="UTF-8"?><fake>TODO: Implement WSDL content loading</fake>';
                $responseBody = $wsdl;
            } else {
                $responseBody = $this->_getSoapServer()->handle();
            }
            $this->_setResponseBody($responseBody);
        } catch (Exception $e) {
            // TODO: Fix logic error. Who is responsible for handling this soap fault?
            $this->_soapFault($e->getMessage(), $e);
        }
        $this->getResponse()->sendResponse();
        return $this;
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
                    $apiConfigCharset = Mage::getStoreConfig('api/config/charset');
                    $this->_soapServer = new Zend_Soap_Server($this->_getWsdlUrl(), array('encoding' => $apiConfigCharset));
                } catch (SoapFault $e) {
                    if (false !== strpos($e->getMessage(),
                        "can't import schema from 'http://schemas.xmlsoap.org/soap/encoding/'")
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
            /** Front controller plays role of SOAP handler */
            $this->_soapServer->setReturnResponse(true)->setClass($this);
        }
        return $this->_soapServer;
    }

    /**
     * Prepare response object and set body to it
     *
     * @param string $responseBody
     * @return Mage_Api2_Controller_Front_Soap
     */
    protected function _setResponseBody($responseBody)
    {
        // TODO: What do we need this charset for?
        $apiConfigCharset = Mage::getStoreConfig("api/config/charset");
        $this->getResponse()->clearHeaders()->setHeader('Content-Type', 'text/xml; charset=' . $apiConfigCharset)
            ->setBody(preg_replace(
                '/<\?xml version="([^\"]+)"([^\>]+)>/i',
                '<?xml version="$1" encoding="' . $apiConfigCharset . '"?>',
                $responseBody
            )
        );
        return $this;
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
     * @return string
     */
    protected function _getWsdlUrl()
    {
        // TODO: Implement
        $wsdlUrl = 'http://dd.varien.com/dev/alex.paliarush/api2/api/soap/?wsdl';
        return $wsdlUrl;
    }

    /**
     * Generate SOAP fault
     *
     * @param string $reason Human-readable explanation of the fault
     * @param Exception $e Exception can be used to add information to Detail node of SOAP message
     * @param string $code SOAP fault code
     * @throws SoapFault
     */
    protected function _soapFault($reason, Exception $e = null, $code = 'Sender')
    {
        $details = null;
        if (!is_null($e)) {
            $details = (object)array('ExceptionCode' => $e->getCode());
            // add detailed message only if it differs from fault reason
            if ($e->getMessage() != $reason) {
                $details->ExceptionMessage = $e->getMessage();
            }
        }
        if ($this->_isSoapExtensionLoaded()) {
            // TODO: Investigate if $this->_getSoapServer()->fault() can be used here
            throw new SoapFault($code, $reason, null, $details);
        } else {
            // TODO: die with proper fault message or put it into response object. Generate fault message manually
        }
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
}
