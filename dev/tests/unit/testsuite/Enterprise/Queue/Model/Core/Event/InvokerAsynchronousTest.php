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

class Enterprise_Queue_Model_Core_Event_InvokerAsynchronousTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param array $arguments
     * @return Enterprise_Queue_Model_Core_Event_InvokerAsynchronous
     */
    protected function _getEventInvokerAsynchronous($arguments = array())
    {
        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);

        return $objectManagerHelper->getObject('Enterprise_Queue_Model_Core_Event_InvokerAsynchronous', $arguments);
    }

    public function testDispatchWithAsynchronousMode()
    {
        $configuration = array(
            'model' => 'some_model',
            'method' => 'some_method',
            'config' => array(
                Enterprise_Queue_Model_Core_Event_InvokerAsynchronous::CONFIG_PARAMETER_ASYNCHRONOUS => true,
                Enterprise_Queue_Model_Core_Event_InvokerAsynchronous::CONFIG_PARAMETER_PRIORITY => 7,
            )
        );

        $eventMock = $this->getMock('Varien_Event', array(), array(), '', false);
        $eventMock->expects($this->once())->method('getName')->will($this->returnValue('some_event'));

        $eventObserverMock = $this->getMock('Varien_Event_Observer', array(), array(), '', false);
        $eventObserverMock->expects($this->once())->method('getEvent')->will($this->returnValue($eventMock));
        $eventObserverMock->expects($this->once())->method('toArray')->will($this->returnValue(array('123')));

        $queueHandlerMock = $this->getMock('Enterprise_Queue_Model_Queue_HandlerInterface', array(), array(), '',
            false);
        $queueHandlerMock->expects($this->once())->method('addTask')->with('some_event', array('123'), 7);

        $invokerDefaultMock = $this->getMock('Mage_Core_Model_Event_InvokerDefault', array(), array(), '', false);
        $invokerDefaultMock->expects($this->never())->method('dispatch');

        $invokerAsynchronous = $this->_getEventInvokerAsynchronous(array(
            'queueHandler' => $queueHandlerMock,
            'invokerDefault' => $invokerDefaultMock,
        ));
        $invokerAsynchronous->dispatch($configuration, $eventObserverMock);
    }

    /**
     * @param array $configuration
     * @dataProvider dataProviderForDispatchWithNotAsynchronousMode
     */
    public function testDispatchWithNonAsynchronousMode($configuration)
    {
        $eventObserverMock = $this->getMock('Varien_Event_Observer', array(), array(), '', false);

        $queueHandlerMock = $this->getMock('Enterprise_Queue_Model_Queue_HandlerInterface', array(), array(), '',
            false);
        $queueHandlerMock->expects($this->never())->method('addTask');

        $invokerDefaultMock = $this->getMock('Mage_Core_Model_Event_InvokerDefault', array(), array(), '', false);
        $invokerDefaultMock->expects($this->once())->method('dispatch')->with($configuration, $eventObserverMock);

        $invokerAsynchronous = $this->_getEventInvokerAsynchronous(array(
            'queueHandler' => $queueHandlerMock,
            'invokerDefault' => $invokerDefaultMock,
        ));
        $invokerAsynchronous->dispatch($configuration, $eventObserverMock);
    }

    /**
     * @return array
     */
    public function dataProviderForDispatchWithNotAsynchronousMode()
    {
        return array(
            array(
                array(
                    'model' => 'some_model',
                    'method' => 'some_method',
                    'config' => array(
                        Enterprise_Queue_Model_Core_Event_InvokerAsynchronous::CONFIG_PARAMETER_ASYNCHRONOUS => false,
                    )
                )
            ),
            array(
                array(
                    'model' => 'some_model',
                    'method' => 'some_method',
                )
            )
        );
    }
}
