<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_AdminGws
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
class Magento_AdminGws_Model_BlocksTest extends Magento_TestFramework_TestCase_ControllerAbstract
{
    protected function setUp()
    {
        parent::setUp();
        /** @var $auth Magento_Backend_Model_Auth */
        Mage::getSingleton('Magento_Backend_Model_Url')->turnOffSecretKey();
        $auth = Mage::getSingleton('Magento_Backend_Model_Auth');
        $auth->login('admingws_user', 'admingws_password1');
    }

    protected function tearDown()
    {
        /** @var $auth Magento_Backend_Model_Auth */
        $auth = Mage::getSingleton('Magento_Backend_Model_Auth');
        $auth->logout();
        Mage::getSingleton('Magento_Backend_Model_Url')->turnOnSecretKey();
        parent::tearDown();
    }

    /**
     * @magentoConfigFixture admin_store catalog/magento_catalogpermissions/enabled 1
     * @magentoDataFixture Magento/Catalog/_files/categories.php
     * @magentoDataFixture Magento/AdminGws/_files/role_websites_login.php
     */
    public function testValidateCatalogPermissionsWebsites()
    {
        $this->dispatch('backend/admin/catalog_category/edit/id/3');
        $result = $this->getResponse()->getBody();
        $this->assertContains('category_permissions_3', $result);
        $this->assertContains('limited_website_ids', $result);
    }

    /**
     * @magentoConfigFixture admin_store catalog/magento_catalogpermissions/enabled 1
     * @magentoDataFixture Magento/Catalog/_files/categories.php
     * @magentoDataFixture Magento/AdminGws/_files/role_stores_login.php
     */
    public function testValidateCatalogPermissionsStoreGroups()
    {
        $this->dispatch('backend/admin/catalog_category/edit/id/3');
        $this->assertRegExp('/title\="New Permission"\s+type\="button"\s+'
            . 'class="action-\w*\s+scalable\s+delete\s+disabled\s+disabled"/', $this->getResponse()->getBody());
    }

    /**
     * @magentoDataFixture Magento/AdminGws/_files/role_websites_login.php
     */
    public function testBackendUserRoleEditContainsGwsBlock()
    {
        $this->dispatch('backend/admin/user_role/editrole');

        $this->assertInstanceOf(
            'Magento_AdminGws_Block_Adminhtml_Permissions_Tab_Rolesedit_Gws',
            Mage::app()->getLayout()->getBlock('adminhtml.user.role.edit.gws'),
            'Magento_AdminGws_Block_Adminhtml_Permissions_Tab_Rolesedit_Gws block is not loaded'
        );

        $body = $this->getResponse()->getBody();
        $this->assertSelectEquals(
            'div.entry-edit.form-inline fieldset.fieldset legend.legend span',
            'Role Scopes',
            1,
            $body
        );
    }

    /**
     * @magentoDataFixture Magento/AdminGws/_files/role_websites_login.php
     */
    public function testBackendUserRoleEditRoleGridContainsGwsBlock()
    {
        $this->dispatch('backend/admin/user_role/editrolegrid');

        $this->assertInstanceOf(
            'Magento_AdminGws_Block_Adminhtml_Permissions_Grid_Role',
            Mage::app()->getLayout()->getBlock('adminhtml.user.role.grid'),
            'Magento_AdminGws_Block_Adminhtml_Permissions_Grid_Role block is not loaded'
        );
    }
}
