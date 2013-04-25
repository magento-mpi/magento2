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

    protected function setUp()
    {
        Mage::getConfig()->setCurrentAreaCode(Mage_Core_Model_App_Area::AREA_ADMINHTML);
        Mage::app()->loadAreaPart(Mage_Core_Model_App_Area::AREA_ADMINHTML, Mage_Core_Model_App_Area::PART_CONFIG);
        parent::setUp();
    }

    protected function tearDown()
    {
        $this->_auth = null;
        parent::tearDown();
        Mage::getConfig()->setCurrentAreaCode(null);
    }

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
        $this->dispatch('backend/admin/index/index');
        $this->assertFalse($this->getResponse()->isRedirect());
        $body = $this->getResponse()->getBody();
        $this->assertSelectCount('form#login-form input#username[type=text]', true, $body);
        $this->assertSelectCount('form#login-form input#login[type=password]', true, $body);
    }

    /**
     * Check logged state
     * @covers Mage_Backend_Adminhtml_IndexController::indexAction
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
