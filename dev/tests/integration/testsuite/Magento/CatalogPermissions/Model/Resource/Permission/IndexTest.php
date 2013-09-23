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

class Magento_CatalogPermissions_Model_Resource_Permission_IndexTest extends PHPUnit_Framework_TestCase
{
    /**
     * CatalogPermissions Index model
     *
     * @var Magento_CatalogPermissions_Model_Permission_Index
     */
    protected $_indexModel;

    protected function setUp()
    {
        $this->_indexModel = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_CatalogPermissions_Model_Permission_Index');
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/categories.php
     * @magentoDataFixture Magento/CatalogPermissions/_files/permission.php
     */
    public function testReindex()
    {
        $fixturePermission = array(
            'category_id'                 => 6,
            'website_id'                  => Mage::app()->getWebsite()->getId(),
            'customer_group_id'           => 1,
            'grant_catalog_category_view' => Magento_CatalogPermissions_Model_Permission::PERMISSION_DENY,
            'grant_catalog_product_price' => Magento_CatalogPermissions_Model_Permission::PERMISSION_DENY,
            'grant_checkout_items'        => Magento_CatalogPermissions_Model_Permission::PERMISSION_DENY,
        );

        $permissions = $this->_indexModel->getIndexForCategory(6, 1, 1);
        $this->assertEquals(array(), $permissions);

        $this->_indexModel->reindex('1/2/6');
        $permissions = $this->_indexModel->getIndexForCategory(6, 1, 1);

        $this->assertArrayHasKey(6, $permissions);
        $this->assertEquals(1, count($permissions));
        $this->assertEquals($fixturePermission, reset($permissions));
    }
}
