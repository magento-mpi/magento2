<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Saas_Saas_Model_EntryPoint_WorkerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManagerMock;

    protected function setUp()
    {
        $this->_objectManagerMock = $this->getMock('Magento_ObjectManager');
    }

    /**
     * @param string $taskName
     * @param mixed $taskParams
     * @dataProvider taskParamsDataProvider
     */
    public function testWorkerTaskHandler($taskName, $taskParams)
    {
        $params = array(
            Saas_Saas_Model_EntryPoint_Worker::TASK_OPTIONS_KEY => array(
                array('task_name' => $taskName, 'params' => $taskParams)
            )
        );
        $config = new Magento_Core_Model_Config_Primary(BP, $params);
        Mage::reset(); // hack to reset object manager if it happens to be set in this class already
        $worker = new Saas_Saas_Model_EntryPoint_Worker($config, $this->_objectManagerMock);
        $dispatcher = $this->getMock('Magento_Core_Model_Event_Manager', array(), array(), '', false);

        if (array_key_exists(Saas_Saas_Model_EntryPoint_Worker::EVENT_NAME_KEY, $taskParams)) {
            //Using worker task as event transport
            $eventName = $taskParams[Saas_Saas_Model_EntryPoint_Worker::EVENT_NAME_KEY];
            if (array_key_exists(Saas_Saas_Model_EntryPoint_Worker::EVENT_DATA_KEY, $taskParams)) {
                $eventData = $taskParams[Saas_Saas_Model_EntryPoint_Worker::EVENT_DATA_KEY];
            } else {
                $eventData = array();
            }
        } else {
            $eventName = $taskName;
            $eventData = $taskParams;
        }
        $dispatcher->expects($this->exactly(2))
            ->method('dispatch')
            ->with(
                $this->logicalOr($eventName, 'job_complete'),
                $this->logicalOr($eventData, array('task_name' => $taskName))
            );

        $app = $this->getMock('Magento_Core_Model_App', array(), array(), '', false);
        $app->expects($this->once())->method('setUseSessionInUrl')->with(false);
        $app->expects($this->once())->method('requireInstalledInstance');

        $valueMap = array(
            array('Magento_Core_Model_Config_Primary', $config),
            array('Magento_Core_Model_App', $app),
        );
        $this->_objectManagerMock
            ->expects($this->exactly(2))
            ->method('get')
            ->will($this->returnValueMap($valueMap));
        $this->_objectManagerMock
            ->expects($this->once())
            ->method('create')
            ->with('Magento_Core_Model_Event_Manager', array('invoker' => 'Magento_Core_Model_Event_Invoker_InvokerDefault'))
            ->will($this->returnValue($dispatcher));
        $worker->processRequest();
    }

    public function taskParamsDataProvider()
    {
        return array(
            array('dummy', array()),
            array('worker_as_event_transport', array(Saas_Saas_Model_EntryPoint_Worker::EVENT_NAME_KEY => 'test')),
            array(
                'worker_as_event_transport_with_data',
                array(
                    Saas_Saas_Model_EntryPoint_Worker::EVENT_NAME_KEY => 'test',
                    Saas_Saas_Model_EntryPoint_Worker::EVENT_DATA_KEY => array('key' => 'value'),
                )
            ),
        );
    }
}
