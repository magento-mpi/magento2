<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Admin
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Admin_Model_SessionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Admin_Model_Session
     */
    protected $_model;

    public function setUp()
    {
        $this->_model = new Mage_Admin_Model_Session();
    }

    public function testLoginFailed()
    {
        $result = $this->_model->login('not_exists', 'not_exists');
        $this->assertFalse($result);
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testLoginSuccessful()
    {
        $result = $this->_model->login(Magento_Test_Bootstrap::ADMIN_NAME, Magento_Test_Bootstrap::ADMIN_PASSWORD);
        $this->assertInstanceOf('Mage_Admin_Model_User', $result);
        $this->assertGreaterThan(time() - 10, $this->_model->getUpdatedAt());
    }

    public function testLogout()
    {
        $this->_model->login(Magento_Test_Bootstrap::ADMIN_NAME, Magento_Test_Bootstrap::ADMIN_PASSWORD);
        $this->assertNotEmpty($this->_model->getData());
        $this->_model->getCookie()->set($this->_model->getSessionName(), 'session_id');
        $this->_model->logout();
        $this->assertEmpty($this->_model->getData());
        $this->assertEmpty($this->_model->getCookie()->get($this->_model->getSessionName()));
    }

    /**
     * Disabled form security in order to prevent exit from the app
     * @magentoConfigFixture current_store admin/security/session_lifetime 100
     */
    public function testIsLoggedIn()
    {
        $this->_model->login(Magento_Test_Bootstrap::ADMIN_NAME, Magento_Test_Bootstrap::ADMIN_PASSWORD);
        $this->assertTrue($this->_model->isLoggedIn());

        $this->_model->setUpdatedAt(time() - 101);
        $this->assertFalse($this->_model->isLoggedIn());
    }

    /**
     * Disabled form security in order to prevent exit from the app
     * @magentoConfigFixture current_store admin/security/session_lifetime 59
     */
    public function testIsLoggedInWithIgnoredLifetime()
    {
        $this->_model->login(Magento_Test_Bootstrap::ADMIN_NAME, Magento_Test_Bootstrap::ADMIN_PASSWORD);
        $this->assertTrue($this->_model->isLoggedIn());

        $this->_model->setUpdatedAt(time() - 101);
        $this->assertTrue($this->_model->isLoggedIn());
    }
}
