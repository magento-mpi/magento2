<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_CatalogPermissions
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_CatalogPermissions_Model_Resource_Permission_IndexTest extends PHPUnit_Framework_TestCase
{
    /**
     * CatalogPermissions Index model
     *
     * @var Enterprise_CatalogPermissions_Model_Permission_Index
     */
    protected $_indexModel;

    protected function setUp()
    {
        $this->_indexModel = new Enterprise_CatalogPermissions_Model_Permission_Index();
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture Mage/Catalog/_files/categories.php
     * @magentoDataFixture Enterprise/CatalogPermissions/_files/permissions.php
     */
    public function testCategoryPermissionsIndexingReturnsSuccess()
    {
        /** @var $fixturePermission Enterprise_CatalogPermissions_Model_Permission */
        $fixturePermission = Mage::getModel('Enterprise_CatalogPermissions_Model_Permission')->load(1);

        $this->_indexModel->reindex('1/2/6');

        $permissions = $this->_indexModel->getIndexForCategory(
            $fixturePermission->getCategoryId(),
            $fixturePermission->getCustomerGroupId(),
            $fixturePermission->getWebsiteId()
        );

        $permission = array_shift($permissions);

        $this->assertTrue(
            $permission['category_id'] == $fixturePermission->getCategoryId()
            && $permission['website_id'] == $fixturePermission->getWebsiteId()
            && $permission['grant_catalog_category_view'] == $fixturePermission->getGrantCatalogCategoryView()
            && $permission['grant_catalog_product_price'] == $fixturePermission->getGrantCatalogProductPrice()
            && $permission['grant_checkout_items'] == $fixturePermission->getGrantCheckoutItems()
        );
    }
}
