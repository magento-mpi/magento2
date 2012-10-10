<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Catalog_Model_Product_Type_VirtualTest extends PHPUnit_Framework_TestCase
{
    public function testIsVirtual()
    {
        $this->markTestIncomplete('Need to fix DI dependencies');

        $model = Mage::getModel('Mage_Catalog_Model_Product_Type_Virtual');
        $product = Mage::getModel('Mage_Catalog_Model_Product');
        $this->assertTrue($model->isVirtual($product));
    }
}
