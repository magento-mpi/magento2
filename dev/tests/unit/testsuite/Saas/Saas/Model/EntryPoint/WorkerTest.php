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
        $config = new Mage_Core_Model_Config_Primary(BP, $params);
        $worker = new Saas_Saas_Model_EntryPoint_Worker($config, $this->_objectManagerMock);
        $dispatcher = $this->getMock('Mage_Core_Model_Event_Manager', array(), array(), '', false);
        $dispatcher->expects($this->once())->method('dispatch')->with($taskName, $taskParams);
        $app = $this->getMock('Mage_Core_Model_App', array(), array(), '', false);
        $app->expects($this->once())->method('setUseSessionInUrl')->with(false);
        $app->expects($this->once())->method('requireInstalledInstance');

        $valueMap = array(
            array('Mage_Core_Model_Config_Primary', $config),
            array('Mage_Core_Model_App', $app),
        );
        $this->_objectManagerMock
            ->expects($this->exactly(2))
            ->method('get')
            ->will($this->returnValueMap($valueMap));
        $this->_objectManagerMock
            ->expects($this->once())
            ->method('create')
            ->with('Mage_Core_Model_Event_Manager')
            ->will($this->returnValue($dispatcher));
        $worker->processRequest();
    }

    public function taskParamsDataProvider()
    {
        return array(
            array('dummy', array())
        );
    }
}
