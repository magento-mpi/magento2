<?php
/**
 * {license_notice}
 *
 * @category Magento
 * @package Magento_Catalog
 * @subpackage integration_tests
 * @copyright {copyright}
 * @license {license_link}
 */

namespace Magento\Catalog\Model\Indexer\Product\Price\Action;

/**
 * Class RowTest
 * @package Magento\Catalog\Model\Indexer\Product\Price\Action
 */
class RowTest extends \PHPUnit_Framework_TestCase
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
     * @var \Magento\Catalog\Helper\Product\Price
     */
    protected $_helper;

    /**
     * @var \Magento\Catalog\Model\Indexer\Product\Price\Processor
     */
    protected $_processor;

    protected function setUp()
    {
        $this->_product = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Model\Product');
        $this->_category = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Model\Category');
        $this->_helper = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Helper\Product\Price');
        $this->_processor = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Model\Indexer\Product\Price\Processor');
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Catalog/_files/price_row_fixture.php
     */
    public function testProductUpdate()
    {
        $categoryFactory = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('\Magento\Catalog\Model\CategoryFactory');
        $listProduct = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('\Magento\Catalog\Block\Product\ListProduct');

        $this->_processor->getIndexer()->setScheduled(false);
        $this->assertFalse($this->_processor->getIndexer()->isScheduled());

        $this->_product->load(1);
        $this->_product->setPrice(1);
        $this->_product->save();

        $category = $categoryFactory->create()->load(9);
        $layer = $listProduct->getLayer();
        $layer->setCurrentCategory($category);
        $productCollection = $layer->getProductCollection();

        $this->assertEquals(1, $productCollection->count());
        /** @var $product \Magento\Catalog\Model\Product */
        foreach ($productCollection as $product) {
            $this->assertEquals($this->_product->getId(), $product->getId());
            $this->assertEquals($this->_product->getPrice(), $product->getPrice());
        }
    }
}
