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

class Magento_Catalog_Model_Product_Type_VirtualTest extends PHPUnit_Framework_TestCase
{
    public function testIsVirtual()
    {
        /** @var $model Magento_Catalog_Model_Product_Type_Virtual */
        $model = Mage::getModel('Magento_Catalog_Model_Product_Type_Virtual');
        $product = Mage::getModel('Magento_Catalog_Model_Product');
        $this->assertTrue($model->isVirtual($product));
    }
}
