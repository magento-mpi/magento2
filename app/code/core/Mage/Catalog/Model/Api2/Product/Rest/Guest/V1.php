<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * API2 for catalog_product (Guest)
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Api2_Product_Rest_Guest_V1 extends Mage_Catalog_Model_Api2_Product_Rest
{
    /**
     * Get customer group
     *
     * @return int
     */
    protected function _getCustomerGroupId()
    {
        return Mage_Customer_Model_Group::NOT_LOGGED_IN_ID;
    }

    /**
     * Define product price with or without taxes
     *
     * @param float $price
     * @param bool $withTax
     * @return float
     */
    protected function _applyTaxToPrice($price, $withTax = true)
    {
        return $this->_getPrice($price, $withTax);
    }
}
