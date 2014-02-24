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

namespace Magento\Catalog\Model\Indexer\Product\Flat\Action;

/**
 * Class RowTest
 * @package Magento\Catalog\Model\Indexer\Product\Flat\Action
 */
class RowTest extends \Magento\TestFramework\Indexer\TestCase
{
    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $_product;

    /**
     * @var \Magento\Catalog\Model\Category
     */
    protected $_category;

    /**
     * @var \Magento\Catalog\Helper\Product\Flat
     */
    protected $_helper;

    /**
     * @var \Magento\Catalog\Model\Indexer\Product\Flat\Processor
     */
    protected $_processor;

    protected function setUp()
    {
        $this->_product = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Model\Product');
        $this->_category = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Model\Category');
        $this->_helper = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Helper\Product\Flat');
        $this->_processor = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Model\Indexer\Product\Flat\Processor');
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Catalog/_files/row_fixture.php
     * @magentoConfigFixture current_store catalog/frontend/flat_catalog_product 1
     */
    public function testProductUpdate()
    {
        $categoryFactory = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Model\CategoryFactory');
        $listProduct = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Block\Product\ListProduct');

        $this->assertTrue($this->_helper->isEnabled());

        $this->_processor->getIndexer()->setScheduled(false);
        $this->assertFalse($this->_processor->getIndexer()->isScheduled());

        $this->_product->load(1);
        $this->_product->setName('Updated Product');
        $this->_product->save();

        $category = $categoryFactory->create()->load(9);
        $layer = $listProduct->getLayer();
        $layer->setCurrentCategory($category);
        $productCollection = $layer->getProductCollection();

        $this->assertEquals(1, $productCollection->count());

        /** @var $product \Magento\Catalog\Model\Product */
        foreach ($productCollection as $product) {
            $this->assertEquals($this->_product->getId(), $product->getId());
            $this->assertEquals($this->_product->getName(), $product->getName());
            $this->assertEquals($this->_product->getPrice(), $product->getPrice());
        }
    }
}
