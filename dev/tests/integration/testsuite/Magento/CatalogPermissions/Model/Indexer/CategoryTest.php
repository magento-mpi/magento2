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

namespace Magento\CatalogPermissions\Model\Indexer;

/**
 * @magentoDbIsolation enabled
 */
class CategoryTest extends \PHPUnit_Framework_TestCase
{
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
        $indexer = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Indexer\Model\Indexer');
        $indexer->load(\Magento\CatalogPermissions\Model\Indexer\Category::INDEXER_ID);
        $indexer->reindexAll();

        $indexTable = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\CatalogPermissions\Model\Resource\Permission\Index');

        $this->assertEmpty($indexTable->getIndexForCategory(10));
        $this->assertContains(
            [
                'category_id' => '6',
                'website_id' => '1',
                'customer_group_id' => '1',
                'grant_catalog_category_view' => '-2',
                'grant_catalog_product_price' => '-2',
                'grant_checkout_items' => '-2',
            ],
            $indexTable->getIndexForCategory(6)
        );
    }
}
