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
 * Products creation tests with ability to change attribute set during creation and editing products
 */
class Saas_Mage_Product_Create_ChangeAttributeSetTest extends Core_Mage_Product_Create_ChangeAttributeSetTest
{
    public function productTypeDataProvider()
    {
        return array(
            array('simple'),
            array('virtual'),
            array('grouped'),
            array('bundle')
        );
    }
}
