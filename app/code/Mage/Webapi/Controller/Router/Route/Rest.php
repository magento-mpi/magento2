<?php
/**
 * Route to resources available via REST API.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_Controller_Router_Route_Rest extends Mage_Webapi_Controller_Router_Route
{
    /**#@+
     * Names of special parameters in routes.
     */
    const PARAM_VERSION = 'resourceVersion';
    const PARAM_ID = 'id';
    const PARAM_PARENT_ID = 'parentId';
    /**#@-*/

    /** @var string */
    protected $_serviceName;

    /** @var string */
    protected $_httpMethod;

    /** @var string */
    protected $_methodName;

    /**
     * Set route resource.
     *
     * @param string $serviceName
     * @return Mage_Webapi_Controller_Router_Route_Rest
     */
    public function setServiceName($serviceName)
    {
        $this->_serviceName = $serviceName;
        return $this;
    }

    /**
     * Get route resource.
     *
     * @return string
     */
    public function getServiceName()
    {
        return $this->_serviceName;
    }

    /**
     * Set HTTP method associated with current route.
     *
     * @param string $httpMethod
     * @return Mage_Webapi_Controller_Router_Route_Rest
     */
    public function setHttpMethod($httpMethod)
    {
        $this->_httpMethod = $httpMethod;
        return $this;
    }

    /**
     * Get HTTP method associated with current route.
     *
     * @return string
     */
    public function getHttpMethod()
    {
        return $this->_httpMethod;
    }

    /**
     * Retrieve service method name.
     *
     * @return string
     */
    public function getMethodName()
    {
        return $this->_methodName;
    }

    /**
     * Set service method name.
     *
     * @param string $methodName
     * @return Mage_Webapi_Controller_Router_Route_Rest
     */
    public function setMethodName($methodName)
    {
        $this->_methodName = $methodName;
        return $this;
    }
}
