<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Log_Model_Shell_Command_CleanTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeManagerMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_logFactoryMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_logMock;

    protected function setUp()
    {
        $this->_storeManagerMock = $this->getMock('Magento_Core_Model_StoreManagerInterface');
        $this->_logFactoryMock = $this->getMock('Magento_Log_Model_LogFactory', array('create'), array(), '', false);
        $this->_logMock = $this->getMock('Magento_Log_Model_Log', array(), array(), '', false);
        $this->_logFactoryMock->expects($this->once())->method('create')->will($this->returnValue($this->_logMock));
    }

    public function testExecuteWithoutDaysOffset()
    {
        $model = new Magento_Log_Model_Shell_Command_Clean($this->_storeManagerMock, $this->_logFactoryMock, 0);
        $this->_storeManagerMock->expects($this->never())->method('getStore');
        $this->_logMock->expects($this->once())->method('clean');
        $this->assertStringStartsWith('Log cleaned', $model->execute());
    }

    public function testExecuteWithDaysOffset()
    {
        $model = new Magento_Log_Model_Shell_Command_Clean($this->_storeManagerMock, $this->_logFactoryMock, 10);
        $storeMock = $this->getMock('Magento_Core_Model_Store', array(), array(), '', false);
        $this->_storeManagerMock->expects($this->once())->method('getStore')->will($this->returnValue($storeMock));

        $this->_logMock->expects($this->once())->method('clean');
        $storeMock->expects($this->once())->method('setConfig')->with(Magento_Log_Model_Log::XML_LOG_CLEAN_DAYS, 10);
        $this->assertStringStartsWith('Log cleaned', $model->execute());
    }
}