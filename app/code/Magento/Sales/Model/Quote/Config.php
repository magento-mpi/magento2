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

    /**
     * @return array
     */
    public function getProductAttributes()
    {
        $attributes = \Mage::getConfig()->getNode(self::XML_PATH_QUOTE_PRODUCT_ATTRIBUTES)->asArray();
        return array_keys($attributes);
    }

    public function getTotalModels()
    {

    }
}
