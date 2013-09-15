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
     * @var \Magento\Backend\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Backend\Model\Auth
     */
    protected $_auth;

    protected function setUp()
    {
        Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento\Core\Model\Config\Scope')
            ->setCurrentScope(\Magento\Core\Model\App\Area::AREA_ADMINHTML);
        $this->_helper = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento\Backend\Helper\Data');
    }

    protected function tearDown()
    {
        $this->_helper = null;
        $this->_auth = null;
        Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->get('Magento\Core\Model\Config\Scope')
            ->setCurrentScope(null);
    }

    /**
     * Performs user login
     */
    protected  function _login()
    {
        Mage::getSingleton('Magento\Backend\Model\Url')->turnOffSecretKey();
        $this->_auth = Mage::getSingleton('Magento\Backend\Model\Auth');
        $this->_auth->login(
            Magento_TestFramework_Bootstrap::ADMIN_NAME, Magento_TestFramework_Bootstrap::ADMIN_PASSWORD);
    }

    /**
     * Performs user logout
     */
    protected function _logout()
    {
        $this->_auth->logout();
        Mage::getSingleton('Magento\Backend\Model\Url')->turnOnSecretKey();
    }

    /**
     * @covers \Magento\Backend\Helper\Data::getPageHelpUrl
     * @covers \Magento\Backend\Helper\Data::setPageHelpUrl
     * @covers \Magento\Backend\Helper\Data::addPageHelpUrl
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
     * @covers \Magento\Backend\Helper\Data::getCurrentUserId
     */
    public function testGetCurrentUserId()
    {
        $this->assertFalse($this->_helper->getCurrentUserId());

        /**
         * perform login
         */
        Mage::getSingleton('Magento\Backend\Model\Url')->turnOffSecretKey();

        $auth = Mage::getModel('Magento\Backend\Model\Auth');
        $auth->login(Magento_TestFramework_Bootstrap::ADMIN_NAME, Magento_TestFramework_Bootstrap::ADMIN_PASSWORD);
        $this->assertEquals(1, $this->_helper->getCurrentUserId());

        /**
         * perform logout
         */
        $auth->logout();
        Mage::getSingleton('Magento\Backend\Model\Url')->turnOnSecretKey();

        $this->assertFalse($this->_helper->getCurrentUserId());
    }

    /**
     * @covers \Magento\Backend\Helper\Data::prepareFilterString
     * @covers \Magento\Backend\Helper\Data::decodeFilter
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
