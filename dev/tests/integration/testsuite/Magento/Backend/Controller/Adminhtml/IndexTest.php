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

/**
 * Test class for Magento_Backend_Controller_Adminhtml_Index.
 *
 * @magentoAppArea adminhtml
 */
class Magento_Backend_Controller_Adminhtml_IndexTest extends Magento_TestFramework_TestCase_ControllerAbstract
{
    /**
     * @var Magento_Backend_Model_Auth
     */
    protected $_auth;

    /**
     * Performs user login
     */
    protected  function _login()
    {
        Mage::getSingleton('Magento_Backend_Model_Url')->turnOffSecretKey();
        $this->_auth = Mage::getSingleton('Magento_Backend_Model_Auth');
        $this->_auth->login(
            Magento_TestFramework_Bootstrap::ADMIN_NAME, Magento_TestFramework_Bootstrap::ADMIN_PASSWORD);
    }

    /**
     * Performs user logout
     */
    protected function _logout()
    {
        $this->_auth->logout();
        Mage::getSingleton('Magento_Backend_Model_Url')->turnOnSecretKey();
    }

    /**
     * Check not logged state
     * @covers Magento_Backend_Controller_Adminhtml_Index::indexAction
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
     * @covers Magento_Backend_Controller_Adminhtml_Index::indexAction
     * @magentoDbIsolation enabled
     */
    public function testLoggedIndexAction()
    {
        $this->_login();
        $this->dispatch('backend/admin/index/index');
        $this->assertRedirect();
        $this->_logout();
    }
}
