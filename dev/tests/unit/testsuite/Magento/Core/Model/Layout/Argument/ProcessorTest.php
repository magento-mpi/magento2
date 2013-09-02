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
 * Test class for Magento_Core_Model_Layout_Argument_Processor
 */
class Magento_Core_Model_Layout_Argument_ProcessorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_Layout_Argument_Processor
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_argumentUpdaterMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_handlerFactory;

    protected function setUp()
    {
        $this->_argumentUpdaterMock = $this->getMock(
            'Magento_Core_Model_Layout_Argument_Updater',
            array(),
            array(),
            '',
            false
        );
        $this->_handlerFactory = $this->getMock(
            'Magento_Core_Model_Layout_Argument_HandlerFactory',
            array(),
            array(),
            '',
            false
        );

        $this->_model = new Magento_Core_Model_Layout_Argument_Processor($this->_argumentUpdaterMock,
            $this->_handlerFactory
        );
    }

    /**
     * @param array $arguments
     * @param boolean $isUpdater
     * @param mixed $result
     * @dataProvider processArgumentsDataProvider
     */
    public function testProcess(array $argument, $isUpdater, $result)
    {
        $argumentHandlerMock = $this->getMock(
            'Magento_Core_Model_Layout_Argument_HandlerInterface', array(), array(), '', false
        );
        $argumentHandlerMock->expects($this->once())
            ->method('process')
            ->with($this->equalTo($argument))
            ->will($this->returnValue($argument['value']));

        $this->_handlerFactory->expects($this->once())->method('getArgumentHandlerByType')
            ->with($this->equalTo('string'))
            ->will($this->returnValue($argumentHandlerMock));

        if ($isUpdater) {
            $this->_argumentUpdaterMock->expects($this->once())
                ->method('applyUpdaters')
                ->with(
                    $this->equalTo($argument['value']),
                    $this->equalTo($argument['updater'])
                )
                ->will($this->returnValue($argument['value'] . '_Updated'));
        } else {
            $this->_argumentUpdaterMock->expects($this->never())->method('applyUpdaters');
        }

        $processed = $this->_model->process($argument);
        $this->assertEquals($processed, $result);
    }

    public function processArgumentsDataProvider()
    {
        return array(
            array(
                array(
                    'type' => 'string',
                    'value' => 'Test Value'
                ),
                false,
                'Test Value'
            ),
            array(
                array(
                    'type' => 'string',
                    'updater' => array('Dummy_Updater_Class'),
                    'value' => 'Dummy_Argument_Value_Class_Name'
                ),
                true,
                'Dummy_Argument_Value_Class_Name_Updated'
            )
        );
    }

    public function testParse()
    {
        // Because descendants of SimpleXMLElement couldn't be mocked
        $argument = new Magento_Core_Model_Layout_Element('<argument xsi:type="string" name="argumentName" '
            . 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">Value</argument>'
        );

        $argumentHandlerMock = $this->getMock(
            'Magento_Core_Model_Layout_Argument_HandlerInterface', array(), array(), '', false
        );
        $argumentHandlerMock->expects($this->once())
            ->method('parse')
            ->with($this->equalTo($argument))
            ->will($this->returnValue(true));

        $this->_handlerFactory->expects($this->once())->method('getArgumentHandlerByType')
            ->with($this->equalTo('string'))
            ->will($this->returnValue($argumentHandlerMock));

        $this->_model->parse($argument);
    }
}