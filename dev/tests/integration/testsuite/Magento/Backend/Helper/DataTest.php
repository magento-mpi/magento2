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

namespace Magento\Backend\Helper;

/**
 * @magentoAppArea adminhtml
 */
class DataTest extends \PHPUnit_Framework_TestCase
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
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Config\ScopeInterface')
            ->setCurrentScope(\Magento\Backend\App\Area\FrontNameResolver::AREA_CODE);
        $this->_helper = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\Backend\Helper\Data');
    }

    protected function tearDown()
    {
        $this->_helper = null;
        $this->_auth = null;
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\Config\ScopeInterface')
            ->setCurrentScope(null);
    }

    /**
     * Performs user login
     */
    protected  function _login()
    {
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Backend\Model\Url')
            ->turnOffSecretKey();
        $this->_auth = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Backend\Model\Auth');
        $this->_auth->login(
            \Magento\TestFramework\Bootstrap::ADMIN_NAME, \Magento\TestFramework\Bootstrap::ADMIN_PASSWORD);
    }

    /**
     * Performs user logout
     */
    protected function _logout()
    {
        $this->_auth->logout();
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\Backend\Model\Url')->turnOnSecretKey();
    }

    /**
     * @covers \Magento\Backend\Helper\Data::getPageHelpUrl
     * @covers \Magento\Backend\Helper\Data::setPageHelpUrl
     * @covers \Magento\Backend\Helper\Data::addPageHelpUrl
     */
    public function testPageHelpUrl()
    {
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\App\RequestInterface')
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
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Backend\Model\Url')
            ->turnOffSecretKey();

        $auth = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Backend\Model\Auth');
        $auth->login(\Magento\TestFramework\Bootstrap::ADMIN_NAME, \Magento\TestFramework\Bootstrap::ADMIN_PASSWORD);
        $this->assertEquals(1, $this->_helper->getCurrentUserId());

        /**
         * perform logout
         */
        $auth->logout();
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\Backend\Model\Url')->turnOnSecretKey();

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
