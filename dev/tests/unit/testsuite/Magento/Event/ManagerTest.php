<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Event;

class ManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_invoker;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_eventFactory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_event;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_wrapperFactory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_observer;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_eventConfigMock;

    /**
     * @var \Magento\Event\Manager
     */
    protected $_eventManager;

    protected function setUp()
    {
        $this->_invoker = $this->getMock('Magento\Event\InvokerInterface');
        $this->_eventConfigMock = $this->getMock('Magento\Event\ConfigInterface');

        $this->_eventManager = new \Magento\Event\Manager($this->_invoker, $this->_eventConfigMock);
    }

    public function testDispatch()
    {
        $this->_eventConfigMock->expects(
            $this->once()
        )->method(
            'getObservers'
        )->with(
            'some_event'
        )->will(
            $this->returnValue(
                array('observer' => array('instance' => 'class', 'method' => 'method', 'name' => 'observer'))
            )
        );
        $this->_eventManager->dispatch('some_event', array('123'));
    }

    public function testDispatchWithEmptyEventObservers()
    {
        $this->_eventConfigMock->expects(
            $this->once()
        )->method(
            'getObservers'
        )->with(
            'some_event'
        )->will(
            $this->returnValue(array())
        );
        $this->_invoker->expects($this->never())->method('dispatch');
        $this->_eventManager->dispatch('some_event');
    }
}
