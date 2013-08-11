<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Limitation_Model_Magento_Catalog_Model_ProductTest extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedException Magento_Core_Exception
     * @expectedExceptionMessage Sorry, you are using all the products and variations your account allows.
     * @magentoConfigFixture limitations/catalog_product 1
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     */
    public function testSaveRestricted()
    {
        $product = Mage::getModel('Magento_Catalog_Model_Product');
        $product->setName('test')->save();
    }

    /**
     * @expectedException Magento_Core_Exception
     * @expectedExceptionMessage Sorry, you are using all the products and variations your account allows.
     * @magentoConfigFixture limitations/catalog_product 1
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     */
    public function testValidateRestricted()
    {
        /** @var $product Magento_Catalog_Model_Product */
        $product = Mage::getModel('Magento_Catalog_Model_Product');
        $product->validate();
    }
}
