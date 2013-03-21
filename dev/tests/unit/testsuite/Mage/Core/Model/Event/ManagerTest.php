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

class Mage_Core_Model_Event_ManagerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Varien_Event
     */
    protected $_eventMock;

    /**
     * @var Varien_EventFactory
     */
    protected $_eventFactoryMock;

    /**
     * @var Varien_Event_Observer
     */
    protected $_eventObserverMock;

    /**
     * @var Varien_Event_ObserverFactory
     */
    protected $_eventObserverFactory;

    /**
     * @param array $arguments
     * @return Mage_Core_Model_Event_Manager
     */
    protected function _getEventManagerMock($arguments = array())
    {
        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        return $objectManagerHelper->getObject('Mage_Core_Model_Event_Manager', $arguments);
    }

    protected function _initEventMock()
    {
        $eventMock = $this->getMock('Varien_Event', array(), array(), '', false);
        $eventMock->expects($this->once())->method('setName')->with('some_event')->will($this->returnSelf());

        $this->_eventMock = $eventMock;
    }

    /**
     * @param array $data
     */
    protected function _initEventFactoryMock($data = array())
    {
        $eventFactoryMock = $this->getMock('Varien_EventFactory', array('create'), array(), '', false);
        $eventFactoryMock->expects($this->once())->method('create')->with(array('data' => $data))
            ->will($this->returnValue($this->_eventMock));

        $this->_eventFactoryMock = $eventFactoryMock;
    }

    /**
     * @param array $data
     */
    protected function _initObserverMock($data = array())
    {
        $eventObserverMock = $this->getMock('Varien_Event_Observer', array(), array(), '', false);
        $eventObserverMock->expects($this->once())->method('setData')
            ->with(array_merge(array('event' => $this->_eventMock), $data))->will($this->returnSelf());

        $this->_eventObserverMock = $eventObserverMock;
    }

    protected function _initObserverFactoryMock()
    {
        $eventObserverFactory = $this->getMock('Varien_Event_ObserverFactory', array('create'), array(), '', false);
        $eventObserverFactory->expects($this->once())->method('create')
            ->will($this->returnValue($this->_eventObserverMock));

        $this->_eventObserverFactory = $eventObserverFactory;
    }

    /**
     * @param string $area
     * @dataProvider dataProviderForDispatchWithDifferentArea
     */
    public function testDispatchWithDifferentArea($area)
    {
        $data = array('123');

        $this->_initEventMock();
        $this->_initEventFactoryMock($data);
        $this->_initObserverMock($data);
        $this->_initObserverFactoryMock($data);

        $invokerMock = $this->getMock('Mage_Core_Model_Event_InvokerInterface', array(), array(), '', false);
        $invokerMock->expects($this->once())->method('dispatch')->with(array(
            'model' => 'some_class',
            'method' => 'some_method',
        ), $this->_eventObserverMock);

        $eventManager = $this->_getEventManagerMock(array(
            'invoker' => $invokerMock,
            'eventFactory' => $this->_eventFactoryMock,
            'eventObserverFactory' => $this->_eventObserverFactory,
        ));
        $eventManager->addObservers($area, 'some_event', array(
            'some_observer_name' => array(
                'model' => 'some_class',
                'method' => 'some_method',
            )
        ));
        $eventManager->dispatch('some_event', array('123'));
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
        $invokerMock = $this->getMock('Mage_Core_Model_Event_InvokerInterface', array(), array(), '', false);
        $invokerMock->expects($this->never())->method('dispatch');

        $eventManager = $this->_getEventManagerMock(array(
            'invoker' => $invokerMock
        ));
        $eventManager->dispatch('some_event');
    }

    public function testMergeObservers()
    {
        $this->_initEventMock();
        $this->_initEventFactoryMock();
        $eventObserverMock = $this->getMock('Varien_Event_Observer', array(), array(), '', false);
        $eventObserverMock->expects($this->exactly(2))->method('setData')
            ->with(array('event' => $this->_eventMock))->will($this->returnSelf());
        $this->_eventObserverMock = $eventObserverMock;
        $this->_initObserverFactoryMock();

        $invokerMock = $this->getMock('Mage_Core_Model_Event_InvokerInterface', array(), array(), '', false);
        $invokerMock->expects($this->at(0))->method('dispatch')->with(array(
            'model' => 'some_class',
            'method' => 'some_method',
        ), $this->isInstanceOf('Varien_Event_Observer'));
        $invokerMock->expects($this->at(1))->method('dispatch')->with(array(
            'model' => 'another_some_class',
            'method' => 'another_some_method',
        ), $this->isInstanceOf('Varien_Event_Observer'));

        $eventManager = $this->_getEventManagerMock(array(
            'invoker' => $invokerMock,
            'eventFactory' => $this->_eventFactoryMock,
            'eventObserverFactory' => $this->_eventObserverFactory,
        ));
        $eventManager->addObservers(Mage_Core_Model_App_Area::AREA_ADMIN, 'some_event', array(
            'some_observer_name' => array(
                'model' => 'some_class',
                'method' => 'some_method',
            )
        ));
        $eventManager->addObservers(Mage_Core_Model_App_Area::AREA_ADMIN, 'some_event', array(
            'another_observer_name' => array(
                'model' => 'another_some_class',
                'method' => 'another_some_method',
            )
        ));
        $eventManager->dispatch('some_event');
    }
}
