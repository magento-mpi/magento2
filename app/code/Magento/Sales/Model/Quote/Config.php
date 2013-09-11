<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\Sales\Model\Quote;

class Config
{
    const XML_PATH_QUOTE_PRODUCT_ATTRIBUTES = 'global/sales/quote/item/product_attributes';

    public function getProductAttributes()
    {
        $attributes = \Mage::getConfig()->getNode(self::XML_PATH_QUOTE_PRODUCT_ATTRIBUTES)->asArray();
        $transfer = new \Magento\Object($attributes);
        \Mage::dispatchEvent('sales_quote_config_get_product_attributes', array('attributes' => $transfer));
        $attributes = $transfer->getData();
        return array_keys($attributes);
    }

    public function getTotalModels()
    {

    }
}
