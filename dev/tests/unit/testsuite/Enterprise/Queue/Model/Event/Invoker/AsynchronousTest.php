<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Enterprise_Queue_Model_Event_Invoker_AsynchronousTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_queueHandlerMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_invokerDefaultMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_eventMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_eventObserverMock;

    /**
     * @var Enterprise_Queue_Model_Event_Invoker_Asynchronous
     */
    protected $_invokerAsynchronous;

    protected function setUp()
    {
        $this->_queueHandlerMock = $this->getMock('Enterprise_Queue_Model_Event_HandlerInterface');
        $this->_invokerDefaultMock = $this->getMock(
            'Mage_Core_Model_Event_Invoker_InvokerDefault',
            array(),
            array(),
            '',
            false
        );
        $this->_eventMock = $this->getMock('Magento_Event', array(), array(), '', false);
        $this->_eventObserverMock = $this->getMock('Magento_Event_Observer', array(), array(), '', false);

        $this->_invokerAsynchronous = new Enterprise_Queue_Model_Event_Invoker_Asynchronous(
            $this->_queueHandlerMock, $this->_invokerDefaultMock
        );
    }

    public function testDispatchWithAsynchronousMode()
    {
        $configuration = array(
            'instance' => 'some_model',
            'method' => 'some_method',
            'name' => 'observer',
            Enterprise_Queue_Model_Event_Invoker_Asynchronous::CONFIG_PARAMETER_ASYNCHRONOUS => 1,
            Enterprise_Queue_Model_Event_Invoker_Asynchronous::CONFIG_PARAMETER_PRIORITY => 'low',
        );

        $this->_eventMock->expects($this->once())->method('getName')->will($this->returnValue('some_event'));
        $this->_eventObserverMock->expects($this->once())->method('toArray')->will($this->returnValue(array('123')));
        $this->_eventObserverMock->expects($this->once())->method('getEvent')
            ->will($this->returnValue($this->_eventMock));

        $this->_queueHandlerMock->expects($this->once())->method('addTask')
            ->with('some_event', array('observer' => array('123'), 'configuration' => $configuration), 'low');
        $this->_invokerDefaultMock->expects($this->never())->method('dispatch');

        $this->_invokerAsynchronous->dispatch($configuration, $this->_eventObserverMock);
    }

    /**
     * @param array $configuration
     * @dataProvider dataProviderForDispatchWithNonAsynchronousMode
     */
    public function testDispatchWithNonAsynchronousMode($configuration)
    {
        $this->_queueHandlerMock->expects($this->never())->method('addTask');
        $this->_invokerDefaultMock->expects($this->once())->method('dispatch')
            ->with($configuration, $this->_eventObserverMock);

        $this->_invokerAsynchronous->dispatch($configuration, $this->_eventObserverMock);
    }

    /**
     * @return array
     */
    public function dataProviderForDispatchWithNonAsynchronousMode()
    {
        return array(
            array(
                array(
                    'instance' => 'some_model',
                    'method' => 'some_method',
                    Enterprise_Queue_Model_Event_Invoker_Asynchronous::CONFIG_PARAMETER_ASYNCHRONOUS => false,
                    'name' => 'observer',
                ),
            ),
            array(
                array(
                    'instance' => 'some_model',
                    'method' => 'some_method',
                    'name' => 'observer',
                ),
            ),
        );
    }
}
