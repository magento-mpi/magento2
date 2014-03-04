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

namespace Magento\CatalogPermissions\Model\Indexer\Category\Action;

class RowsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\CatalogPermissions\Model\Permission\Index
     */
    protected $index;

    /**
     * @var \Magento\CatalogPermissions\Model\Indexer\Category\Action\Rows
     */
    protected $action;

    protected function setUp()
    {
        $this->index = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\CatalogPermissions\Model\Permission\Index');
        $this->action = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\CatalogPermissions\Model\Indexer\Category\Action\Rows');
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/categories.php
     * @magentoDataFixture Magento/CatalogPermissions/_files/permission.php
     */
    public function testReindex()
    {
        $fixturePermission = array(
            'category_id'                 => 6,
            'website_id'                  => \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->
                get('Magento\Core\Model\StoreManagerInterface')->getWebsite()->getId(),
            'customer_group_id'           => 1,
            'grant_catalog_category_view' => \Magento\CatalogPermissions\Model\Permission::PERMISSION_DENY,
            'grant_catalog_product_price' => \Magento\CatalogPermissions\Model\Permission::PERMISSION_DENY,
            'grant_checkout_items'        => \Magento\CatalogPermissions\Model\Permission::PERMISSION_DENY,
        );

        $permissions = $this->index->getIndexForCategory(6, 1, 1);
        $this->assertEquals(array(), $permissions);

        $this->action->execute([1, 2, 6]);
        $permissions = $this->index->getIndexForCategory(6, 1, 1);

        $this->assertArrayHasKey(6, $permissions);
        $this->assertEquals(1, count($permissions));
        $this->assertEquals($fixturePermission, reset($permissions));
    }
}
