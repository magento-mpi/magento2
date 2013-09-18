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

class Magento_Catalog_Model_Resource_Product_Collection_AssociatedProductTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Catalog/_files/product_associated.php
     */
    public function testPrepareSelect()
    {
        $product = Mage::getModel('Magento\Catalog\Model\Product');
        $product->load(1); // fixture
        $product->setId(10);
        /** @var $objectManager Magento_TestFramework_ObjectManager */
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $objectManager->get('Magento\Core\Model\Registry')->register('current_product', $product);
        $collection = Mage::getResourceModel('Magento\Catalog\Model\Resource\Product\Collection\AssociatedProduct');
        $collectionProduct = $collection->getFirstItem();
        $this->assertEquals($product->getName(), $collectionProduct->getName());
        $this->assertEquals($product->getSku(), $collectionProduct->getSku());
        $this->assertEquals($product->getPrice(), $collectionProduct->getPrice());
        $this->assertEquals($product->getWeight(), $collectionProduct->getWeight());
        $this->assertEquals($product->getTypeId(), $collectionProduct->getTypeId());
        $this->assertEquals($product->getAttributeSetId(), $collectionProduct->getAttributeSetId());
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Catalog/_files/product_associated.php
     */
    public function testPrepareSelectForSameProduct()
    {
        $product = Mage::getModel('Magento\Catalog\Model\Product');
        $product->load(1); // fixture
        /** @var $objectManager Magento_TestFramework_ObjectManager */
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $objectManager->get('Magento\Core\Model\Registry')->register('current_product', $product);
        $collection = Mage::getResourceModel('Magento\Catalog\Model\Resource\Product\Collection\AssociatedProduct');
        $this->assertEmpty($collection->count());
    }
}
