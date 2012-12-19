<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_UnitPrice
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_UnitPrice_Helper_FakeData extends Saas_UnitPrice_Helper_Data
{
    /**
     * Delegate method to protected parent method to make unit testing possible
     *
     * @param  Mage_Catalog_Model_Product $product
     * @return UnitPrice_Helper_Data
     */
    public function _loadDefaultUnitPriceValues($product)
    {
        return parent::_loadDefaultUnitPriceValues($product);
    }
}
