<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Queue_Model_Queue_DefaultHandlerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_adapterMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_helperGearmanMock;

    /**
     * @var Saas_Queue_Model_Queue_DefaultHandler
     */
    protected $_defaultHandler;

    /**
     * @var Varien_Event
     */
    protected $_eventMock;

    protected function setUp()
    {
        $this->_eventMock = $this->getMock('Varien_Event');
        $this->_adapterMock = $this->getMock('Enterprise_Queue_Model_Queue_AdapterInterface');
        $this->_helperGearmanMock = $this->getMock('Saas_Queue_Helper_Gearman', array(), array(), '', false);
        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $this->_defaultHandler = $objectManagerHelper->getObject('Saas_Queue_Model_Queue_DefaultHandler', array(
            'adapter' => $this->_adapterMock,
            'helper'  => $this->_helperGearmanMock
        ));
    }

    /**
     * @param string $eventName
     * @param array $data
     * @param array $eventData
     * @param string $adapterTaskName
     * @param array $adapterData
     * @dataProvider eventDataProvider
     */
    public function testTaskDataPreparation($eventName, $data, $eventData, $adapterTaskName, $adapterData)
    {
        //Prepare event
        $data['observer']['event'] = $this->_eventMock;
        $this->_eventMock->expects($this->once())->method('getData')
            ->will($this->returnValue($eventData));

        $this->_helperGearmanMock->expects($this->once())->method('getTaskParams')
            ->will($this->returnValue(array()));

        $this->_adapterMock->expects($this->once())->method('addTask')->with($adapterTaskName, $adapterData)
            ->will($this->returnSelf());

        $this->assertEquals($this->_defaultHandler, $this->_defaultHandler->addTask($eventName, $data));
    }

    public function eventDataProvider()
    {
        $eventName = 'handler_event_name';
        $taskName  = 'task_name';
        $eventArea = 'event_area';
        $eventData = array(1,2,3);

        return array(
            'default' => array(
                //Handler params
                $eventName,
                array(
                    'observer' => array(
                        'event' => null
                    ),
                    'configuration' => array (
                        'config' => array (
                            'params' => array (),
                        ),
                    ),
                ),
                $eventData,

                //Expected adapter params
                $eventName,
                array(
                    'task_name' => $eventName,
                    'params' => $eventData,
                ),
            ),
            'worker_as_event_transport' => array(
                //Handler params
                $eventName,
                array(
                    'observer' => array(
                        'event' => null
                    ),
                    'configuration' => array (
                        'config' => array (
                            'params' => array (
                                'event_area' => $eventArea,
                                'task_name'  => $taskName,
                            ),
                        ),
                    ),
                ),
                $eventData,

                //Expected adapter params
                $taskName,
                array(
                    'task_name' => $taskName,
                    'params' => array(
                        'event_name' => $eventName,
                        'event_data' => $eventData,
                        'event_area' => $eventArea,
                    ),
                ),
            )
        );
    }
}
