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
     * @var Mage_Core_Model_Event_InvokerInterface|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_invoker;

    /**
     * @var Varien_EventFactory|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_eventFactory;

    /**
     * @var Varien_Event|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_event;

    /**
     * @var Varien_Event_ObserverFactory|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_observerFactory;

    /**
     * @var Varien_Event_Observer|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_observer;

    /**
     * @var Mage_Core_Model_Event_Manager
     */
    protected $_eventManager;

    protected function setUp()
    {
        $this->_invoker = $this->getMock('Mage_Core_Model_Event_InvokerInterface');
        $this->_eventFactory = $this->getMock('Varien_EventFactory', array('create'), array(), '', false);
        $this->_event = $this->getMock('Varien_Event', array(), array(), '', false);
        $this->_observerFactory = $this->getMock('Varien_Event_ObserverFactory', array('create'), array(), '',
            false);
        $this->_observer = $this->getMock('Varien_Event_Observer', array(), array(), '', false);

        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $this->_eventManager = $objectManagerHelper->getObject('Mage_Core_Model_Event_Manager', array(
            'invoker' => $this->_invoker,
            'eventFactory' => $this->_eventFactory,
            'eventObserverFactory' => $this->_observerFactory,
        ));
    }

    /**
     * @param string $area
     * @dataProvider dataProviderForDispatchWithDifferentArea
     */
    public function testDispatchWithDifferentArea($area)
    {
        $data = array('123');

        $this->_event->expects($this->once())->method('setName')->with('some_event')->will($this->returnSelf());
        $this->_eventFactory->expects($this->once())->method('create')->with(array('data' => $data))
            ->will($this->returnValue($this->_event));

        $this->_observer->expects($this->once())->method('setData')
            ->with(array_merge(array('event' => $this->_event), $data))->will($this->returnSelf());
        $this->_observerFactory->expects($this->once())->method('create')
            ->will($this->returnValue($this->_observer));
        $this->_invoker->expects($this->once())->method('dispatch')->with(array(
            'model' => 'some_class',
            'method' => 'some_method',
        ), $this->_observer);

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
        $this->_invoker->expects($this->never())->method('dispatch');

        $this->_eventManager->dispatch('some_event');
    }

    public function testMergeObservers()
    {
        $data = array('123');

        $this->_event->expects($this->once())->method('setName')->with('some_event')->will($this->returnSelf());
        $this->_eventFactory->expects($this->once())->method('create')->with(array('data' => $data))
            ->will($this->returnValue($this->_event));

        $this->_observer->expects($this->exactly(2))->method('setData')
            ->with(array_merge(array('event' => $this->_event), $data))->will($this->returnSelf());
        $this->_observerFactory->expects($this->once())->method('create')
            ->will($this->returnValue($this->_observer));

        $this->_invoker->expects($this->at(0))->method('dispatch')->with(array(
            'model' => 'some_class',
            'method' => 'some_method',
        ), $this->isInstanceOf('Varien_Event_Observer'));
        $this->_invoker->expects($this->at(1))->method('dispatch')->with(array(
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
