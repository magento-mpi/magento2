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
    const VALID_URL = 'http://test.com';
    /**#@-*/

    /**
     * @var Mage_DesignEditor_Model_Url_NavigationMode
     */
    protected $_model;

    /**
     * @var Mage_DesignEditor_Helper_Data
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

    public function testGetRoutePath()
    {
        $this->_helper->expects($this->once())
            ->method('getFrontName')
            ->will($this->returnValue(self::FRONT_NAME));

        $this->_model->setData('route_path', self::ROUTE_PATH);
        $this->assertEquals(self::FRONT_NAME . '/' . self::ROUTE_PATH, $this->_model->getRoutePath());
    }

    public function testGetRouteUrl()
    {
        $this->assertEquals(self::VALID_URL, $this->_model->getRouteUrl(self::VALID_URL));
    }
}
