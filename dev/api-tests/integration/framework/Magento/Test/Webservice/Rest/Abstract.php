<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

abstract class Magento_Test_Webservice_Rest_Abstract extends Magento_Test_Webservice
{
    /**
     * REST Webservice adapter instances registry
     *
     * @var array
     */
    protected static $_adapterRegistry = array();

    /**
     * REST Webservice user type (admin/customer/guest)
     *
     * @var string|null
     */
    protected $_userType;

    /**
     * Get adapter instance
     *
     * @return Magento_Test_Webservice_Rest_Adapter
     */
    protected function getInstance()
    {
        $instance = null;
        if (isset(self::$_adapterRegistry[$this->_userType])){
            $instance = self::$_adapterRegistry[$this->_userType];
        }

        return $instance;
    }

    /**
     * Set adapter instance
     *
     * @param Magento_Test_Webservice_Rest_Adapter $instance
     */
    protected function setInstance(Magento_Test_Webservice_Rest_Adapter $instance)
    {
        self::$_adapterRegistry[$this->_userType] = $instance;
    }

    /**
     * Get webservice adapter
     *
     * @param array $options
     * @return Magento_Test_Webservice_Rest_Adapter
     */
    public function getWebService($options = null)
    {
        if (null === $this->getInstance()) {
            $this->setInstance(new Magento_Test_Webservice_Rest_Adapter());
            $options['type'] = $this->_userType;
            $this->getInstance()->init($options);
        }

        return $this->getInstance();
    }

    /**
     * REST GET
     *
     * @param string $resourceName
     * @param array $params
     * @return Magento_Test_Webservice_Rest_ResponseDecorator
     */
    public function callGet($resourceName, $params = array())
    {
        if (null === $this->getInstance()) {
            $this->getWebService();
        }

        return $this->getInstance()->callGet($resourceName, $params);
    }

    /**
     * REST DELETE
     *
     * @param string $resourceName
     * @param array $params
     * @return Magento_Test_Webservice_Rest_ResponseDecorator
     */
    public function callDelete($resourceName, $params = array())
    {
        if (null === $this->getInstance()) {
            $this->getWebService();
        }

        return $this->getInstance()->callDelete($resourceName, $params);
    }

    /**
     * REST POST
     *
     * @param string $resourceName
     * @param array $params
     * @return Magento_Test_Webservice_Rest_ResponseDecorator
     */
    public function callPost($resourceName, $params)
    {
        if (null === $this->getInstance()) {
            $this->getWebService();
        }

        return $this->getInstance()->callPost($resourceName, $params);
    }

    /**
     * REST PUT
     *
     * @param string $resourceName
     * @param array $params
     * @return Magento_Test_Webservice_Rest_ResponseDecorator
     */
    public function callPut($resourceName, $params)
    {
        if (null === $this->getInstance()) {
            $this->getWebService();
        }

        return $this->getInstance()->callPut($resourceName, $params);
    }
}
