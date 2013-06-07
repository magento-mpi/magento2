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
     * Return the expected message, used by product limitation
     *
     * @return string
     */
    protected function _getCreateRestrictedMessage()
    {
        /** @var Saas_Limitation_Model_Catalog_Product_Limitation $limitation */
        $limitation = Mage::getModel('Saas_Limitation_Model_Catalog_Product_Limitation');
        return $limitation->getCreateRestrictedMessage();
    }

    /**
     * @magentoConfigFixture limitations/catalog_product 1
     * @magentoDataFixture Mage/Catalog/_files/product_simple.php
     */
    public function testSaveRestricted()
    {
        $this->setExpectedException('Mage_Core_Exception', $this->_getCreateRestrictedMessage());
        $product = Mage::getModel('Mage_Catalog_Model_Product');
        $product->setName('test')->save();
    }

    /**
     * @magentoConfigFixture limitations/catalog_product 1
     * @magentoDataFixture Mage/Catalog/_files/product_simple.php
     */
    public function testValidateRestricted()
    {
        $this->setExpectedException('Mage_Core_Exception', $this->_getCreateRestrictedMessage());
        /** @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('Mage_Catalog_Model_Product');
        $product->validate();
    }
}
