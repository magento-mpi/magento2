<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Api
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Api resource abstract
 *
 * @category   Magento
 * @package    Magento_Api
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Api_Model_Resource_Abstract
{

    /**
     * Resource configuration
     *
     * @var \Magento\Simplexml\Element
     */
    protected $_resourceConfig = null;

    /**
     * Retrieve webservice session
     *
     * @return Magento_Api_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('Magento_Api_Model_Session');
    }

    /**
     * Retrieve webservice configuration
     *
     * @return Magento_Api_Model_Config
     */
    protected function _getConfig()
    {
        return Mage::getSingleton('Magento_Api_Model_Config');
    }

    /**
     * Set configuration for api resource
     *
     * @param \Magento\Simplexml\Element $xml
     * @return Magento_Api_Model_Resource_Abstract
     */
    public function setResourceConfig(\Magento\Simplexml\Element $xml)
    {
        $this->_resourceConfig = $xml;
        return $this;
    }

    /**
     * Retrieve configuration for api resource
     *
     * @return \Magento\Simplexml\Element
     */
    public function getResourceConfig()
    {
        return $this->_resourceConfig;
    }

    /**
     * Retrieve webservice server
     *
     * @return Magento_Api_Model_Server
     */
    protected function _getServer()
    {
        return Mage::getSingleton('Magento_Api_Model_Server');
    }

    /**
     * Dispatches fault
     *
     * @param string $code
     */
    protected function _fault($code, $customMessage=null)
    {
        throw new Magento_Api_Exception($code, $customMessage);
    }
} // Class Magento_Api_Model_Resource_Abstract End
