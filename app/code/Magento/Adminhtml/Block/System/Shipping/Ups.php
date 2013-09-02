<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml shipping UPS content block
 */
class Magento_Adminhtml_Block_System_Shipping_Ups extends Magento_Backend_Block_Template
{
    /**
     * @var Magento_Usa_Model_Shipping_Carrier_Ups
     */
    protected $_shippingModel;

    /**
     * @var Magento_Core_Model_Website
     */
    protected $_websiteModel;

    /**
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeConfig;

    /**
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param Magento_Usa_Model_Shipping_Carrier_Ups $shippingModel
     * @param Magento_Core_Model_Website $websiteModel
     * @param Magento_Core_Model_Store_Config $storeConfig
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_Store_Config $coreStoreConfig,
        Magento_Usa_Model_Shipping_Carrier_Ups $shippingModel,
        Magento_Core_Model_Website $websiteModel,
        Magento_Core_Model_Store_Config $storeConfig,
        array $data = array()
    ) {
        $this->_shippingModel = $shippingModel;
        $this->_websiteModel = $websiteModel;
        $this->_storeConfig = $storeConfig;
        parent::__construct($context, $coreStoreConfig, $data);
    }

    /**
     * Get shipping model
     *
     * @return Magento_Usa_Model_Shipping_Carrier_Ups
     */
    public function getShippingModel()
    {
        return $this->_shippingModel;
    }

    /**
     * Get website model
     *
     * @return Magento_Core_Model_Website
     */
    public function getWebsiteModel()
    {
        return $this->_websiteModel;
    }

    /**
     * Get store config
     *
     * @param string $path
     * @param mixed $store
     * @return mixed
     */
    public function getConfig($path, $store = null)
    {
        return $this->_storeConfig->getConfig($path, $store);
    }
}
