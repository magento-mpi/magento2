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
     * A valid ACL resource that admin will have access out of the box
     */
    const ACL_RESOURCE = 'adminhtml/dashboard';

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
     * @covers Mage_Rss_Helper_Data::isAdminAuthorized
     */
    public function testIsAuthLoggedIn()
    {
        $session = Mage::getSingleton('Mage_Backend_Model_Auth_Session');
        $auth = Mage::getModel('Mage_Backend_Model_Auth');
        $auth->setAuthStorage($session)->login(
            Magento_Test_Bootstrap::ADMIN_NAME, Magento_Test_Bootstrap::ADMIN_PASSWORD
        );
        $this->assertTrue($this->_helper->isAdminAuthorized(
            $session, 'wrong_login', 'password_doesn\'t matter as well', 'whatever/acl'
        ));
    }

    /**
     * @covers Mage_Rss_Helper_Data::isAdminAuthorized
     */
    public function testIsAuthEmptyLoginOrPass()
    {
        $session = new Mage_Backend_Model_Auth_Session;
        $this->assertFalse($this->_helper->isAdminAuthorized($session, '', '', self::ACL_RESOURCE));
        $this->assertFalse($this->_helper->isAdminAuthorized(
            $session, Magento_Test_Bootstrap::ADMIN_NAME, '', self::ACL_RESOURCE
        ));
        $this->assertFalse($this->_helper->isAdminAuthorized(
            $session, '', Magento_Test_Bootstrap::ADMIN_PASSWORD, self::ACL_RESOURCE
        ));
    }

    /**
     * @magentoAppIsolation enabled
     * @dataProvider isAuthTryLoginDataProvider
     * @covers Mage_Rss_Helper_Data::isAdminAuthorized
     */
    public function testIsAuthTryLogin($login, $password, $aclPath, $expectedResult)
    {
        $session = Mage::getSingleton('Mage_Backend_Model_Auth_Session');
        $this->assertEquals($expectedResult, $this->_helper->isAdminAuthorized($session, $login, $password, $aclPath));
    }

    /**
     * @return array
     */
    public function isAuthTryLoginDataProvider()
    {
        return array(
            'correct credentials' => array(
                Magento_Test_Bootstrap::ADMIN_NAME, Magento_Test_Bootstrap::ADMIN_PASSWORD, self::ACL_RESOURCE, true
            ),
            'incorrect password' => array(Magento_Test_Bootstrap::ADMIN_NAME, 'incorrect', self::ACL_RESOURCE, false),
            'unauthorized' => array(
                Magento_Test_Bootstrap::ADMIN_NAME, Magento_Test_Bootstrap::ADMIN_PASSWORD, 'incorrect/resource', true
            ),
        );
    }
}
