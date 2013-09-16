<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_Sales_Model_Quote_Config
{
    const XML_PATH_QUOTE_PRODUCT_ATTRIBUTES = 'global/sales/quote/item/product_attributes';

    /**
     * Core event manager proxy
     *
     * @var Magento_Core_Model_Event_Manager
     */
    protected $_eventManager = null;

    /**
     * @var Magento_Core_Model_Config
     */
    protected $_coreConfig;

    /**
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Model_Config $coreConfig
     */
    public function __construct(
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Model_Config $coreConfig
    ) {
        $this->_eventManager = $eventManager;
        $this->_coreConfig = $coreConfig;
    }

    public function getProductAttributes()
    {
        $attributes = $this->_coreConfig->getNode(self::XML_PATH_QUOTE_PRODUCT_ATTRIBUTES)->asArray();
        $transfer = new Magento_Object($attributes);
        $this->_eventManager->dispatch('sales_quote_config_get_product_attributes', array('attributes' => $transfer));
        $attributes = $transfer->getData();
        return array_keys($attributes);
    }

    public function getTotalModels()
    {

    }
}
