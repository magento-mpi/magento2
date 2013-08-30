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

class Magento_DesignEditor_Model_Url_NavigationModeTest extends PHPUnit_Framework_TestCase
{
    /**#@+
     * Test route params
     */
    const FRONT_NAME = 'vde';
    const ROUTE_PATH = 'some-rout-url/page.html';
    const BASE_URL   = 'http://test.com';
    /**#@-*/

    /**
     * @var Magento_DesignEditor_Model_Url_NavigationMode
     */
    protected $_model;

    /**
     * @var Magento_DesignEditor_Helper_Data|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_helper;

    /**
     * @var Magento_Core_Helper_Data|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_coreData;

    /**
     * @var array
     */
    protected $_testData = array('themeId' => 1, 'mode' => 'test');

    public function setUp()
    {
        $this->_helper = $this->getMock('Magento_DesignEditor_Helper_Data', array('getFrontName'),
            array(), '', false);
        $this->_coreData = $this->getMock('Magento_Core_Helper_Data', array(), array(), '', false);
        $requestMock = $this->getMock('Magento_Core_Controller_Request_Http', array('getAlias'), array(), '', false);
        $requestMock->expects($this->any())->method('getAlias')->will($this->returnValueMap(array(
             array('editorMode', 'navigation'),
             array('themeId', 1)
        )));
        $this->_model = new Magento_DesignEditor_Model_Url_NavigationMode($this->_helper, $this->_coreData,
            $this->_testData);
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

        $store = $this->getMock('Magento_Core_Model_Store',
            array('getBaseUrl', 'isAdmin', 'isAdminUrlSecure', 'isFrontUrlSecure'),
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
