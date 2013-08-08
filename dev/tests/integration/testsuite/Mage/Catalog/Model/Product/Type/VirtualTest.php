<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Catalog
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Catalog_Model_Product_Type_VirtualTest extends PHPUnit_Framework_TestCase
{
    public function testIsVirtual()
    {
        /** @var $model Mage_Catalog_Model_Product_Type_Virtual */
        $model = Mage::getModel('Mage_Catalog_Model_Product_Type_Virtual');
        $product = Mage::getModel('Mage_Catalog_Model_Product');
        $this->assertTrue($model->isVirtual($product));
    }
}
