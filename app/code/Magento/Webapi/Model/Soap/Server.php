<?php
/**
 * Magento-specific SOAP server.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Model_Soap_Server
{
    const SOAP_DEFAULT_ENCODING = 'UTF-8';

    /**#@+
     * Path in config to Webapi settings.
     */
    const CONFIG_PATH_WSDL_CACHE_ENABLED = 'webapi/soap/wsdl_cache_enabled';
    const CONFIG_PATH_SOAP_CHARSET = 'webapi/soap/charset';
    /**#@-*/

    const REQUEST_PARAM_SERVICES = 'services';
    const REQUEST_PARAM_WSDL = 'wsdl';

    /** @var Magento_Core_Model_Config */
    protected $_applicationConfig;

    /** @var Magento_DomDocument_Factory */
    protected $_domDocumentFactory;

    /** @var Magento_Webapi_Controller_Soap_Request */
    protected $_request;

    /** @var Magento_Core_Model_StoreManagerInterface */
    protected $_storeManager;

    /** @var Magento_Webapi_Model_Soap_Server_Factory */
    protected $_soapServerFactory;

    /**
     * Initialize dependencies, initialize WSDL cache.
     *
     * @param Magento_Core_Model_Config $applicationConfig
     * @param Magento_Webapi_Controller_Soap_Request $request
     * @param Magento_DomDocument_Factory $domDocumentFactory
     * @param Magento_Core_Model_StoreManagerInterface
     * @param Magento_Webapi_Model_Soap_Server_Factory
     * @throws Magento_Webapi_Exception with invalid SOAP extension
     */
    public function __construct(
        Magento_Core_Model_Config $applicationConfig,
        Magento_Webapi_Controller_Soap_Request $request,
        Magento_DomDocument_Factory $domDocumentFactory,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Webapi_Model_Soap_Server_Factory $soapServerFactory
    ) {
        if (!extension_loaded('soap')) {
            throw new Magento_Webapi_Exception('SOAP extension is not loaded.', 0,
                Magento_Webapi_Exception::HTTP_INTERNAL_ERROR);
        }
        $this->_applicationConfig = $applicationConfig;
        $this->_request = $request;
        $this->_domDocumentFactory = $domDocumentFactory;
        $this->_storeManager = $storeManager;
        $this->_soapServerFactory = $soapServerFactory;
        /** Enable or disable SOAP extension WSDL cache depending on Magento configuration. */
        $wsdlCacheEnabled = (bool)$storeManager->getStore()->getConfig(self::CONFIG_PATH_WSDL_CACHE_ENABLED);
        if ($wsdlCacheEnabled) {
            ini_set('soap.wsdl_cache_enabled', '1');
        } else {
            ini_set('soap.wsdl_cache_enabled', '0');
        }
    }

    /**
     * Handle SOAP request.
     *
     * @return string
     */
    public function handle()
    {
        $rawRequestBody = file_get_contents('php://input');
        $this->_checkRequest($rawRequestBody);
        $options = array(
            'encoding' => $this->getApiCharset(),
            'soap_version' => SOAP_1_2
        );
        $soap = $this->_soapServerFactory->create($this->generateUri(true), $options);
        return $soap->handle($rawRequestBody);
    }

    /**
     * Retrieve charset used in SOAP API.
     *
     * @return string
     */
    public function getApiCharset()
    {
        $charset = $this->_storeManager->getStore()->getConfig(self::CONFIG_PATH_SOAP_CHARSET);
        return $charset ? $charset : Magento_Webapi_Model_Soap_Server::SOAP_DEFAULT_ENCODING;
    }

    /**
     * Get SOAP endpoint URL.
     *
     * @param bool $isWsdl
     * @return string
     */
    public function generateUri($isWsdl = false)
    {
        $params = array(
            self::REQUEST_PARAM_SERVICES => $this->_request->getParam(
                Magento_Webapi_Model_Soap_Server::REQUEST_PARAM_SERVICES
            )
        );
        if ($isWsdl) {
            $params[self::REQUEST_PARAM_WSDL] = true;
        }
        $query = http_build_query($params, '', '&');
        return $this->getEndpointUri() . '?' . $query;
    }

    /**
     * Generate URI of SOAP endpoint.
     *
     * @return string
     */
    public function getEndpointUri()
    {
        return $this->_storeManager->getStore()->getBaseUrl() . $this->_applicationConfig->getAreaFrontName();
    }

    /**
     * Generate exception if request is invalid.
     *
     * @param string $soapRequest
     * @throws Magento_Webapi_Exception with invalid SOAP extension
     * @return Magento_Webapi_Model_Soap_Server
     */
    protected function _checkRequest($soapRequest)
    {
        $dom = new DOMDocument();
        if (strlen($soapRequest) == 0 || !$dom->loadXML($soapRequest)) {
            throw new Magento_Webapi_Exception(__('Invalid XML'), 0, Magento_Webapi_Exception::HTTP_INTERNAL_ERROR);
        }
        foreach ($dom->childNodes as $child) {
            if ($child->nodeType === XML_DOCUMENT_TYPE_NODE) {
                throw new Magento_Webapi_Exception(__('Invalid XML: Detected use of illegal DOCTYPE'), 0,
                    Magento_Webapi_Exception::HTTP_INTERNAL_ERROR);
            }
        }
        return $this;
    }
}
