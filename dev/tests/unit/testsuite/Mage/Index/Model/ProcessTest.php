<?php
/**
 * Test class for Mage_Index_Model_Process
 *
 * @copyright {}
 */
class Mage_Index_Model_ProcessTest extends PHPUnit_Framework_TestCase
{
    /**
     * Process ID for tests
     */
    const PROCESS_ID ='testProcessId';

    /**
     * @var PHPUnit_Framework_MockObject_MockObject|Mage_Index_Model_Process_File
     */
    protected $_processFile;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject|Mage_Index_Model_Process
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
        $this->_processFile = $this->getMock('Mage_Index_Model_Process_File');
        $this->_prepareIndexProcess();

        // assert that process file is stored in process instance
        // lock method is used as invocation of _getProcessFile
        $this->_indexProcess->lock();
        $this->assertAttributeEquals($this->_processFile, '_processFile', $this->_indexProcess);
        $this->_indexProcess->lock();
        $this->assertAttributeEquals($this->_processFile, '_processFile', $this->_indexProcess);
    }

    /**
     * Create Mage_Index_Model_Process instance for lock tests
     *
     * @param bool $nonBlocking
     */
    protected function _prepareMocksForTestLock($nonBlocking)
    {
        $this->_processFile = $this->getMock('Mage_Index_Model_Process_File', array('processLock'));
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
        /** @var $eventDispatcher Mage_Core_Model_Event_Manager */
        $eventDispatcher = $this->getMock('Mage_Core_Model_Event_Manager', array(), array(), '', false);
        /** @var $cacheManager Mage_Core_Model_Cache */
        $cacheManager = $this->getMock('Mage_Core_Model_Cache', array(), array(), '', false);

        $lockStorage = $this->getMock('Mage_Index_Model_Lock_Storage', array('getFile'), array(), '', false);
        $lockStorage->expects($this->once())
            ->method('getFile')
            ->with(self::PROCESS_ID)
            ->will($this->returnValue($this->_processFile));

        $this->_indexProcess = new Mage_Index_Model_Process(
            $eventDispatcher,
            $cacheManager,
            $lockStorage,
            null,
            null,
            array('process_id' => self::PROCESS_ID)
        );
    }

    public function testUnlock()
    {
        $this->_processFile = $this->getMock('Mage_Index_Model_Process_File', array('processUnlock'));
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
        $this->_processFile = $this->getMock('Mage_Index_Model_Process_File', array('isProcessLocked'));
        $this->_processFile->expects($this->once())
            ->method('isProcessLocked')
            ->with($needUnlock)
            ->will($this->returnArgument(0));
        $this->_prepareIndexProcess();

        $this->assertEquals($needUnlock, $this->_indexProcess->isLocked($needUnlock));
    }
}
