<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Index
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Index_Model_Process
 */
class Mage_Index_Model_ProcessTest extends PHPUnit_Framework_TestCase
{
    /** Process id for tests */
    const PROCESS_ID ='testId';

    /**
     * Object Manager Helper for tests
     *
     * @var Magento_Test_Helper_ObjectManager
     */
    protected $_objectManagerHelper;

    /**#@+
     * Objects for Mage_Index_Model_Process __constructor
     */
    protected $_eventDispatcher;
    protected $_cacheManager;
    protected $_processFile;
    /**#@-*/

    /**
     * Index Process for test
     *
     * @var Mage_Index_Model_Process
     */
    protected $_indexProcess;

    protected function setUp()
    {
        $this->_objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $this->_eventDispatcher = $this->getMock('Mage_Core_Model_Event_Manager', array(), array(), '', false);
        $this->_cacheManager = $this->_objectManagerHelper->getModel('Mage_Core_Model_Cache');
    }

    protected function tearDown()
    {
        unset($this->_objectManagerHelper);
        unset($this->_eventDispatcher);
        unset($this->_cacheManager);
        unset($this->_processFile);
    }

    /**
     * Create Mage_Index_Model_Process instance for lock tests
     *
     * @param bool $nonBlocking
     */
    protected function _prepareLockFileForLockTests($nonBlocking)
    {
        $this->_processFile = $this->getMock('Mage_Index_Model_Process_File',
            array('processLock')
        );
        $this->_processFile->expects($this->once())
            ->method('processLock')
            ->with($nonBlocking);

        $lockStorage = $this->getMock('Mage_Index_Model_Lock_Storage', array('getFile'), array(), '', false);
        $lockStorage->expects($this->once())
            ->method('getFile')
            ->with(self::PROCESS_ID)
            ->will($this->returnValue($this->_processFile));
        $this->_indexProcess = new Mage_Index_Model_Process(
            $this->_eventDispatcher,
            $this->_cacheManager,
            $lockStorage,
            null,
            null,
            array('process_id' => self::PROCESS_ID)
        );
    }

    public function testLock()
    {
        $this->_prepareLockFileForLockTests(true);
        $this->assertInstanceOf('Mage_Index_Model_Process', $this->_indexProcess->lock());
        $this->assertAttributeEquals($this->_processFile, '_processFile', $this->_indexProcess);
    }

    public function testLockAndBlock()
    {
        $this->_prepareLockFileForLockTests(false);
        $this->assertInstanceOf('Mage_Index_Model_Process', $this->_indexProcess->lockAndBlock());
        $this->assertAttributeEquals($this->_processFile, '_processFile', $this->_indexProcess);
    }

    /**
     * Create Mage_Index_Model_Process instance for isLocked tests
     *
     * @param bool $needUnlock
     */
    protected function _prepareLockFileForIsLockedTests($needUnlock)
    {
        $this->_processFile = $this->getMock('Mage_Index_Model_Process_File',
            array('isProcessLocked')
        );
        $this->_processFile->expects($this->once())
            ->method('isProcessLocked')
            ->with($needUnlock)
            ->will($this->returnArgument(0));

        $lockStorage = $this->getMock('Mage_Index_Model_Lock_Storage', array('getFile'), array(), '', false);
        $lockStorage->expects($this->once())
            ->method('getFile')
            ->with(self::PROCESS_ID)
            ->will($this->returnValue($this->_processFile));
        $this->_indexProcess = new Mage_Index_Model_Process(
            $this->_eventDispatcher,
            $this->_cacheManager,
            $lockStorage,
            null,
            null,
            array('process_id' => self::PROCESS_ID)
        );
    }

    /**
     * @dataProvider needUnlockForIsLockedDataProvider
     * @param bool $needUnlock
     */
    public function testIsLocked($needUnlock)
    {
        $this->_prepareLockFileForIsLockedTests($needUnlock);
        $this->assertEquals($needUnlock, $this->_indexProcess->isLocked($needUnlock));
    }

    /**
     * Data Provider for unlock method
     *
     * @return array
     */
    public function needUnlockForIsLockedDataProvider()
    {
        return array(
            'need unlock process' => array(true),
            'no need unlock process' => array(false)
        );
    }

    /**
     * Create Mage_Index_Model_Process instance for unlock tests
     */
    protected function _prepareLockFileForUnlockTest()
    {
        $this->_processFile = $this->getMock('Mage_Index_Model_Process_File',
            array('processUnlock')
        );
        $this->_processFile->expects($this->once())
            ->method('processUnlock');

        $lockStorage = $this->getMock('Mage_Index_Model_Lock_Storage', array('getFile'), array(), '', false);
        $lockStorage->expects($this->once())
            ->method('getFile')
            ->with(self::PROCESS_ID)
            ->will($this->returnValue($this->_processFile));
        $this->_indexProcess = new Mage_Index_Model_Process(
            $this->_eventDispatcher,
            $this->_cacheManager,
            $lockStorage,
            null,
            null,
            array('process_id' => self::PROCESS_ID)
        );
    }

    public function testUnlock()
    {
        $this->_prepareLockFileForUnlockTest();
        $this->assertInstanceOf('Mage_Index_Model_Process', $this->_indexProcess->unlock());
    }
}
