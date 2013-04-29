<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Queue_Model_Event_HandlerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_queueMock;

    /**
     * @var Saas_Queue_Model_Event_Handler
     */
    protected $_model;

    /**
     * @var Varien_Event
     */
    protected $_eventMock;

    protected function setUp()
    {
        $this->_eventMock = $this->getMock('Varien_Event');
        $this->_queueMock = $this->getMock('Enterprise_Queue_Model_QueueInterface');
        $this->_model = new Saas_Queue_Model_Event_Handler($this->_queueMock);
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

        $this->_queueMock->expects($this->once())->method('addTask')->with($adapterTaskName, $adapterData)
            ->will($this->returnSelf());

        $this->assertEquals($this->_model, $this->_model->addTask($eventName, $data));
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
