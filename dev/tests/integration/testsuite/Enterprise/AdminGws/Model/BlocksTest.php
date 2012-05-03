<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_AdminGws
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_AdminGws_Model_BlocksTest extends Magento_Test_TestCase_ControllerAbstract
{
    protected function setUp()
    {
        parent::setUp();
        Mage::getSingleton('Mage_Adminhtml_Model_Url')->turnOffSecretKey();
        $session = new Mage_Admin_Model_Session();
        $session->login('admingws_user', 'admingws_password');
    }

    protected function tearDown()
    {
        $session = new Mage_Admin_Model_Session();
        $session->logout();
        Mage::getSingleton('Mage_Adminhtml_Model_Url')->turnOnSecretKey();
        parent::tearDown();
    }

    /**
     * @magentoConfigFixture admin_store catalog/enterprise_catalogpermissions/enabled 1
     * @magentoDataFixture Mage/Catalog/_files/categories.php
     * @magentoDataFixture Enterprise/AdminGws/_files/role_websites_login.php
     */
    public function testValidateCatalogPermissionsWebsites()
    {
        $this->dispatch('admin/catalog_category/edit/id/3');
        $result = $this->getResponse()->getBody();
        $this->assertContains('category_permissions_3', $result);
        $this->assertContains('limited_website_ids', $result);
    }

    /**
     * @magentoConfigFixture admin_store catalog/enterprise_catalogpermissions/enabled 1
     * @magentoDataFixture Mage/Catalog/_files/categories.php
     * @magentoDataFixture Enterprise/AdminGws/_files/role_stores_login.php
     */
    public function testValidateCatalogPermissionsStoreGroups()
    {
        $this->dispatch('admin/catalog_category/edit/id/3');
        $result = $this->getResponse()->getBody();
        $expected = 'title="New Permission" type="button" class="scalable delete disabled disabled" onclick="" style=""'
            . ' disabled="disabled">';
        $this->assertContains($expected, $result);
    }
}
