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
    protected $_resourceName;

    /** @var string */
    protected $_httpMethod;

    /** @var string */
    protected $_methodName;

    /**
     * Set route resource.
     *
     * @param string $resourceName
     * @return Mage_Webapi_Controller_Router_Route_Rest
     */
    public function setResourceName($resourceName)
    {
        $this->_resourceName = $resourceName;
        return $this;
    }

    /**
     * Get route resource.
     *
     * @return string
     */
    public function getResourceName()
    {
        return $this->_resourceName;
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
