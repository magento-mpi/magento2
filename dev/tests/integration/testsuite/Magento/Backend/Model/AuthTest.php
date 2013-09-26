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
 * Test class for Magento_Backend_Model_Auth.
 *
 * @magentoAppArea adminhtml
 */
class Magento_Backend_Model_AuthTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Backend_Model_Auth
     */
    protected $_model;

    protected function setUp()
    {
        parent::setUp();

        Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_App')
            ->loadArea(Magento_Core_Model_App_Area::AREA_ADMINHTML);
        $this->_model = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Backend_Model_Auth');
    }

    /**
     * @expectedException Magento_Backend_Model_Auth_Exception
     */
    public function testLoginFailed()
    {
        $this->_model->login('not_exists', 'not_exists');
    }

    public function testSetGetAuthStorage()
    {
        // by default Magento_Backend_Model_Auth_Session class will instantiate as a Authentication Storage
        $this->assertInstanceOf('Magento_Backend_Model_Auth_Session', $this->_model->getAuthStorage());

        $mockStorage = $this->getMock('Magento_Backend_Model_Auth_StorageInterface');
        $this->_model->setAuthStorage($mockStorage);
        $this->assertInstanceOf('Magento_Backend_Model_Auth_StorageInterface', $this->_model->getAuthStorage());

        $incorrectStorage = new StdClass();
        try {
            $this->_model->setAuthStorage($incorrectStorage);
            $this->fail('Incorrect authentication storage setted.');
        } catch (Magento_Backend_Model_Auth_Exception $e) {
            // in case of exception - Auth works correct
            $this->assertNotEmpty($e->getMessage());
        }
    }

    public function testGetCredentialStorageList()
    {
        $storage = $this->_model->getCredentialStorage();
        $this->assertInstanceOf('Magento_Backend_Model_Auth_Credential_StorageInterface', $storage);
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testLoginSuccessful()
    {
        $this->_model->login(
            Magento_TestFramework_Bootstrap::ADMIN_NAME, Magento_TestFramework_Bootstrap::ADMIN_PASSWORD);
        $this->assertInstanceOf('Magento_Backend_Model_Auth_Credential_StorageInterface', $this->_model->getUser());
        $this->assertGreaterThan(time() - 10, $this->_model->getAuthStorage()->getUpdatedAt());
    }

    public function testLogout()
    {
        $this->_model->login(
            Magento_TestFramework_Bootstrap::ADMIN_NAME, Magento_TestFramework_Bootstrap::ADMIN_PASSWORD);
        $this->assertNotEmpty($this->_model->getAuthStorage()->getData());
        $this->_model->getAuthStorage()
            ->getCookie()
            ->set($this->_model->getAuthStorage()->getSessionName(), 'session_id');
        $this->_model->logout();
        $this->assertEmpty($this->_model->getAuthStorage()->getData());
        $this->assertEmpty($this->_model->getAuthStorage()
            ->getCookie()
            ->get($this->_model->getAuthStorage()->getSessionName())
        );
    }

    /**
     * Disabled form security in order to prevent exit from the app
     * @magentoConfigFixture current_store admin/security/session_lifetime 100
     */
    public function testIsLoggedIn()
    {
        $this->_model->login(
            Magento_TestFramework_Bootstrap::ADMIN_NAME, Magento_TestFramework_Bootstrap::ADMIN_PASSWORD);
        $this->assertTrue($this->_model->isLoggedIn());

        $this->_model->getAuthStorage()->setUpdatedAt(time() - 101);
        $this->assertFalse($this->_model->isLoggedIn());
    }

    /**
     * Disabled form security in order to prevent exit from the app
     * @magentoConfigFixture current_store admin/security/session_lifetime 59
     */
    public function testIsLoggedInWithIgnoredLifetime()
    {
        $this->_model->login(
            Magento_TestFramework_Bootstrap::ADMIN_NAME, Magento_TestFramework_Bootstrap::ADMIN_PASSWORD);
        $this->assertTrue($this->_model->isLoggedIn());

        $this->_model->getAuthStorage()->setUpdatedAt(time() - 101);
        $this->assertTrue($this->_model->isLoggedIn());
    }

    public function testGetUser()
    {
        $this->_model->login(
            Magento_TestFramework_Bootstrap::ADMIN_NAME, Magento_TestFramework_Bootstrap::ADMIN_PASSWORD);

        $this->assertNotNull($this->_model->getUser());
        $this->assertGreaterThan(0, $this->_model->getUser()->getId());
        $this->assertInstanceOf('Magento_Backend_Model_Auth_Credential_StorageInterface', $this->_model->getUser());
    }
}
