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
    const PARAM_REQUEST_TYPE = 'request_type';
    const VERSION_NUMBER_PREFIX = 'V';

    /**
     * Modify pathInfo: strip down the request type and query.
     *
     * @param Mage_Core_Model_Config $config
     * @param string $requestType
     * @param null|string|Zend_Uri $uri
     * @throws LogicException
     */
    public function __construct(Mage_Core_Model_Config $config, $requestType, $uri = null)
    {
        parent::__construct($uri);
        $pattern = '#.*?/' . $requestType . '/#';
        /** Set path info without area, API type and GET query params */
        $this->_pathInfo = '/' . preg_replace(array($pattern, '/\?.*/'), array('', ''), $this->_requestUri);
    }
}
