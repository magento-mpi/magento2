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
 * Test class for \Magento\Core\Model\Layout\Argument\Processor
 */
namespace Magento\Core\Model\Layout\Argument;

class ProcessorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Layout\Argument\Processor
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_argumentUpdaterMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_handlerFactory;

    protected function setUp()
    {
        $this->_argumentUpdaterMock = $this->getMock(
            'Magento\Core\Model\Layout\Argument\Updater',
            array(),
            array(),
            '',
            false
        );
        $this->_handlerFactory = $this->getMock(
            'Magento\View\Layout\Argument\HandlerFactory',
            array(),
            array(),
            '',
            false
        );

        $this->_model = new \Magento\Core\Model\Layout\Argument\Processor($this->_argumentUpdaterMock,
            $this->_handlerFactory
        );
    }

    /**
     * @param array $argument
     * @param boolean $isUpdater
     * @param mixed $result
     * @dataProvider processArgumentsDataProvider
     */
    public function testProcess(array $argument, $isUpdater, $result)
    {
        $argumentHandlerMock = $this->getMock(
            'Magento\View\Layout\Argument\HandlerInterface', array(), array(), '', false
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
                    $this->equalTo($argument['updaters'])
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
                    'updaters' => array('Dummy_Updater_Class'),
                    'value' => 'Dummy_Argument_Value_Class_Name'
                ),
                true,
                'Dummy_Argument_Value_Class_Name_Updated'
            )
        );
    }

    public function testParse()
    {
        // Because descendants of \SimpleXMLElement couldn't be mocked
        $argument = new \Magento\View\Layout\Element('<argument xsi:type="string" name="argumentName" '
            . 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">Value</argument>'
        );

        $argumentHandlerMock = $this->getMock(
            'Magento\View\Layout\Argument\HandlerInterface', array(), array(), '', false
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
