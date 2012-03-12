<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @group module:Mage_Adminhtml
 */
class Mage_Adminhtml_Helper_RssTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Adminhtml_Helper_Rss
     */
    protected $_helper;

    protected function setUp()
    {
        $this->_helper = new Mage_Adminhtml_Helper_Rss;
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testAuthAdminLoggedIn()
    {
        $admin = new Varien_Object(array('id' => 1));
        $session = Mage::getSingleton('Mage_Rss_Model_Session');
        $session->setAdmin($admin);
        $this->assertEquals($admin, $this->_helper->authAdmin(''));
    }

    public function testAuthAdminNotLogged()
    {
        $this->markTestIncomplete('Incomplete until helper stops exiting script for non-logged user');
        $this->assertFalse($this->_helper->authAdmin(''));
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture adminUserFixture
     */
    public function testAuthAdminLoginWithRedirect()
    {
        $_SERVER['PHP_AUTH_USER'] = 'user';
        $_SERVER['PHP_AUTH_PW'] = 'password';
        $this->assertTrue($this->_helper->authAdmin(''));

        $response = Mage::app()->getResponse();
        $code = $response->getHttpResponseCode();
        $this->assertTrue(($code >= 300) && ($code < 400));
    }

    public static function adminUserFixture()
    {
        Mage_Admin_Utility_User::getInstance()
            ->createAdmin();
    }

    public static function adminUserFixtureRollback()
    {
        Mage_Admin_Utility_User::getInstance()
            ->destroyAdmin();
    }
}
