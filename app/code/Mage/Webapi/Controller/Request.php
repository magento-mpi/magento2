<?php
/**
 * Web API request.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_Controller_Request extends Zend_Controller_Request_Http
{
    const PARAM_API_TYPE = 'api_type';
    const VERSION_NUMBER_PREFIX = 'V';

    /** @var string */
    protected $_apiType;

    /**
     * Set current API type.
     *
     * @param Mage_Core_Model_Config $config
     * @param string $apiType
     * @param null|string|Zend_Uri $uri
     */
    public function __construct(Mage_Core_Model_Config $config, $apiType, $uri = null)
    {
        $this->setApiType($apiType);
        parent::__construct($uri);
        $pattern = '#.*?/' . $config->getNode('global/areas/webapi/frontName') . '/\w+#';
        /** Set path info without area, API type and GET query params */
        $this->_pathInfo = preg_replace(array($pattern, '/\?.*/'), array('', ''), $this->_requestUri);
    }

    /**
     * Get current API type.
     *
     * @return string
     */
    public function getApiType()
    {
        return $this->_apiType;
    }

    /**
     * Set current API type.
     *
     * @param string $apiType
     */
    public function setApiType($apiType)
    {
        $this->_apiType = $apiType;
    }
}
