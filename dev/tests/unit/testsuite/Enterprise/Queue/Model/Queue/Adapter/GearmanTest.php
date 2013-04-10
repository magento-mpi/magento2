<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Enterprise_Queue_Model_Queue_Adapter_GearmanTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_helperGearmanMock;

    /**
     * @var Enterprise_Queue_Model_Queue_Adapter_Gearman
     */
    protected $_adapterGearman;

    protected function setUp()
    {
        //Temporary solution. See MAGETWO-8375
        if (!extension_loaded('gearman')) {
            $this->markTestSkipped("'Gearman' extension is not loaded");
        }
        $this->_helperGearmanMock = $this->getMock('Enterprise_Queue_Helper_Gearman', array(), array(), '', false);
        $this->_helperGearmanMock->expects($this->once())->method('getServers')
            ->will($this->returnValue('127.0.0.1:4730'));

        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $this->_adapterGearman = $objectManagerHelper->getObject('Enterprise_Queue_Model_Queue_Adapter_Gearman', array(
            'helperGearman' => $this->_helperGearmanMock,
        ));
    }

    public function testAddTaskTest()
    {
        $data = array('123');
        $preparedData = '{prepared_data}';

        $this->_helperGearmanMock->expects($this->once())->method('encodeData')->with($data)
            ->will($this->returnValue($preparedData));

        $this->assertEquals($this->_adapterGearman, $this->_adapterGearman->addTask('some_event', $data, 7));
    }
}
