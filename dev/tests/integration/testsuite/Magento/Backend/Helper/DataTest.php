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
        parent::setUp();

        Mage::getObjectManager()->get('Magento_Core_Model_Config_Scope')
            ->setCurrentScope(Magento_Core_Model_App_Area::AREA_ADMINHTML);
        $this->_helper = Mage::helper('Magento_Backend_Helper_Data');
    }

    protected function tearDown()
    {
        $this->_helper = null;
        $this->_auth = null;
        Mage::getObjectManager()->get('Magento_Core_Model_Config_Scope')->setCurrentScope(null);
    }

    /**
     * Performs user login
     */
    protected  function _login()
    {
        Mage::getSingleton('Magento_Backend_Model_Url')->turnOffSecretKey();
        $this->_auth = Mage::getSingleton('Magento_Backend_Model_Auth');
        $this->_auth->login(Magento_Test_Bootstrap::ADMIN_NAME, Magento_Test_Bootstrap::ADMIN_PASSWORD);
    }

    /**
     * Performs user logout
     */
    protected function _logout()
    {
        $this->_auth->logout();
        Mage::getSingleton('Magento_Backend_Model_Url')->turnOnSecretKey();
    }

    /**
     * @covers Magento_Backend_Helper_Data::getPageHelpUrl
     * @covers Magento_Backend_Helper_Data::setPageHelpUrl
     * @covers Magento_Backend_Helper_Data::addPageHelpUrl
     */
    public function testPageHelpUrl()
    {
        Mage::app()->getRequest()
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
        Mage::getSingleton('Magento_Backend_Model_Url')->turnOffSecretKey();

        $auth = Mage::getModel('Magento_Backend_Model_Auth');
        $auth->login(Magento_Test_Bootstrap::ADMIN_NAME, Magento_Test_Bootstrap::ADMIN_PASSWORD);
        $this->assertEquals(1, $this->_helper->getCurrentUserId());

        /**
         * perform logout
         */
        $auth->logout();
        Mage::getSingleton('Magento_Backend_Model_Url')->turnOnSecretKey();

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

    /**
     * @magentoConfigFixture admin/routers/adminhtml/args/frontName admin
     */
    public function testGetHomePageUrl()
    {
        $this->assertStringEndsWith(
            'index.php/backend/admin/', $this->_helper->getHomePageUrl(), 'Incorrect home page URL'
        );
    }
}
