<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_DesignEditor
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_DesignEditor_Model_Url_NavigationModeTest extends PHPUnit_Framework_TestCase
{
    /**#@+
     * Test route params
     */
    const FRONT_NAME = 'vde';
    const ROUTE_PATH = 'design';
    const BASE_URL   = 'http://test.com';
    /**#@-*/

    /**
     * @var Mage_DesignEditor_Model_Url_NavigationMode
     */
    protected $_model;

    /**
     * @var Mage_DesignEditor_Helper_Data|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_helper;

    /**
     * @var array
     */
    protected $_testData = array(1,'test');

    public function setUp()
    {
        $this->_helper = $this->getMock('Mage_DesignEditor_Helper_Data', array('getFrontName'), array(), '', false);
        $this->_model = new Mage_DesignEditor_Model_Url_NavigationMode($this->_helper, $this->_testData);
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

        $store = $this->getMock('Mage_Core_Model_Store', array('getBaseUrl'), array(), '', false);
        $store->expects($this->any())
            ->method('getBaseUrl')
            ->will($this->returnValue(self::BASE_URL));

        $this->_model->setData('store', $store);
        $this->_model->setData('type', null);

        $sourceUrl   = self::BASE_URL . '/' . self::ROUTE_PATH;
        $expectedUrl = self::BASE_URL . '/' . self::FRONT_NAME . '/' . self::ROUTE_PATH;

        $this->assertEquals($expectedUrl, $this->_model->getRouteUrl($sourceUrl));
        $this->assertEquals($expectedUrl, $this->_model->getRouteUrl($expectedUrl));
    }
}
