<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\CatalogPermissions\Model\Indexer;

/**
 * @magentoDbIsolation enabled
 */
class CategoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\CatalogPermissions\Model\Resource\Permission\Index
     */
    protected $indexTable;

    /**
     * @var \Magento\Catalog\Model\Category
     */
    protected $category;

    public function setUp()
    {
        $this->indexTable = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\CatalogPermissions\Model\Resource\Permission\Index'
        );
        $this->category = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Catalog\Model\Category'
        );
    }

    /**
     * @test
     *
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/enabled 1
     * @magentoDataFixture Magento/Catalog/_files/categories.php
     * @magentoDataFixture Magento/CatalogPermissions/_files/permission.php
     */
    public function testReindexAll()
    {
        /** @var  $indexer \Magento\Indexer\Model\IndexerInterface */
        $indexer = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Indexer\Model\Indexer'
        );
        $indexer->load(\Magento\CatalogPermissions\Model\Indexer\Category::INDEXER_ID);
        $indexer->reindexAll();

        $this->assertEmpty($this->indexTable->getIndexForCategory(10));
        $this->assertContains($this->getCategoryDataById(6), $this->indexTable->getIndexForCategory(6));
    }

    /**
     * @test
     *
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/enabled 1
     * @magentoDataFixture Magento/Catalog/_files/categories.php
     * @magentoDataFixture Magento/CatalogPermissions/_files/permission.php
     */
    public function testCategoryMove()
    {
        $this->category->load(7);
        $this->category->move(6, null);

        $this->assertContains($this->getCategoryDataById(7), $this->indexTable->getIndexForCategory(7));
    }

    /**
     * @test
     *
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/enabled 1
     * @magentoDataFixture Magento/Catalog/_files/categories.php
     * @magentoDataFixture Magento/CatalogPermissions/_files/permission.php
     */
    public function testCategoryCreate()
    {
        $this->category->isObjectNew(true);
        $this->category->setId(
            13
        )->setName(
            'New'
        )->setParentId(
            6
        )->setPath(
            '1/2/6/13'
        )->setLevel(
            3
        )->setAvailableSortBy(
            'name'
        )->setDefaultSortBy(
            'name'
        )->setIsActive(
            true
        )->setPosition(
            3
        )->save();

        $this->assertContains($this->getCategoryDataById(13), $this->indexTable->getIndexForCategory(13));
    }

    /**
     * Return default row from permission by category id
     *
     * @param int $id
     * @return array
     */
    protected function getCategoryDataById($id)
    {
        return [
            'category_id' => $id,
            'website_id' => '1',
            'customer_group_id' => '1',
            'grant_catalog_category_view' => '-2',
            'grant_catalog_product_price' => '-2',
            'grant_checkout_items' => '-2'
        ];
    }
}
