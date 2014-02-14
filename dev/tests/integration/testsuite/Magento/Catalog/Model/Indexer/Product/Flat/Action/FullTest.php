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
 * Full reindex Test
 */
class FullTest extends \Magento\TestFramework\Indexer\TestCase
{
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
        $this->_helper = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\Catalog\Helper\Product\Flat');
        $this->_processor = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\Catalog\Model\Indexer\Product\Flat\Processor');
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoConfigFixture current_store catalog/frontend/flat_catalog_product 1
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     */
    public function testReindexAll()
    {
        $this->assertTrue($this->_helper->isEnabled());
        $this->_processor->reindexAll();

        $categoryFactory = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('\Magento\Catalog\Model\CategoryFactory');
        $listProduct = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('\Magento\Catalog\Block\Product\ListProduct');

        $category = $categoryFactory->create()->load(2);
        $layer = $listProduct->getLayer();
        $layer->setCurrentCategory($category);
        $productCollection = $layer->getProductCollection();

        $this->assertCount(1, $productCollection);

        /** @var $product \Magento\Catalog\Model\Product */
        foreach ($productCollection as $product) {
            $this->assertEquals('Simple Product', $product->getName());
            $this->assertEquals('Short description', $product->getShortDescription());
        }
    }
}
