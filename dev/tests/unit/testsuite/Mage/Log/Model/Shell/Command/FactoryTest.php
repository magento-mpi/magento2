<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Log_Model_Shell_Command_FactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManagerMock;

    /**
     * @var Mage_Log_Model_Shell_Command_Factory
     */
    protected $_model;

    protected function setUp()
    {
        $this->_objectManagerMock = $this->getMock('Magento_ObjectManager');
        $this->_model = new Mage_Log_Model_Shell_Command_Factory($this->_objectManagerMock);
    }

    public function testCreateCleanCommand()
    {
        $this->_objectManagerMock->expects($this->once())
            ->method('create')
            ->with('Mage_Log_Model_Shell_Command_Clean', array('days' => 1))
            ->will($this->returnValue(
                $this->getMock('Mage_Log_Model_Shell_Command_Clean', array(), array(), '', false)
            )
        );
        $this->isInstanceOf('Mage_Log_Model_Shell_CommandInterface', $this->_model->createCleanCommand(1));
    }

    public function testCreateStatusCommand()
    {
        $this->_objectManagerMock->expects($this->once())
            ->method('create')
            ->with('Mage_Log_Model_Shell_Command_Status')
            ->will($this->returnValue(
                $this->getMock('Mage_Log_Model_Shell_Command_Status', array(), array(), '', false)
            )
        );
        $this->isInstanceOf('Mage_Log_Model_Shell_CommandInterface', $this->_model->createStatusCommand());
    }
}