<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Rss
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Rss_Helper_DataTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Rss_Helper_Data
     */
    protected $_helper;

    protected function setUp()
    {
        $this->_helper = new Mage_Rss_Helper_Data;
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
     */
    public function testAuthAdminLogin()
    {
        $_SERVER['PHP_AUTH_USER'] = Magento_Test_Bootstrap::ADMIN_NAME;
        $_SERVER['PHP_AUTH_PW'] = Magento_Test_Bootstrap::ADMIN_PASSWORD;
        $this->assertInstanceOf('Mage_Admin_Model_User', $this->_helper->authAdmin(''));

        $response = Mage::app()->getResponse();
        $code = $response->getHttpResponseCode();
        $this->assertFalse(($code >= 300) && ($code < 400));
    }
}
