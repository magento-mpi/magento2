<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogPermissions
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogPermissions\Model\Permission;

class IndexTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\CatalogPermissions\Model\Permission\Index
     */
    protected $index;

    /**
     * @var \Magento\Indexer\Model\IndexerInterface
     */
    protected $indexer;

    protected function setUp()
    {
        $this->index = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\CatalogPermissions\Model\Permission\Index');
        $this->indexer = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Indexer\Model\IndexerInterface');
        $this->indexer->load(\Magento\CatalogPermissions\Model\Indexer\Category::INDEXER_ID);
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/categories.php
     * @magentoDataFixture Magento/CatalogPermissions/_files/permission.php
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/enabled true
     */
    public function testGetIndexForCategory()
    {
        $fixturePermission = array(
            'category_id'                 => 6,
            'website_id'                  => \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->
                get('Magento\Store\Model\StoreManagerInterface')->getWebsite()->getId(),
            'customer_group_id'           => 1,
            'grant_catalog_category_view' => \Magento\CatalogPermissions\Model\Permission::PERMISSION_DENY,
            'grant_catalog_product_price' => \Magento\CatalogPermissions\Model\Permission::PERMISSION_DENY,
            'grant_checkout_items'        => \Magento\CatalogPermissions\Model\Permission::PERMISSION_DENY,
        );

        $permissions = $this->index->getIndexForCategory(6, 1, 1);
        $this->assertEquals(array(), $permissions);

        $this->indexer->reindexRow(6);
        $permissions = $this->index->getIndexForCategory(6, 1, 1);

        $this->assertArrayHasKey(6, $permissions);
        $this->assertCount(1, $permissions);
        foreach ($fixturePermission as $key => $permissionData) {
            $this->assertArrayHasKey($key, $permissions[6]);
            $this->assertEquals($permissionData, $permissions[6][$key]);
        }
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/categories.php
     * @magentoDataFixture Magento/CatalogPermissions/_files/permission.php
     * @magentoAppArea frontend
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/enabled true
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/grant_catalog_category_view 1
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/grant_catalog_product_price 1
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/grant_checkout_items 1
     */
    public function testAddIndexToCategoryCollectionWithDefaultAllow()
    {
        /** @var \Magento\Customer\Model\Session $session */
        $session = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->
            get('Magento\Customer\Model\Session');

        $session->setCustomerGroupId(0);
        /** @var \Magento\Catalog\Model\Resource\Category\Collection $categoryCollection */
        $categoryCollection = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->
            create('Magento\Catalog\Model\Resource\Category\Collection');
        $categoryCollection->addIsActiveFilter();
        $categoryCollection->load();
        $this->assertCount(10, $categoryCollection->getItems());
        $this->assertInstanceOf('Magento\Catalog\Model\Category', $categoryCollection->getItemById(6));

        $this->indexer->reindexAll();

        $session->setCustomerGroupId(1);
        /** @var \Magento\Catalog\Model\Resource\Category\Collection $categoryCollection */
        $categoryCollection = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->
            create('Magento\Catalog\Model\Resource\Category\Collection');
        $categoryCollection->addIsActiveFilter();
        $categoryCollection->load();
        $this->assertCount(9, $categoryCollection->getItems());
        $this->assertEquals(null, $categoryCollection->getItemById(6));
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/categories.php
     * @magentoDataFixture Magento/CatalogPermissions/_files/permission.php
     * @magentoAppArea frontend
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/enabled true
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/grant_catalog_category_view 0
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/grant_catalog_product_price 0
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/grant_checkout_items 0
     */
    public function testAddIndexToCategoryCollectionWithDefaultDeny()
    {
        /** @var \Magento\Catalog\Model\Resource\Category\Collection $categoryCollection */
        $categoryCollection = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->
            create('Magento\Catalog\Model\Resource\Category\Collection');
        $categoryCollection->addIsActiveFilter();
        $categoryCollection->load();
        $this->assertCount(0, $categoryCollection->getItems());

        $this->indexer->reindexAll();

        /** @var \Magento\Catalog\Model\Resource\Category\Collection $categoryCollection */
        $categoryCollection = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->
            create('Magento\Catalog\Model\Resource\Category\Collection');
        $categoryCollection->addIsActiveFilter();
        $categoryCollection->load();
        $this->assertCount(0, $categoryCollection->getItems());
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/categories.php
     * @magentoDataFixture Magento/CatalogPermissions/_files/permission.php
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/enabled true
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/grant_catalog_category_view 0
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/grant_catalog_product_price 0
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/grant_checkout_items 0
     */
    public function testGetRestrictedCategoryIdsWithDefaultDeny()
    {
        $websiteId = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->
            get('Magento\Store\Model\StoreManagerInterface')->getWebsite()->getId();

        $this->assertCount(12, $this->index->getRestrictedCategoryIds(0, $websiteId));
        $this->assertCount(12, $this->index->getRestrictedCategoryIds(1, $websiteId));

        $this->indexer->reindexAll();

        $this->assertCount(12, $this->index->getRestrictedCategoryIds(0, $websiteId));
        $this->assertCount(12, $this->index->getRestrictedCategoryIds(1, $websiteId));
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/categories.php
     * @magentoDataFixture Magento/CatalogPermissions/_files/permission.php
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/enabled true
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/grant_catalog_category_view 1
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/grant_catalog_product_price 1
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/grant_checkout_items 1
     */
    public function testGetRestrictedCategoryIdsWithDefaultAllow()
    {
        $websiteId = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->
            get('Magento\Store\Model\StoreManagerInterface')->getWebsite()->getId();

        $this->assertCount(0, $this->index->getRestrictedCategoryIds(0, $websiteId));
        $this->assertCount(0, $this->index->getRestrictedCategoryIds(1, $websiteId));

        $this->indexer->reindexAll();

        $this->assertCount(1, $this->index->getRestrictedCategoryIds(0, $websiteId));
        $this->assertCount(1, $this->index->getRestrictedCategoryIds(1, $websiteId));
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/categories.php
     * @magentoDataFixture Magento/CatalogPermissions/_files/permission.php
     * @magentoAppArea frontend
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/enabled true
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/grant_catalog_category_view 1
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/grant_catalog_product_price 1
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/grant_checkout_items 1
     */
    public function testAddIndexToProductCollectionWithDefaultAllow()
    {
        /** @var \Magento\Customer\Model\Session $session */
        $session = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->
            get('Magento\Customer\Model\Session');

        /** @var \Magento\Catalog\Model\Category $category */
        $category = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->
            create('Magento\Catalog\Model\Category');
        $category->load(6);

        $session->setCustomerGroupId(0);
        /** @var \Magento\Catalog\Model\Resource\Product\Collection $categoryCollection */
        $productCollection = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->
            create('Magento\Catalog\Model\Resource\Product\Collection');
        $productCollection->addCategoryFilter($category);
        $productCollection->load();
        $this->assertCount(1, $productCollection->getItems());
        $this->assertInstanceOf('Magento\Catalog\Model\Product', $productCollection->getItemById(5));

        $this->indexer->reindexAll();

        $session->setCustomerGroupId(1);
        /** @var \Magento\Catalog\Model\Resource\Product\Collection $categoryCollection */
        $productCollection = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->
            create('Magento\Catalog\Model\Resource\Product\Collection');
        $productCollection->addCategoryFilter($category);
        $productCollection->load();
        $this->assertCount(0, $productCollection->getItems());
        $this->assertEquals(null, $productCollection->getItemById(5));
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/categories.php
     * @magentoDataFixture Magento/CatalogPermissions/_files/permission.php
     * @magentoAppArea frontend
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/enabled true
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/grant_catalog_category_view 0
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/grant_catalog_product_price 0
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/grant_checkout_items 0
     */
    public function testAddIndexToProductCollectionWithDefaultDeny()
    {
        /** @var \Magento\Customer\Model\Session $session */
        $session = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->
            get('Magento\Customer\Model\Session');

        /** @var \Magento\Catalog\Model\Category $category */
        $category = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->
            create('Magento\Catalog\Model\Category');
        $category->load(6);

        $session->setCustomerGroupId(0);
        /** @var \Magento\Catalog\Model\Resource\Product\Collection $categoryCollection */
        $productCollection = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->
            create('Magento\Catalog\Model\Resource\Product\Collection');
        $productCollection->addCategoryFilter($category);
        $productCollection->load();
        $this->assertCount(0, $productCollection->getItems());
        $this->assertEquals(null, $productCollection->getItemById(5));

        $this->indexer->reindexAll();

        $session->setCustomerGroupId(1);
        /** @var \Magento\Catalog\Model\Resource\Product\Collection $categoryCollection */
        $productCollection = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->
            create('Magento\Catalog\Model\Resource\Product\Collection');
        $productCollection->addCategoryFilter($category);
        $productCollection->load();
        $this->assertCount(0, $productCollection->getItems());
        $this->assertEquals(null, $productCollection->getItemById(5));
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/categories.php
     * @magentoDataFixture Magento/CatalogPermissions/_files/permission.php
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/enabled true
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/grant_catalog_category_view 1
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/grant_catalog_product_price 1
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/grant_checkout_items 1
     */
    public function testAddIndexToProductWithCategoryAndDefaultAllow()
    {
        /** @var \Magento\Catalog\Model\Category $category */
        $category = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->
            create('Magento\Catalog\Model\Category');
        $category->load(6);

        /** @var \Magento\Registry $registry */
        $registry = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->
            get('Magento\Registry');
        $registry->register('current_category', $category);

        /** @var \Magento\Catalog\Model\Product $product */
        $product = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->
            create('Magento\Catalog\Model\Product');
        $product->load(5);

        $this->index->addIndexToProduct($product, 0);
        $this->assertArrayNotHasKey('grant_catalog_category_view', $product->getData());
        $this->assertArrayNotHasKey('grant_catalog_product_price', $product->getData());
        $this->assertArrayNotHasKey('grant_checkout_items', $product->getData());

        $this->index->addIndexToProduct($product, 1);
        $this->assertArrayNotHasKey('grant_catalog_category_view', $product->getData());
        $this->assertArrayNotHasKey('grant_catalog_product_price', $product->getData());
        $this->assertArrayNotHasKey('grant_checkout_items', $product->getData());

        $this->indexer->reindexAll();

        $this->index->addIndexToProduct($product, 0);
        $this->assertArrayNotHasKey('grant_catalog_category_view', $product->getData());
        $this->assertArrayNotHasKey('grant_catalog_product_price', $product->getData());
        $this->assertArrayNotHasKey('grant_checkout_items', $product->getData());

        $this->index->addIndexToProduct($product, 1);
        $this->assertArrayHasKey('grant_catalog_category_view', $product->getData());
        $this->assertEquals(
            \Magento\CatalogPermissions\Model\Permission::PERMISSION_DENY,
            $product->getData('grant_catalog_category_view')
        );
        $this->assertArrayHasKey('grant_catalog_product_price', $product->getData());
        $this->assertEquals(
            \Magento\CatalogPermissions\Model\Permission::PERMISSION_DENY,
            $product->getData('grant_catalog_product_price')
        );
        $this->assertArrayHasKey('grant_checkout_items', $product->getData());
        $this->assertEquals(
            \Magento\CatalogPermissions\Model\Permission::PERMISSION_DENY,
            $product->getData('grant_checkout_items')
        );
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/categories.php
     * @magentoDataFixture Magento/CatalogPermissions/_files/permission.php
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/enabled true
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/grant_catalog_category_view 1
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/grant_catalog_product_price 1
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/grant_checkout_items 1
     */
    public function testAddIndexToProductStandaloneWithDefaultAllow()
    {
        /** @var \Magento\Catalog\Model\Product $product */
        $product = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->
            create('Magento\Catalog\Model\Product');
        $product->load(5);

        $this->index->addIndexToProduct($product, 0);
        $this->assertArrayNotHasKey('grant_catalog_category_view', $product->getData());
        $this->assertArrayNotHasKey('grant_catalog_product_price', $product->getData());
        $this->assertArrayNotHasKey('grant_checkout_items', $product->getData());

        $this->index->addIndexToProduct($product, 1);
        $this->assertArrayNotHasKey('grant_catalog_category_view', $product->getData());
        $this->assertArrayNotHasKey('grant_catalog_product_price', $product->getData());
        $this->assertArrayNotHasKey('grant_checkout_items', $product->getData());

        $this->indexer->reindexAll();

        $this->index->addIndexToProduct($product, 0);
        $this->assertArrayNotHasKey('grant_catalog_category_view', $product->getData());
        $this->assertArrayNotHasKey('grant_catalog_product_price', $product->getData());
        $this->assertArrayNotHasKey('grant_checkout_items', $product->getData());

        $this->index->addIndexToProduct($product, 1);
        $this->assertArrayHasKey('grant_catalog_category_view', $product->getData());
        $this->assertEquals(
            \Magento\CatalogPermissions\Model\Permission::PERMISSION_DENY,
            $product->getData('grant_catalog_category_view')
        );
        $this->assertArrayHasKey('grant_catalog_product_price', $product->getData());
        $this->assertEquals(
            \Magento\CatalogPermissions\Model\Permission::PERMISSION_DENY,
            $product->getData('grant_catalog_product_price')
        );
        $this->assertArrayHasKey('grant_checkout_items', $product->getData());
        $this->assertEquals(
            \Magento\CatalogPermissions\Model\Permission::PERMISSION_DENY,
            $product->getData('grant_checkout_items')
        );
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/categories.php
     * @magentoDataFixture Magento/CatalogPermissions/_files/permission.php
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/enabled true
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/grant_catalog_category_view 0
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/grant_catalog_product_price 0
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/grant_checkout_items 0
     */
    public function testAddIndexToProductStandaloneWithDefaultDeny()
    {
        /** @var \Magento\Catalog\Model\Product $product */
        $product = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->
            create('Magento\Catalog\Model\Product');
        $product->load(5);

        $this->index->addIndexToProduct($product, 0);
        $this->assertArrayNotHasKey('grant_catalog_category_view', $product->getData());
        $this->assertArrayNotHasKey('grant_catalog_product_price', $product->getData());
        $this->assertArrayNotHasKey('grant_checkout_items', $product->getData());

        $this->index->addIndexToProduct($product, 1);
        $this->assertArrayNotHasKey('grant_catalog_category_view', $product->getData());
        $this->assertArrayNotHasKey('grant_catalog_product_price', $product->getData());
        $this->assertArrayNotHasKey('grant_checkout_items', $product->getData());

        $this->indexer->reindexAll();

        $this->index->addIndexToProduct($product, 0);
        $this->assertArrayNotHasKey('grant_catalog_category_view', $product->getData());
        $this->assertArrayNotHasKey('grant_catalog_product_price', $product->getData());
        $this->assertArrayNotHasKey('grant_checkout_items', $product->getData());

        $this->index->addIndexToProduct($product, 1);
        $this->assertArrayHasKey('grant_catalog_category_view', $product->getData());
        $this->assertEquals(
            \Magento\CatalogPermissions\Model\Permission::PERMISSION_DENY,
            $product->getData('grant_catalog_category_view')
        );
        $this->assertArrayHasKey('grant_catalog_product_price', $product->getData());
        $this->assertEquals(
            \Magento\CatalogPermissions\Model\Permission::PERMISSION_DENY,
            $product->getData('grant_catalog_product_price')
        );
        $this->assertArrayHasKey('grant_checkout_items', $product->getData());
        $this->assertEquals(
            \Magento\CatalogPermissions\Model\Permission::PERMISSION_DENY,
            $product->getData('grant_checkout_items')
        );
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/categories.php
     * @magentoDataFixture Magento/CatalogPermissions/_files/permission.php
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/enabled true
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/grant_catalog_category_view 1
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/grant_catalog_product_price 1
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/grant_checkout_items 1
     */
    public function testGetIndexForProductWithDefaultAllow()
    {
        $storeId = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->
            get('Magento\Store\Model\StoreManagerInterface')->getStore()->getId();

        $deny = \Magento\CatalogPermissions\Model\Permission::PERMISSION_DENY;

        $permissions = $this->index->getIndexForProduct(5, 0, $storeId);
        $this->assertCount(0, $permissions);
        $permissions = $this->index->getIndexForProduct(5, 1, $storeId);
        $this->assertCount(0, $permissions);

        $this->indexer->reindexAll();

        $permissions = $this->index->getIndexForProduct(5, 0, $storeId);
        $this->assertCount(0, $permissions);

        $permissions = $this->index->getIndexForProduct(5, 1, $storeId);
        $this->assertCount(1, $permissions);
        $this->assertArrayHasKey('grant_catalog_category_view', $permissions[$deny]);
        $this->assertEquals($deny, $permissions[$deny]['grant_catalog_category_view']);
        $this->assertArrayHasKey('grant_catalog_product_price', $permissions[$deny]);
        $this->assertEquals($deny, $permissions[$deny]['grant_catalog_product_price']);
        $this->assertArrayHasKey('grant_checkout_items', $permissions[$deny]);
        $this->assertEquals($deny, $permissions[$deny]['grant_checkout_items']);
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/categories.php
     * @magentoDataFixture Magento/CatalogPermissions/_files/permission.php
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/enabled true
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/grant_catalog_category_view 0
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/grant_catalog_product_price 0
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/grant_checkout_items 0
     */
    public function testGetIndexForProductWithDefaultDeny()
    {
        $storeId = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->
            get('Magento\Store\Model\StoreManagerInterface')->getStore()->getId();

        $deny = \Magento\CatalogPermissions\Model\Permission::PERMISSION_DENY;

        $permissions = $this->index->getIndexForProduct(5, 0, $storeId);
        $this->assertCount(0, $permissions);
        $permissions = $this->index->getIndexForProduct(5, 1, $storeId);
        $this->assertCount(0, $permissions);

        $this->indexer->reindexAll();

        $permissions = $this->index->getIndexForProduct(5, 0, $storeId);
        $this->assertCount(0, $permissions);

        $permissions = $this->index->getIndexForProduct(5, 1, $storeId);
        $this->assertCount(1, $permissions);
        $this->assertArrayHasKey('grant_catalog_category_view', $permissions[$deny]);
        $this->assertEquals($deny, $permissions[$deny]['grant_catalog_category_view']);
        $this->assertArrayHasKey('grant_catalog_product_price', $permissions[$deny]);
        $this->assertEquals($deny, $permissions[$deny]['grant_catalog_product_price']);
        $this->assertArrayHasKey('grant_checkout_items', $permissions[$deny]);
        $this->assertEquals($deny, $permissions[$deny]['grant_checkout_items']);
    }
}
