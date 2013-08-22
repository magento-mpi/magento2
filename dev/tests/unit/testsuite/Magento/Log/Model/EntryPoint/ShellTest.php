<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Log_Model_EntryPoint_ShellTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Log_Model_EntryPoint_Shell
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManagerMock;

    protected function setUp()
    {
        $config = $this->getMock('Magento_Core_Model_Config_Primary', array(), array(), '', false);
        $entryFileName = 'shell.php';
        $this->_objectManagerMock = $this->getMock('Magento_ObjectManager');
        $this->_model = new Magento_Log_Model_EntryPoint_Shell($config, $entryFileName, $this->_objectManagerMock);
    }

    public function testProcessRequest()
    {
        $shellMock = $this->getMock('Magento_Log_Model_Shell', array(), array(), '', false);

        $this->_objectManagerMock->expects($this->once())
            ->method('create')
            ->with('Magento_Log_Model_Shell', array('entryPoint' => 'shell.php'))
            ->will($this->returnValue($shellMock));

        $shellMock->expects($this->once())->method('run');
        $this->_model->processRequest();
    }
}
