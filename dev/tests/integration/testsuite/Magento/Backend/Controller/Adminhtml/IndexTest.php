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


namespace Magento\Backend\Controller\Adminhtml;

/**
 * @magentoAppArea adminhtml
 */
class IndexTest extends \Magento\TestFramework\TestCase\AbstractController
{
    /**
     * @var \Magento\Backend\Model\Auth
     */
    protected $_auth;

    /**
     * Performs user login
     */
    protected  function _login()
    {
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Backend\Model\Url')
            ->turnOffSecretKey();
        $this->_auth = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Backend\Model\Auth');
        $this->_auth->login(
            \Magento\TestFramework\Bootstrap::ADMIN_NAME, \Magento\TestFramework\Bootstrap::ADMIN_PASSWORD);
    }

    /**
     * Performs user logout
     */
    protected function _logout()
    {
        $this->_auth->logout();
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\Backend\Model\Url')->turnOnSecretKey();
    }

    /**
     * Check not logged state
     * @covers \Magento\Backend\Controller\Adminhtml\Index::indexAction
     */
    public function testNotLoggedIndexAction()
    {
        $this->dispatch('backend/admin/index/index');
        $this->assertFalse($this->getResponse()->isRedirect());

        $body = $this->getResponse()->getBody();
        $this->assertSelectCount('form#login-form input#username[type=text]', true, $body);
        $this->assertSelectCount('form#login-form input#login[type=password]', true, $body);
    }

    /**
     * Check logged state
     * @covers \Magento\Backend\Controller\Adminhtml\Index::indexAction
     * @magentoDbIsolation enabled
     */
    public function testLoggedIndexAction()
    {
        $this->_login();
        $this->dispatch('backend/admin/index/index');
        $this->assertRedirect();
        $this->_logout();
    }


    /**
     * @covers \Magento\Backend\Controller\Index::globalSearchAction
     */
    public function testGlobalSearchAction()
    {
        $this->_login();
        $this->getRequest()->setParam('isAjax', 'true');
        $this->getRequest()->setPost('query', 'dummy');
        $this->dispatch('backend/admin/index/globalSearch');

        $actual = $this->getResponse()->getBody();
        $this->assertEquals(array(), json_decode($actual));
        $this->_logout();
    }
}
