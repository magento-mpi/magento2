<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Product collection resource
 *
 * @category   Enterprise
 * @package    Enterprise_Checkout
 */
class Enterprise_Checkout_Model_Resource_Product_Collection extends Magento_Catalog_Model_Resource_Product_Collection
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
