<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Limitation_Model_Mage_Catalog_Model_ProductTest extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedException Mage_Core_Exception
     * @expectedExceptionMessage Sorry, you are using all the products and variations your account allows.
     * @magentoConfigFixture limitations/catalog_product 1
     * @magentoDataFixture Mage/Catalog/_files/product_simple.php
     */
    public function testSaveRestricted()
    {
        $product = Mage::getModel('Mage_Catalog_Model_Product');
        $product->setName('test')->save();
    }

    /**
     * @expectedException Mage_Core_Exception
     * @expectedExceptionMessage Sorry, you are using all the products and variations your account allows.
     * @magentoConfigFixture limitations/catalog_product 1
     * @magentoDataFixture Mage/Catalog/_files/product_simple.php
     */
    public function testValidateRestricted()
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('Mage_Catalog_Model_Product');
        $product->validate();
    }
}
