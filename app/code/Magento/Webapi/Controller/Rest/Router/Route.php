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
    protected $_serviceClass;

    /** @var string */
    protected $_serviceMethod;

    /** @var boolean */
    protected $_secure;

    /**
     * Set service class.
     *
     * @param string $serviceClass
     * @return Magento_Webapi_Controller_Rest_Router_Route
     */
    public function setServiceClass($serviceClass)
    {
        $this->_serviceClass = $serviceClass;
        return $this;
    }

    /**
     * Get service class.
     *
     * @return string
     */
    public function getServiceClass()
    {
        return $this->_serviceClass;
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
