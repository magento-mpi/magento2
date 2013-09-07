<?php
/**
 * Route to services available via REST API.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Controller_Rest_Router_Route extends Zend_Controller_Router_Route
{
    /** @var string */
    protected $_serviceId;

    /** @var string */
    protected $_serviceMethod;

    /** @var string */
    protected $_serviceVersion;

    /** @var string */
    protected $_httpMethod;

    /** @var boolean */
    protected $_secure;

    /**
     * Set service service ID.
     *
     * @param string $serviceClass
     * @return Magento_Webapi_Controller_Rest_Router_Route
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
     * @return Magento_Webapi_Controller_Rest_Router_Route
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
     * @return Magento_Webapi_Controller_Rest_Router_Route
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
     * @return Magento_Webapi_Controller_Rest_Router_Route
     */
    public function setHttpMethod($httpMethod)
    {
        $this->_httpMethod = $httpMethod;
        return $this;
    }

    /**
     * Get route service type.
     *
     * @return string
     */
    public function getHttpMethod()
    {
        return $this->_httpMethod;
    }

    /**
     * Set if the route is secure
     *
     * @param boolean $secure
     * @return Magento_Webapi_Controller_Rest_Router_Route
     */
    public function setSecure($secure)
    {
        $this->_secure = $secure;
        return $this;
    }

    /**
     * Returns true if the route is secure
     *
     * @return boolean
     */
    public function isSecure()
    {
        return $this->_secure;
    }

    /**
     * Matches a Request with parts defined by a map. Assigns and
     * returns an array of variables on a successful match.
     *
     * @param Magento_Webapi_Controller_Request $request
     * @param boolean $partial Partial path matching
     * @return array|bool An array of assigned values or a boolean false on a mismatch
     */
    public function match($request, $partial = false)
    {
        return parent::match(strtolower(ltrim($request->getPathInfo(), $this->_urlDelimiter)), $partial);
    }
}
