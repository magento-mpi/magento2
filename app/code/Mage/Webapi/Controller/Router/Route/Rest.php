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
    protected $_serviceId;

    /** @var string */
    protected $_serviceMethod;

    /** @var string */
    protected $_serviceVersion;

    /** @var string */
    protected $_httpMethod;

    /**
     * Set service service ID.
     *
     * @param string $serviceClass
     * @return Mage_Webapi_Controller_Router_Route_Rest
     */
    public function setServiceId($serviceClass)
    {
        $this->_serviceId = $serviceClass;
        return $this;
    }

    /**
     * Get service ID.
     *
     * @return string
     */
    public function getServiceId()
    {
        return $this->_serviceId;
    }

    /**
     * Set service method name.
     *
     * @param string $serviceMethod
     * @return Mage_Webapi_Controller_Router_Route_Rest
     */
    public function setServiceMethod($serviceMethod)
    {
        $this->_serviceMethod = $serviceMethod;
        return $this;
    }

    /**
     * Get service method name.
     *
     * @return string
     */
    public function getServiceMethod()
    {
        return $this->_serviceMethod;
    }

    /**
     * Set service version.
     *
     * @param string $serviceVersion
     * @return Mage_Webapi_Controller_Router_Route_Rest
     */
    public function setServiceVersion($serviceVersion)
    {
        $this->_serviceVersion = $serviceVersion;
        return $this;
    }

    /**
     * Get service version.
     *
     * @return string
     */
    public function getServiceVersion()
    {
        return $this->_serviceVersion;
    }

    /**
     * Set route HTTP method.
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
     * Get route resource type.
     *
     * @return string
     */
    public function getHttpMethod()
    {
        return $this->_httpMethod;
    }
}
