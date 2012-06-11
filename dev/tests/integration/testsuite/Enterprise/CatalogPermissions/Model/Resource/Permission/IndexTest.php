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
     * @magentoDataFixture Mage/Catalog/_files/categories.php
     * @magentoDataFixture Enterprise/CatalogPermissions/_files/permission.php
     */
    public function testReindex()
    {
        $fixturePermission = Mage::getModel('Enterprise_CatalogPermissions_Model_Permission')->load(1);
        unset($fixturePermission['permission_id']);

        $permissions = $this->_indexModel->getIndexForCategory(6, 1, 1);
        $this->assertEquals(array(), $permissions);

        $this->_indexModel->reindex('1/2/6');
        $permissions = $this->_indexModel->getIndexForCategory(6, 1, 1);

        $this->assertArrayHasKey(6, $permissions);
        $this->assertEquals(1, count($permissions));
        $this->assertEquals($fixturePermission->getData(), reset($permissions));
    }
}
