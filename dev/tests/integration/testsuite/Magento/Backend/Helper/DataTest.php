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
 * @magentoAppArea adminhtml
 */
class Magento_Backend_Helper_DataTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Backend_Helper_Data
     */
    protected $_helper;

    /**
     * @var Magento_Backend_Model_Auth
     */
    protected $_auth;

    protected function setUp()
    {
        Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_Config_Scope')
            ->setCurrentScope(Magento_Core_Model_App_Area::AREA_ADMINHTML);
        $this->_helper = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Backend_Helper_Data');
    }

    protected function tearDown()
    {
        $this->_helper = null;
        $this->_auth = null;
        Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->get('Magento_Core_Model_Config_Scope')
            ->setCurrentScope(null);
    }

    /**
     * Performs user login
     */
    protected  function _login()
    {
        Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Backend_Model_Url')
            ->turnOffSecretKey();
        $this->_auth = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Backend_Model_Auth');
        $this->_auth->login(
            Magento_TestFramework_Bootstrap::ADMIN_NAME, Magento_TestFramework_Bootstrap::ADMIN_PASSWORD);
    }

    /**
     * Performs user logout
     */
    protected function _logout()
    {
        $this->_auth->logout();
        Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Backend_Model_Url')->turnOnSecretKey();
    }

    /**
     * @covers Magento_Backend_Helper_Data::getPageHelpUrl
     * @covers Magento_Backend_Helper_Data::setPageHelpUrl
     * @covers Magento_Backend_Helper_Data::addPageHelpUrl
     */
    public function testPageHelpUrl()
    {
        Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Controller_Request_Http')
            ->setControllerModule('dummy')
            ->setControllerName('index')
            ->setActionName('test');


        $expected = 'http://www.magentocommerce.com/gethelp/en_US/dummy/index/test/';
        $this->assertEquals($expected, $this->_helper->getPageHelpUrl(), 'Incorrect help Url');

        $this->_helper->addPageHelpUrl('dummy');
        $expected .= 'dummy';
        $this->assertEquals($expected, $this->_helper->getPageHelpUrl(), 'Incorrect help Url suffix');
    }

    /**
     * @covers Magento_Backend_Helper_Data::getCurrentUserId
     */
    public function testGetCurrentUserId()
    {
        $this->assertFalse($this->_helper->getCurrentUserId());

        /**
         * perform login
         */
        Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Backend_Model_Url')
            ->turnOffSecretKey();

        $auth = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Backend_Model_Auth');
        $auth->login(Magento_TestFramework_Bootstrap::ADMIN_NAME, Magento_TestFramework_Bootstrap::ADMIN_PASSWORD);
        $this->assertEquals(1, $this->_helper->getCurrentUserId());

        /**
         * perform logout
         */
        $auth->logout();
        Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Backend_Model_Url')->turnOnSecretKey();

        $this->assertFalse($this->_helper->getCurrentUserId());
    }

    /**
     * @covers Magento_Backend_Helper_Data::prepareFilterString
     * @covers Magento_Backend_Helper_Data::decodeFilter
     */
    public function testPrepareFilterString()
    {
        $expected = array(
            'key1' => 'val1',
            'key2' => 'val2',
            'key3' => 'val3',
        );

        $filterString = base64_encode('key1='.rawurlencode('val1').'&key2=' . rawurlencode('val2') . '&key3=val3');
        $actual = $this->_helper->prepareFilterString($filterString);
        $this->assertEquals($expected, $actual);
    }

    public function testGetHomePageUrl()
    {
        $this->assertStringEndsWith(
            'index.php/backend/admin/', $this->_helper->getHomePageUrl(), 'Incorrect home page URL'
        );
    }
}
