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
        $this->_invokerDefaultMock = $this->getMock('Mage_Core_Model_Event_Invoker_InvokerDefault', array(), array(), '',
            false);
        $this->_eventMock = $this->getMock('Varien_Event', array(), array(), '', false);
        $this->_eventObserverMock = $this->getMock('Varien_Event_Observer', array(), array(), '', false);

        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $this->_invokerAsynchronous = $objectManagerHelper->getObject(
            'Enterprise_Queue_Model_Event_Invoker_Asynchronous', array(
            'queueHandler' => $this->_queueHandlerMock,
            'invokerDefault' => $this->_invokerDefaultMock,
        ));
    }

    public function testDispatchWithAsynchronousMode()
    {
        $configuration = array(
            'model' => 'some_model',
            'method' => 'some_method',
            'config' => array(
                Enterprise_Queue_Model_Event_Invoker_Asynchronous::CONFIG_PARAMETER_ASYNCHRONOUS => 1,
                Enterprise_Queue_Model_Event_Invoker_Asynchronous::CONFIG_PARAMETER_PRIORITY => 7,
            ),
        );

        $this->_eventMock->expects($this->once())->method('getName')->will($this->returnValue('some_event'));
        $this->_eventObserverMock->expects($this->once())->method('toArray')->will($this->returnValue(array('123')));
        $this->_eventObserverMock->expects($this->once())->method('getEvent')
            ->will($this->returnValue($this->_eventMock));

        $this->_queueHandlerMock->expects($this->once())->method('addTask')
            ->with('some_event', array('observer' => array('123'), 'configuration' => $configuration), 7);
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
                    'model' => 'some_model',
                    'method' => 'some_method',
                    'config' => array(
                        Enterprise_Queue_Model_Event_Invoker_Asynchronous::CONFIG_PARAMETER_ASYNCHRONOUS => 0,
                    ),
                ),
            ),
            array(
                array(
                    'model' => 'some_model',
                    'method' => 'some_method',
                ),
            ),
        );
    }
}
