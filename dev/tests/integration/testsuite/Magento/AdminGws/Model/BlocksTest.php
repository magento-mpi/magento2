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

namespace Magento\AdminGws\Model;
include(__DIR__ . '/../../Customer/_files/session.php');

/**
 * @magentoAppArea adminhtml
 */
class BlocksTest extends \Magento\TestFramework\TestCase\AbstractController
{
    protected function setUp()
    {
        parent::setUp();
        /** @var $auth \Magento\Backend\Model\Auth */
        $this->_objectManager->get('Magento\Backend\Model\Url')->turnOffSecretKey();
        $auth = $this->_objectManager->get('Magento\Backend\Model\Auth');
        $auth->login('admingws_user', 'admingws_password1');
    }

    protected function tearDown()
    {
        /** @var $checkoutSession \Magento\Checkout\Model\Session */
        $checkoutSession = $this->_objectManager->get('Magento\Checkout\Model\Session');
        $checkoutSession->clearStorage();
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
        if (isset($_COOKIE[$checkoutSession->getName()])) {
            unset($_COOKIE[$checkoutSession->getName()]);
        }

        $this->_objectManager->get('Magento\Backend\Model\Url')->turnOnSecretKey();
        parent::tearDown();
    }

    /**
     * @magentoConfigFixture default/catalog/magento_catalogpermissions/enabled 1
     * @magentoDataFixture Magento/Catalog/_files/categories.php
     * @magentoDataFixture Magento/AdminGws/_files/role_websites_login.php
     */
    public function testValidateCatalogPermissionsWebsites()
    {
        $this->dispatch('backend/catalog/category/edit/id/3');
        $result = $this->getResponse()->getBody();
        $this->assertContains('category_permissions_3', $result);
        $this->assertContains('limited_website_ids', $result);
    }

    /**
     * @magentoConfigFixture default/catalog/magento_catalogpermissions/enabled 1
     * @magentoDataFixture Magento/Catalog/_files/categories.php
     * @magentoDataFixture Magento/AdminGws/_files/role_stores_login.php
     */
    public function testValidateCatalogPermissionsStoreGroups()
    {
        $this->dispatch('backend/catalog/category/edit/id/3');
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
            'Magento\AdminGws\Block\Adminhtml\Permissions\Tab\Rolesedit\Gws',
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\View\LayoutInterface')
                ->getBlock('adminhtml.user.role.edit.gws'),
            'Magento\AdminGws\Block\Adminhtml\Permissions\Tab\Rolesedit\Gws block is not loaded'
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
            'Magento\AdminGws\Block\Adminhtml\Permissions\Grid\Role',
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\View\LayoutInterface')
                ->getBlock('adminhtml.user.role.grid'),
            'Magento\AdminGws\Block\Adminhtml\Permissions\Grid\Role block is not loaded'
        );
    }
}
