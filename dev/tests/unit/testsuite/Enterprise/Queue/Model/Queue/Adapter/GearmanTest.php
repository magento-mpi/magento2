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

class Enterprise_Queue_Model_Queue_Adapter_GearmanTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param array $arguments
     * @return Enterprise_Queue_Model_Queue_DefaultHandler
     */
    protected function _getQueueAdapterGearman($arguments = array())
    {
        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);

        return $objectManagerHelper->getObject('Enterprise_Queue_Model_Queue_Adapter_Gearman', $arguments);
    }

    public function testAddTaskTest()
    {
        $data = array('123');
        $preparedData = '{prepared_data}';

        $clientMock = $this->getMock('GearmanClient', array(), array(), '', false);
        $clientMock->expects($this->once())->method('addServers')->with('127.0.0.1:4730');
        $clientMock->expects($this->once())->method('doBackground')->with('some_event', $preparedData);

        $helperGearmanMock = $this->getMock('Enterprise_Queue_Helper_Gearman', array(), array(), '', false);
        $helperGearmanMock->expects($this->once())->method('getServers')->will($this->returnValue('127.0.0.1:4730'));
        $helperGearmanMock->expects($this->once())->method('prepareData')->with($data)
            ->will($this->returnValue($preparedData));

        $adapterGearman = $this->_getQueueAdapterGearman(array(
            'client' => $clientMock,
            'helperGearman' => $helperGearmanMock,
        ));
        $this->assertEquals($adapterGearman, $adapterGearman->addTask('some_event', $data, 7));
    }
}
