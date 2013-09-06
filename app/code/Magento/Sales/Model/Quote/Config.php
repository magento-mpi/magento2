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
     * @var Magento_Core_Model_Config
     */
    protected $_coreConfig;

    /**
     * Constructor
     *
     * @param Magento_Core_Model_Config $coreConfig
     */
    public function __construct(
        Magento_Core_Model_Config $coreConfig
    ) {
        $this->_coreConfig = $coreConfig;
    }

    public function getProductAttributes()
    {
        $attributes = $this->_coreConfig->getNode(self::XML_PATH_QUOTE_PRODUCT_ATTRIBUTES)->asArray();
        $transfer = new Magento_Object($attributes);
        Mage::dispatchEvent('sales_quote_config_get_product_attributes', array('attributes' => $transfer));
        $attributes = $transfer->getData();
        return array_keys($attributes);
    }

    public function getTotalModels()
    {

    }
}
