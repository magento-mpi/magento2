<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_DesignEditor_Model_Url_HandleTest extends PHPUnit_Framework_TestCase
{
    /**#@+
     * Test route params
     */
    const FRONT_NAME = 'vde';
    const ROUTE_PATH = 'design';
    /**#@-*/

    /**
     * @var Mage_DesignEditor_Model_Url_Handle
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
        $this->_model = new Mage_DesignEditor_Model_Url_Handle($this->_helper, $this->_testData);
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
}
