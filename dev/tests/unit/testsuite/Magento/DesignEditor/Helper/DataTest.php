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

class Magento_DesignEditor_Helper_DataTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test front name prefix
     */
    const TEST_FRONT_NAME = 'test_front_name';

    /**
     * @var array
     */
    protected $_disabledCacheTypes = array('type1', 'type2');

    /**
     * @var Magento_DesignEditor_Helper_Data
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_translatorMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_context;

    protected function setUp()
    {
        $this->_translatorMock = $this->getMock('Magento_Core_Model_Translate', array(), array(), '', false);
        $this->_context = $this->getMock('Magento_Core_Helper_Context', array(), array(), '', false);
        $this->_context
            ->expects($this->any())->method('getTranslator')->will($this->returnValue($this->_translatorMock));
    }

    protected function tearDown()
    {
        unset($this->_model);
        unset($this->_context);
    }

    public function testGetFrontName()
    {
        $this->_model = new Magento_DesignEditor_Helper_Data($this->_context, self::TEST_FRONT_NAME);
        $this->assertEquals(self::TEST_FRONT_NAME, $this->_model->getFrontName());
    }

    /**
     * @param string $path
     * @param bool $expected
     * @dataProvider isVdeRequestDataProvider
     */
    public function testIsVdeRequest($path, $expected)
    {
        $this->_model = new Magento_DesignEditor_Helper_Data($this->_context, self::TEST_FRONT_NAME);
        $requestMock = $this->getMock('Magento_Core_Controller_Request_Http', array('getOriginalPathInfo'),
            array(), '', false);
        $requestMock->expects($this->once())
            ->method('getOriginalPathInfo')
            ->will($this->returnValue($path));
        $this->assertEquals($expected, $this->_model->isVdeRequest($requestMock));
    }

    /**
     * @return array
     */
    public function isVdeRequestDataProvider()
    {
        $vdePath = self::TEST_FRONT_NAME . '/' . Magento_DesignEditor_Model_State::MODE_NAVIGATION . '/';
        return array(
            array($vdePath . '1/category.html', true),
            array('/1/category.html', false),
            array('/1/2/3/4/5/6/7/category.html', false)
        );
    }

    public function testGetDisabledCacheTypes()
    {
        $this->_model = new Magento_DesignEditor_Helper_Data(
            $this->_context,
            self::TEST_FRONT_NAME,
            array('type1','type2')
        );
        $this->assertEquals($this->_disabledCacheTypes, $this->_model->getDisabledCacheTypes());
    }

    public function testGetAvailableModes()
    {
        $this->_model = new Magento_DesignEditor_Helper_Data($this->_context, self::TEST_FRONT_NAME);
        $this->assertEquals(array(Magento_DesignEditor_Model_State::MODE_NAVIGATION),
            $this->_model->getAvailableModes());
    }
}
