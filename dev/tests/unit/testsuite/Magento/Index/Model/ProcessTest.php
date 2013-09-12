<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Index
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Magento_Index_Model_Process
 */
class Magento_Index_Model_ProcessTest extends PHPUnit_Framework_TestCase
{
    /**
     * Process ID for tests
     */
    const PROCESS_ID = 'testProcessId';

    /**
     * @var PHPUnit_Framework_MockObject_MockObject|Magento_Index_Model_Process_File
     */
    protected $_processFile;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject|Magento_Index_Model_Process
     */
    protected $_indexProcess;

    protected function tearDown()
    {
        unset($this->_processFile);
        unset($this->_indexProcess);
    }

    public function testLock()
    {
        $this->_prepareMocksForTestLock(true);

        $result = $this->_indexProcess->lock();
        $this->assertEquals($this->_indexProcess, $result);
    }

    public function testLockAndBlock()
    {
        $this->_prepareMocksForTestLock(false);

        $result = $this->_indexProcess->lockAndBlock();
        $this->assertEquals($this->_indexProcess, $result);
    }

    public function testGetProcessFile()
    {
        $this->_processFile = $this->getMock('Magento_Index_Model_Process_File', array(), array(), '', false, false);
        $this->_prepareIndexProcess();

        // assert that process file is stored in process entity instance and isn't changed after several invocations
        // lock method is used as invocation of _getProcessFile
        for ($i = 1; $i <= 2; $i++) {
            $this->_indexProcess->lock();
            $this->assertAttributeEquals($this->_processFile, '_processFile', $this->_indexProcess);
        }
    }

    /**
     * Create Magento_Index_Model_Process instance for lock tests
     *
     * @param bool $nonBlocking
     */
    protected function _prepareMocksForTestLock($nonBlocking)
    {
        $this->_processFile = $this->getMock('Magento_Index_Model_Process_File', array('processLock'), array(), '',
            false, false
        );
        $this->_processFile->expects($this->once())
            ->method('processLock')
            ->with($nonBlocking);

        $this->_prepareIndexProcess();
    }

    /**
     * Create index process instance
     */
    protected function _prepareIndexProcess()
    {
        $lockStorage = $this->getMock('Magento_Index_Model_Lock_Storage', array('getFile'), array(), '', false);
        $lockStorage->expects($this->once())
            ->method('getFile')
            ->with(self::PROCESS_ID)
            ->will($this->returnValue($this->_processFile));

        $resource = $this->getMockForAbstractClass(
            'Magento_Core_Model_Resource_Db_Abstract',
            array(), '', false, false, true, array('getIdFieldName')
        );
        $resource->expects($this->any())->method('getIdFieldName')->will($this->returnValue('process_id'));
        $helper = new Magento_TestFramework_Helper_ObjectManager($this);
        $this->_indexProcess = $helper->getObject('Magento_Index_Model_Process', array(
            'lockStorage' => $lockStorage,
            'resource' => $resource,
            'data' => array('process_id' => self::PROCESS_ID)
        ));
    }

    public function testUnlock()
    {
        $this->_processFile = $this->getMock('Magento_Index_Model_Process_File', array('processUnlock'));
        $this->_processFile->expects($this->once())
            ->method('processUnlock');
        $this->_prepareIndexProcess();

        $result = $this->_indexProcess->unlock();
        $this->assertEquals($this->_indexProcess, $result);
    }

    /**
     * Data Provider for testIsLocked
     *
     * @return array
     */
    public function isLockedDataProvider()
    {
        return array(
            'need to unlock process'    => array('$needUnlock' => true),
            'no need to unlock process' => array('$needUnlock' => false),
        );
    }

    /**
     * @dataProvider isLockedDataProvider
     * @param bool $needUnlock
     */
    public function testIsLocked($needUnlock)
    {
        $this->_processFile = $this->getMock('Magento_Index_Model_Process_File', array('isProcessLocked'));
        $this->_processFile->expects($this->once())
            ->method('isProcessLocked')
            ->with($needUnlock)
            ->will($this->returnArgument(0));
        $this->_prepareIndexProcess();

        $this->assertEquals($needUnlock, $this->_indexProcess->isLocked($needUnlock));
    }
}
