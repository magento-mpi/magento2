<?php
/**
 * Magento
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Product
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
/**
 * Importing custom option functionality
 */
class Saas_Mage_Product_ImportCustomOptionsTest extends Core_Mage_Product_ImportCustomOptionsTest
{
    /**
     * <p>DataProvider with product type list</p>
     * <p>Exclude 'downloadable' product</p>
     *
     * @return array
     */
    public function productTypesDataProvider()
    {
        return array(
            array('simple'),
            array('configurable'),
            array('virtual'),
            array('bundle')
        );
    }
}