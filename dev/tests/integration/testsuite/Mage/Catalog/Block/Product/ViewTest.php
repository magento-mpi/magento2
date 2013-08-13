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

/**
 * Test class for Mage_Catalog_Block_Product_View.
 *
 * @magentoDataFixture Mage/Catalog/_files/product_simple.php
 */
class Mage_Catalog_Block_Product_ViewTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Catalog_Block_Product_View
     */
    protected $_block;

    /**
     * @var Mage_Catalog_Model_Product
     */
    protected $_product;

    protected function setUp()
    {
        $this->_block = Magento_Test_Helper_Bootstrap::getObjectManager()->create('Mage_Catalog_Block_Product_View');
        $this->_product = Mage::getModel('Mage_Catalog_Model_Product');
        $this->_product->load(1);
        Mage::unregister('product');
        Mage::register('product', $this->_product);
    }

    public function testSetLayout()
    {
        /** @var $layout Mage_Core_Model_Layout */
        $layout = Mage::getModel('Mage_Core_Model_Layout');
        $headBlock = $layout->createBlock('Mage_Core_Block_Template', 'head');
        $layout->addBlock($this->_block);

        $this->assertNotEmpty($headBlock->getTitle());
        $this->assertEquals($this->_product->getMetaTitle(), $headBlock->getTitle());
        $this->assertEquals($this->_product->getMetaKeyword(), $headBlock->getKeywords());
        $this->assertEquals($this->_product->getMetaDescription(), $headBlock->getDescription());
    }

    public function testGetProduct()
    {
        $this->assertNotEmpty($this->_block->getProduct()->getId());
        $this->assertEquals($this->_product->getId(), $this->_block->getProduct()->getId());

        Mage::unregister('product');
        $this->_block->setProductId(1);
        $this->assertEquals($this->_product->getId(), $this->_block->getProduct()->getId());
    }

    public function testCanEmailToFriend()
    {
        $this->assertFalse($this->_block->canEmailToFriend());
    }

    public function testGetAddToCartUrl()
    {
        $url = $this->_block->getAddToCartUrl($this->_product);
        $this->assertStringMatchesFormat('%scheckout/cart/add/%sproduct/1/', $url);
    }

    public function testGetJsonConfig()
    {
        $config = (array) json_decode($this->_block->getJsonConfig());
        $this->assertNotEmpty($config);
        $this->assertArrayHasKey('productId', $config);
        $this->assertEquals(1, $config['productId']);
    }

    public function testHasOptions()
    {
        $this->assertTrue($this->_block->hasOptions());
    }

    public function testHasRequiredOptions()
    {
        $this->assertTrue($this->_block->hasRequiredOptions());
    }

    public function testStartBundleCustomization()
    {
        $this->markTestSkipped("Functionality not implemented in Magento 1.x. Implemented in Magento 2");
        $this->assertFalse($this->_block->startBundleCustomization());
    }
}
