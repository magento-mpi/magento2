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
    /**
     * Modify pathInfo: strip down the request type and query.
     *
     * @param Mage_Core_Model_App $application
     * @param null|string|Zend_Uri $uri
     * @throws LogicException
     */
    public function __construct($application, $uri = null)
    {
        parent::__construct($uri);
        $pattern = '#.*?/' . $application->getConfig()->getAreaFrontName() . '/#';
        /** Set path info without area, API type and GET query params */
        $this->_pathInfo = '/' . preg_replace(array($pattern, '/\?.*/'), array('', ''), $this->_requestUri);
    }
}
