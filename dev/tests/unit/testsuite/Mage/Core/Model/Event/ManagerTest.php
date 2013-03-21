<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Event_ManagerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_invokerMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_eventFactoryMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_eventMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_eventObserverFactoryMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_eventObserverMock;

    /**
     * @var Mage_Core_Model_Event_Manager
     */
    protected $_eventManager;

    protected function setUp()
    {
        $this->_invokerMock = $this->getMock('Mage_Core_Model_Event_InvokerInterface');
        $this->_eventFactoryMock = $this->getMock('Varien_EventFactory', array('create'), array(), '', false);
        $this->_eventMock = $this->getMock('Varien_Event', array(), array(), '', false);
        $this->_eventObserverFactoryMock = $this->getMock('Varien_Event_ObserverFactory', array('create'), array(), '',
            false);
        $this->_eventObserverMock = $this->getMock('Varien_Event_Observer', array(), array(), '', false);

        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $this->_eventManager = $objectManagerHelper->getObject('Mage_Core_Model_Event_Manager', array(
            'invoker' => $this->_invokerMock,
            'eventFactory' => $this->_eventFactoryMock,
            'eventObserverFactory' => $this->_eventObserverFactoryMock,
        ));
    }

    /**
     * @param string $area
     * @dataProvider dataProviderForDispatchWithDifferentArea
     */
    public function testDispatchWithDifferentArea($area)
    {
        $data = array('123');

        $this->_eventMock->expects($this->once())->method('setName')->with('some_event')->will($this->returnSelf());
        $this->_eventFactoryMock->expects($this->once())->method('create')->with(array('data' => $data))
            ->will($this->returnValue($this->_eventMock));

        $this->_eventObserverMock->expects($this->once())->method('setData')
            ->with(array_merge(array('event' => $this->_eventMock), $data))->will($this->returnSelf());
        $this->_eventObserverFactoryMock->expects($this->once())->method('create')
            ->will($this->returnValue($this->_eventObserverMock));
        $this->_invokerMock->expects($this->once())->method('dispatch')->with(array(
            'model' => 'some_class',
            'method' => 'some_method',
        ), $this->_eventObserverMock);

        $this->_eventManager->addObservers($area, 'some_event', array(
            'some_observer_name' => array(
                'model' => 'some_class',
                'method' => 'some_method',
            )
        ));
        $this->_eventManager->dispatch('some_event', array('123'));
    }

    /**
     * @return array
     */
    public function dataProviderForDispatchWithDifferentArea()
    {
        return array(
            array(Mage_Core_Model_App_Area::AREA_ADMIN),
            array(Mage_Core_Model_App_Area::AREA_ADMINHTML),
            array(Mage_Core_Model_App_Area::AREA_FRONTEND),
            array(Mage_Core_Model_App_Area::AREA_GLOBAL),
        );
    }

    public function testDispatchWithEmptyAreaEvents()
    {
        $this->_invokerMock->expects($this->never())->method('dispatch');

        $this->_eventManager->dispatch('some_event');
    }

    public function testMergeObservers()
    {
        $data = array('123');

        $this->_eventMock->expects($this->once())->method('setName')->with('some_event')->will($this->returnSelf());
        $this->_eventFactoryMock->expects($this->once())->method('create')->with(array('data' => $data))
            ->will($this->returnValue($this->_eventMock));

        $this->_eventObserverMock->expects($this->exactly(2))->method('setData')
            ->with(array_merge(array('event' => $this->_eventMock), $data))->will($this->returnSelf());
        $this->_eventObserverFactoryMock->expects($this->once())->method('create')
            ->will($this->returnValue($this->_eventObserverMock));

        $this->_invokerMock->expects($this->at(0))->method('dispatch')->with(array(
            'model' => 'some_class',
            'method' => 'some_method',
        ), $this->isInstanceOf('Varien_Event_Observer'));
        $this->_invokerMock->expects($this->at(1))->method('dispatch')->with(array(
            'model' => 'another_some_class',
            'method' => 'another_some_method',
        ), $this->isInstanceOf('Varien_Event_Observer'));

        $this->_eventManager->addObservers(Mage_Core_Model_App_Area::AREA_ADMIN, 'some_event', array(
            'some_observer_name' => array(
                'model' => 'some_class',
                'method' => 'some_method',
            )
        ));
        $this->_eventManager->addObservers(Mage_Core_Model_App_Area::AREA_ADMIN, 'some_event', array(
            'another_observer_name' => array(
                'model' => 'another_some_class',
                'method' => 'another_some_method',
            )
        ));
        $this->_eventManager->dispatch('some_event', $data);
    }
}
