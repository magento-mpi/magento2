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
 * Test class for Mage_Backend_Adminhtml_IndexController.
 *
 */
class Mage_Backend_Adminhtml_IndexControllerTest extends Magento_Test_TestCase_ControllerAbstract
{
    /**
     * @var Mage_Backend_Model_Auth
     */
    protected $_auth;

    /**
     * Performs user login
     */
    protected  function _login()
    {
        Mage::getSingleton('Mage_Backend_Model_Url')->turnOffSecretKey();
        $this->_auth = Mage::getSingleton('Mage_Backend_Model_Auth');
        $this->_auth->login(Magento_Test_Bootstrap::ADMIN_NAME, Magento_Test_Bootstrap::ADMIN_PASSWORD);
    }

    /**
     * Performs user logout
     */
    protected function _logout()
    {
        $this->_auth->logout();
        Mage::getSingleton('Mage_Backend_Model_Url')->turnOnSecretKey();
    }

    /**
     * Check not logged state
     * @covers Mage_Backend_Adminhtml_IndexController::indexAction
     */
    public function testNotLoggedIndexAction()
    {
        $this->dispatch('admin/index/index');
        $this->assertFalse($this->getResponse()->isRedirect());
        $expected = 'Log in to Admin Panel';
        $this->assertContains($expected, $this->getResponse()->getBody(), 'There is no login form');
    }

    /**
     * Check logged state
     * @covers Mage_Backend_Adminhtml_IndexController::indexAction
     */
    public function testLoggedIndexAction()
    {
        $this->_login();
        $this->dispatch('admin/index/index');
        $this->assertRedirect();
        $this->_logout();
    }
}
