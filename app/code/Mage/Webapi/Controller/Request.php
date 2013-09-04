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
     * Modify pathInfo: strip down the front name and query parameters.
     *
     * @param Mage_Core_Model_App $app
     * @param null|string|Zend_Uri $uri
     */
    public function __construct($app, $uri = null)
    {
        parent::__construct($uri);
        $this->_pathInfo = $this->_requestUri;
        /** Remove base url and area from path */
        $this->_pathInfo = preg_replace("#.*?/{$app->getConfig()->getAreaFrontName()}/?#", '/', $this->_pathInfo);
        /** Remove GET parameters from path */
        $this->_pathInfo = preg_replace('#\?.*#', '', $this->_pathInfo);
    }
}
