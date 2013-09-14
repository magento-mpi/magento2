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
 * Test class for \Magento\Backend\Model\Auth.
 *
 * @magentoAppArea adminhtml
 */
class Magento_Backend_Model_AuthTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Backend\Model\Auth
     */
    protected $_model;

    public function setUp()
    {
        parent::setUp();

        Mage::app()->loadArea(\Magento\Core\Model\App\Area::AREA_ADMINHTML);
        $this->_model = Mage::getModel('Magento\Backend\Model\Auth');
    }

    /**
     * @expectedException \Magento\Backend\Model\Auth\Exception
     */
    public function testLoginFailed()
    {
        $this->_model->login('not_exists', 'not_exists');
    }

    public function testSetGetAuthStorage()
    {
        // by default \Magento\Backend\Model\Auth\Session class will instantiate as a Authentication Storage
        $this->assertInstanceOf('Magento\Backend\Model\Auth\Session', $this->_model->getAuthStorage());

        $mockStorage = $this->getMock('Magento\Backend\Model\Auth\StorageInterface');
        $this->_model->setAuthStorage($mockStorage);
        $this->assertInstanceOf('Magento\Backend\Model\Auth\StorageInterface', $this->_model->getAuthStorage());

        $incorrectStorage = new StdClass();
        try {
            $this->_model->setAuthStorage($incorrectStorage);
            $this->fail('Incorrect authentication storage setted.');
        } catch (\Magento\Backend\Model\Auth\Exception $e) {
            // in case of exception - Auth works correct
            $this->assertNotEmpty($e->getMessage());
        }
    }

    public function testGetCredentialStorageList()
    {
        $storage = $this->_model->getCredentialStorage();
        $this->assertInstanceOf('Magento\Backend\Model\Auth\Credential\StorageInterface', $storage);
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testLoginSuccessful()
    {
        $this->_model->login(
            Magento_TestFramework_Bootstrap::ADMIN_NAME, Magento_TestFramework_Bootstrap::ADMIN_PASSWORD);
        $this->assertInstanceOf('Magento\Backend\Model\Auth\Credential\StorageInterface', $this->_model->getUser());
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
        $this->assertInstanceOf('Magento\Backend\Model\Auth\Credential\StorageInterface', $this->_model->getUser());
    }
}
