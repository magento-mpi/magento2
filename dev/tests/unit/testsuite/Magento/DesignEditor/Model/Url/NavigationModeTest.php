<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_DesignEditor
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\DesignEditor\Model\Url;

class NavigationModeTest extends \PHPUnit_Framework_TestCase
{
    /**#@+
     * Test route params
     */
    const FRONT_NAME = 'vde';
    const ROUTE_PATH = 'some-rout-url/page.html';
    const BASE_URL   = 'http://test.com';
    /**#@-*/

    /**
     * @var \Magento\DesignEditor\Model\Url\NavigationMode
     */
    protected $_model;

    /**
     * @var \Magento\DesignEditor\Helper\Data|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_helper;

    /**
     * @var \Magento\Core\Helper\Data|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_coreData;

    /**
     * @var array
     */
    protected $_testData = array('themeId' => 1, 'mode' => 'test');

    protected function setUp()
    {
        $this->_helper = $this->getMock('Magento\DesignEditor\Helper\Data', array('getFrontName'),
            array(), '', false);
        $this->_coreData = $this->getMock('Magento\Core\Helper\Data', array(), array(), '', false);
        $requestMock = $this->getMock('Magento\Core\Controller\Request\Http', array('getAlias'), array(), '', false);
        $requestMock->expects($this->any())->method('getAlias')->will($this->returnValueMap(array(
             array('editorMode', 'navigation'),
             array('themeId', 1)
        )));
        $coreStoreConfig = $this->getMock('Magento\Core\Model\Store\Config', array(), array(), '', false);
        $app = $this->getMock('Magento\Core\Model\App', array(), array(), '', false);
        $storeManager = $this->getMock('Magento\Core\Model\StoreManager', array(), array(), '', false);
        $session = $this->getMock('Magento\Core\Model\Session', array(), array(), '', false);

        $this->_model = new \Magento\DesignEditor\Model\Url\NavigationMode(
            $this->getMock('Magento\Core\Model\Url\SecurityInfoInterface'),
            $this->_helper,
            $coreStoreConfig,
            $this->_coreData,
            $app,
            $storeManager,
            $session,
            $this->_testData
        );
        $this->_model->setRequest($requestMock);
    }

    public function testConstruct()
    {
        $this->assertAttributeEquals($this->_helper, '_helper', $this->_model);
        $this->assertAttributeEquals($this->_testData, '_data', $this->_model);
    }

    public function testGetRouteUrl()
    {
        $this->_helper->expects($this->any())
            ->method('getFrontName')
            ->will($this->returnValue(self::FRONT_NAME));

        $store = $this->getMock('Magento\Core\Model\Store',
            array('getBaseUrl', 'isAdmin', 'isAdminUrlSecure', 'isFrontUrlSecure', '__sleep', '__wakeup'),
            array(), '', false
        );
        $store->expects($this->any())
            ->method('getBaseUrl')
            ->will($this->returnValue(self::BASE_URL));

        $store->expects($this->any())
            ->method('isAdmin')
            ->will($this->returnValue(false));

        $store->expects($this->any())
            ->method('isAdminUrlSecure')
            ->will($this->returnValue(false));

        $store->expects($this->any())
            ->method('isFrontUrlSecure')
            ->will($this->returnValue(false));

        $this->_model->setData('store', $store);
        $this->_model->setData('type', null);
        $this->_model->setData('route_front_name', self::FRONT_NAME);

        $sourceUrl   = self::BASE_URL . '/' . self::ROUTE_PATH;
        $expectedUrl = self::BASE_URL . '/' . self::FRONT_NAME . '/' . $this->_testData['mode'] . '/'
            . $this->_testData['themeId'] . '/' . self::ROUTE_PATH;

        $this->assertEquals($expectedUrl, $this->_model->getRouteUrl($sourceUrl));
        $this->assertEquals($this->_model, $this->_model->setType(null));
        $this->assertEquals($expectedUrl, $this->_model->getRouteUrl($expectedUrl));
    }
}
