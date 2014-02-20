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

namespace Magento\Catalog\Model\Indexer\Product\Flat;

/**
 * Class FullTest
 * @package Magento\Catalog\Model\Product\Flat\Action
 */
class ProcessorTest extends \Magento\TestFramework\Indexer\TestCase
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
     * @magentoAppArea adminhtml
     * @magentoConfigFixture current_store catalog/frontend/flat_catalog_product 1
     */
    public function testEnableProductFlat()
    {
        $this->assertTrue($this->_helper->isEnabled());
        $this->assertTrue($this->_processor->getIndexer()->isInvalid());
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoAppArea adminhtml
     * @magentoDataFixture Magento/Catalog/_files/multiple_products.php
     * @magentoConfigFixture current_store catalog/frontend/flat_catalog_product 1
     */
    public function testSaveAttribute()
    {
        /** @var $product \Magento\Catalog\Model\Product */
        $product = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Model\Product');

        /** @var \Magento\Catalog\Model\Resource\Product $productResource */
        $productResource = $product->getResource();
        $productResource->getAttribute('sku')->setData('used_for_sort_by', 1)->save();

        $this->assertTrue($this->_processor->getIndexer()->isInvalid());
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoAppArea adminhtml
     * @magentoDataFixture Magento/Catalog/_files/multiple_products.php
     * @magentoConfigFixture current_store catalog/frontend/flat_catalog_product 1
     */
    public function testDeleteAttribute()
    {
        /** @var $product \Magento\Catalog\Model\Product */
        $product = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Model\Product');

        /** @var \Magento\Catalog\Model\Resource\Product $productResource */
        $productResource = $product->getResource();
        $productResource->getAttribute('media_gallery')->delete();

        $this->assertTrue($this->_processor->getIndexer()->isInvalid());
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoAppArea adminhtml
     * @magentoDataFixture Magento/Core/_files/store.php
     * @magentoConfigFixture current_store catalog/frontend/flat_catalog_product 1
     */
    public function testAddNewStore()
    {
        $this->assertTrue($this->_processor->getIndexer()->isInvalid());
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoAppArea adminhtml
     * @magentoConfigFixture current_store catalog/frontend/flat_catalog_product 1
     */
    public function testAddNewStoreGroup()
    {
        /** @var \Magento\Core\Model\Store\Group $storeGroup */
        $storeGroup = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Core\Model\Store\Group');
        $storeGroup->setData(array(
            'website_id' => 1,
            'name' => 'New Store Group',
            'root_category_id' => 2,
            'group_id' => null,
        ));
        $storeGroup->save();
        $this->assertTrue($this->_processor->getIndexer()->isInvalid());
    }
}
