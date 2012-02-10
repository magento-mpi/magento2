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

/**
 * @group module:Mage_Admin
 *
 * @magentoDataFixture Mage/Admin/_files/user.php
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

    /**
     * Disabled form security in order to prevent exit from the app
     * @magentoConfigFixture current_store admin/security/use_form_key 0
     */
    public function testLogin()
    {
        $user = $this->_model->login('not_exists', 'not_exists');
        $this->assertEmpty($user->getId());
        $this->assertEmpty($this->_model->getUpdatedAt());

        $user = $this->_model->login('user', 'password');
        $this->assertGreaterThan(0, $user->getId());
        $this->assertGreaterThan(time()-10, $this->_model->getUpdatedAt());
    }

    /**
     * Disabled form security in order to prevent exit from the app
     * @magentoConfigFixture current_store admin/security/use_form_key 0
     * @magentoConfigFixture current_store admin/security/session_lifetime 100
     */
    public function testIsLoggedIn()
    {
        $this->_model->login('user', 'password');
        $this->assertTrue($this->_model->isLoggedIn());

        $this->_model->setUpdatedAt(time()-101);
        $this->assertFalse($this->_model->isLoggedIn());
    }

    /**
     * Disabled form security in order to prevent exit from the app
     * @magentoConfigFixture current_store admin/security/use_form_key 0
     * @magentoConfigFixture current_store admin/security/session_lifetime 59
     */
    public function testIsLoggedInWithIgnoredLifetime()
    {
        $this->_model->login('user', 'password');
        $this->assertTrue($this->_model->isLoggedIn());

        $this->_model->setUpdatedAt(time()-101);
        $this->assertTrue($this->_model->isLoggedIn());
    }
}
