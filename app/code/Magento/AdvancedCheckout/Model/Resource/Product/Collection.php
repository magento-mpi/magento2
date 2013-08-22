<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_AdvancedCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Product collection resource
 *
 * @category   Magento
 * @package    Magento_AdvancedCheckout
 */
class Magento_AdvancedCheckout_Model_Resource_Product_Collection extends Magento_Catalog_Model_Resource_Product_Collection
{
    /**
     * Join Product Price Table using left-join
     *
     * @return Magento_Catalog_Model_Resource_Product_Collection
     */
    protected function _productLimitationJoinPrice()
    {
        return $this->_productLimitationPrice(true);
    }
}
