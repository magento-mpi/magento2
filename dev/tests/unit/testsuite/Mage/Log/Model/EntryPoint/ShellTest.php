<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Log_Model_EntryPoint_ShellTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManagerMock;

    protected function setUp()
    {
        $config = $this->getMock('Mage_Core_Model_Config_Primary', array(), array(), '', false);
        $entryFileName = 'shell.php';
        $this->_objectManagerMock = $this->getMock('Magento_ObjectManager');
        $this->_model = $this->getMock('Mage_Log_Model_EntryPoint_Shell',
            array('_setGlobalObjectManager'), array($config, $entryFileName, $this->_objectManagerMock)
        );
    }

    public function testProcessRequest()
    {
        $this->_objectManagerMock->expects($this->once())
            ->method('get')
            ->with('Mage_Core_Model_Dir_Verification')
            ->will($this->returnValue($this->getMock('Mage_Core_Model_Dir_Verification', array(), array(), '', false)));

        $shellMock = $this->getMock('Mage_Log_Model_Shell', array(), array(), '', false);

        $this->_objectManagerMock->expects($this->once())
            ->method('create')
            ->with('Mage_Log_Model_Shell', array('entryPoint' => 'shell.php'))
            ->will($this->returnValue($shellMock));

        $shellMock->expects($this->once())->method('run');
        $this->_model->processRequest();
    }
}