<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Controller;

/**
 * Test class for \Magento\Backend\Controller\AbstractAction.
 * @magentoAppArea adminhtml
 */
class AbstractActionTest extends \Magento\Backend\Utility\Controller
{
    /**
     * Check redirection to startup page for logged user
     * @magentoConfigFixture global/areas/adminhtml/frontName backend
     * @magentoConfigFixture current_store admin/security/use_form_key 1
     */
    public function testPreDispatchWithEmptyUrlRedirectsToStartupPage()
    {
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Config\ScopeInterface')
            ->setCurrentScope(\Magento\Core\Model\App\Area::AREA_ADMINHTML);
        $this->dispatch('backend');
        /** @var $backendUrlModel \Magento\Backend\Model\Url */
        $backendUrlModel =
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Backend\Model\Url');
        $url = $backendUrlModel->getStartupPageUrl();
        $expected = $backendUrlModel->getUrl($url);
        $this->assertRedirect($this->stringStartsWith($expected));
    }

    /**
     * Check login redirection
     *
     * @covers \Magento\Backend\Controller\AbstractAction::_initAuthentication
     * @magentoDbIsolation enabled
     */
    public function testInitAuthentication()
    {
        /**
         * Logout current session
         */
        $this->_auth->logout();

        $postLogin = array('login' => array(
            'username' => \Magento\TestFramework\Bootstrap::ADMIN_NAME,
            'password' => \Magento\TestFramework\Bootstrap::ADMIN_PASSWORD
        ));

        $this->getRequest()->setPost($postLogin);
        $this->dispatch('backend/admin/system_account/index');

        $expected = 'backend/admin/system_account/index';
        $this->assertRedirect($this->stringContains($expected));
    }

    /**
     * Check layout attribute "acl" for check access to
     *
     * @param string $blockName
     * @param string $resource
     * @param bool $isLimitedAccess
     * @dataProvider nodesWithAcl
     */
    public function testAclInNodes($blockName, $resource, $isLimitedAccess)
    {
        /** @var $noticeInbox \Magento\AdminNotification\Model\Inbox */
        $noticeInbox = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\AdminNotification\Model\Inbox');
        if (!$noticeInbox->loadLatestNotice()->getId()) {
            $noticeInbox->addCritical('Test notice', 'Test description');
        }

        $this->_auth->login(
            \Magento\TestFramework\Bootstrap::ADMIN_NAME, \Magento\TestFramework\Bootstrap::ADMIN_PASSWORD);

        /** @var $acl \Magento\Acl */
        $acl = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Acl\Builder')->getAcl();
        if ($isLimitedAccess) {
            $acl->deny(null, $resource);
        }

        $this->dispatch('backend/admin/dashboard');

        $layout = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\View\LayoutInterface');
        $actualBlocks = $layout->getAllBlocks();

        $this->assertNotEmpty($actualBlocks);
        if ($isLimitedAccess) {
            $this->assertNotContains($blockName, array_keys($actualBlocks));
        } else {
            $this->assertContains($blockName, array_keys($actualBlocks));
        }
    }

    /**
     * Data provider with expected blocks with acl properties
     *
     * @return array
     */
    public function nodesWithAcl()
    {
        return array(
            array('notification_window', 'Magento_AdminNotification::show_toolbar', true),
            array('notification_window', 'Magento_AdminNotification::show_toolbar', false),
        );
    }
}
