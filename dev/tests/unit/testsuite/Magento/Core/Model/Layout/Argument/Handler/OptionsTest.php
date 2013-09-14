<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for \Magento\Core\Model\Layout\Argument\Handler\Options
 */
class Magento_Core_Model_Layout_Argument_Handler_OptionsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Layout\Argument\Handler\Options
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManagerMock;

    protected function setUp()
    {
        $this->_objectManagerMock = $this->getMock('Magento\ObjectManager');
        $this->_model = new \Magento\Core\Model\Layout\Argument\Handler\Options($this->_objectManagerMock);
    }

    protected function tearDown()
    {
        unset($this->_objectManagerMock);
        unset($this->_model);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testProcessIfOptionModelIncorrect()
    {
        $this->_objectManagerMock->expects($this->once())
            ->method('create')
            ->with('StdClass')
            ->will($this->returnValue(new StdClass()));
        $this->_model->process('StdClass');
    }

    public function testProcess()
    {
        $optionArray = array('value' => 'LABEL');
        $expectedOptionArray = array(
            0 => array('value' => 'value',
                       'label' => 'LABEL',
            ));
        $optionsModel = $this->getMock(
            'Magento\Core\Model\Option\ArrayInterface',
            array(),
            array(),
            'Option_Array_Model',
            false);
        $optionsModel->expects($this->once())->method('toOptionArray')->will($this->returnValue($optionArray));
        $this->_objectManagerMock->expects($this->once())
            ->method('create')
            ->with('Option_Array_Model')
            ->will($this->returnValue($optionsModel));
        $this->assertEquals($expectedOptionArray, $this->_model->process('Option_Array_Model'));
    }
}
