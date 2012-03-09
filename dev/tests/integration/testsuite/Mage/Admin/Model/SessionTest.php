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
        Mage_Admin_Utility_User::getInstance()
            ->createAdmin();
    }

    public function tearDown()
    {
        Mage_Admin_Utility_User::getInstance()
            ->destroyAdmin();
    }

    public function testLoginFailed()
    {
        $result = $this->_model->login('not_exists', 'not_exists');
        $this->assertFalse($result);
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testLoginSuccessfulWithRedirect()
    {
        $result = $this->_model->login('user', 'password');
        $this->assertTrue($result);

        $response = Mage::app()->getResponse();
        $code = $response->getHttpResponseCode();
        $this->assertTrue(($code >= 300) && ($code < 400));

        $headers = $response->getHeaders();
        $isRedirectFound = false;
        foreach ($headers as $header) {
            if ($header['name'] == 'Location') {
                $isRedirectFound = true;
                break;
            }
        }
        $this->assertTrue($isRedirectFound);
    }

    /**
     * @magentoConfigFixture current_store admin/security/use_form_key 0
     */
    public function testLoginSuccessfulWithoutRedirect()
    {
        $result = $this->_model->login('user', 'password');
        $this->assertInstanceOf('Mage_Admin_Model_User', $result);
        $this->assertGreaterThan(time() - 10, $this->_model->getUpdatedAt());

        $response = Mage::app()->getResponse();
        $code = $response->getHttpResponseCode();
        $this->assertFalse(($code >= 300) && ($code < 400));
    }

    /**
     * Disabled form security in order to prevent exit from the app
     * @magentoConfigFixture current_store admin/security/session_lifetime 100
     */
    public function testIsLoggedIn()
    {
        $this->_model->login('user', 'password');
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
        $this->_model->login('user', 'password');
        $this->assertTrue($this->_model->isLoggedIn());

        $this->_model->setUpdatedAt(time() - 101);
        $this->assertTrue($this->_model->isLoggedIn());
    }
}
