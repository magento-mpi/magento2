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
namespace Magento\Api\Model\Resource;

class AbstractResource
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
     * @return \Magento\Api\Model\Session
     */
    protected function _getSession()
    {
        return \Mage::getSingleton('Magento\Api\Model\Session');
    }

    /**
     * Retrieve webservice configuration
     *
     * @return \Magento\Api\Model\Config
     */
    protected function _getConfig()
    {
        return \Mage::getSingleton('Magento\Api\Model\Config');
    }

    /**
     * Set configuration for api resource
     *
     * @param \Magento\Simplexml\Element $xml
     * @return \Magento\Api\Model\Resource\AbstractResource
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
     * @return \Magento\Api\Model\Server
     */
    protected function _getServer()
    {
        return \Mage::getSingleton('Magento\Api\Model\Server');
    }

    /**
     * Dispatches fault
     *
     * @param string $code
     */
    protected function _fault($code, $customMessage=null)
    {
        throw new \Magento\Api\Exception($code, $customMessage);
    }
} // Class \Magento\Api\Model\Resource\AbstractResource End
