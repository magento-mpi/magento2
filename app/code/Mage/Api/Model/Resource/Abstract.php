<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Api
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Api resource abstract
 *
 * @category   Mage
 * @package    Mage_Api
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api_Model_Resource_Abstract
{

    /**
     * Resource configuration
     *
     * @var Magento_Simplexml_Element
     */
    protected $_resourceConfig = null;

    /**
     * Retrieve webservice session
     *
     * @return Mage_Api_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('Mage_Api_Model_Session');
    }

    /**
     * Retrieve webservice configuration
     *
     * @return Mage_Api_Model_Config
     */
    protected function _getConfig()
    {
        return Mage::getSingleton('Mage_Api_Model_Config');
    }

    /**
     * Set configuration for api resource
     *
     * @param Magento_Simplexml_Element $xml
     * @return Mage_Api_Model_Resource_Abstract
     */
    public function setResourceConfig(Magento_Simplexml_Element $xml)
    {
        $this->_resourceConfig = $xml;
        return $this;
    }

    /**
     * Retrieve configuration for api resource
     *
     * @return Magento_Simplexml_Element
     */
    public function getResourceConfig()
    {
        return $this->_resourceConfig;
    }

    /**
     * Retrieve webservice server
     *
     * @return Mage_Api_Model_Server
     */
    protected function _getServer()
    {
        return Mage::getSingleton('Mage_Api_Model_Server');
    }

    /**
     * Dispatches fault
     *
     * @param string $code
     */
    protected function _fault($code, $customMessage=null)
    {
        throw new Mage_Api_Exception($code, $customMessage);
    }
} // Class Mage_Api_Model_Resource_Abstract End
