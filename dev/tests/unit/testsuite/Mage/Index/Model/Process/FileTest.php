<?php
/**
 * Test class for Mage_Index_Model_Process_File
 *
 * @copyright {}
 */
class Mage_Index_Model_Process_FileTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test lock name
     */
    const FILE_NAME = 'index_test.lock';

    /**
     * Test file directory
     *
     * @var string
     */
    protected $_fileDirectory;

    /**
     * Full test file name
     *
     * @var string
     */
    protected $_fullFileName;

    /**
     * @var Mage_Index_Model_Process_File
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model         = new Mage_Index_Model_Process_File();
        $this->_fileDirectory = TESTS_TEMP_DIR;
        $this->_fullFileName  = $this->_fileDirectory . DIRECTORY_SEPARATOR . self::FILE_NAME;
    }

    protected function tearDown()
    {
        unset($this->_model);
        unset($this->_fileDirectory);
        unset($this->_fullFileName);
    }

    /**
     * Open test file
     */
    protected function _openFile()
    {
        $this->_model->cd($this->_fileDirectory);
        $this->_model->streamOpen(self::FILE_NAME);
    }

    /**
     * Get test file handler - file is the same as in $this->_model, but returns another handler
     *
     * @return resource
     */
    protected function _getTestFileHandler()
    {
        return fopen($this->_fullFileName, 'w');
    }

    /**
     * Get shared lock for specified file handler
     *
     * @param resource $fileHandler
     * @return bool
     */
    protected function _tryGetSharedLock($fileHandler)
    {
        return flock($fileHandler, LOCK_SH | LOCK_NB);
    }

    /**
     * Unlock specified file handler
     *
     * @param resource $fileHandler
     */
    protected function _unlock($fileHandler)
    {
        flock($fileHandler, LOCK_UN);
    }

    public function testProcessLockNoStream()
    {
        $this->assertFalse($this->_model->processLock());
    }

    /**
     * This test can't check non blocking lock case because its required two parallel test processes
     */
    public function testProcessLockSuccessfulLock()
    {
        $this->_openFile();
        $testFileHandler = $this->_getTestFileHandler();

        // can't take shared lock if file has exclusive lock
        $this->assertTrue($this->_model->processLock());
        $this->assertFalse($this->_tryGetSharedLock($testFileHandler), 'File must be locked');
        $this->assertAttributeSame(true, '_streamLocked', $this->_model);
        $this->assertAttributeSame(false, '_processLocked', $this->_model);

        $this->_model->processUnlock();
        fclose($testFileHandler);
    }

    public function testProcessFailedLock()
    {
        $this->_openFile();
        $testFileHandler = $this->_getTestFileHandler();

        // can't take exclusive lock if file has shared lock
        $this->assertTrue($this->_tryGetSharedLock($testFileHandler), 'File must not be locked');
        $this->assertFalse($this->_model->processLock());
        $this->assertAttributeSame(true, '_streamLocked', $this->_model);
        $this->assertAttributeSame(true, '_processLocked', $this->_model);

        $this->_unlock($testFileHandler);
        fclose($testFileHandler);
    }

    public function testProcessUnlock()
    {
        $this->_openFile();
        $this->_model->processLock();

        $this->assertTrue($this->_model->processUnlock());
        $this->assertAttributeSame(false, '_streamLocked', $this->_model);
        $this->assertAttributeSame(null, '_processLocked', $this->_model);
    }

    public function testIsProcessLockedNoStream()
    {
        $this->assertNull($this->_model->isProcessLocked());
    }

    public function testIsProcessLockedStoredFlag()
    {
        $this->_openFile();
        $this->_model->processLock();
        $this->assertFalse($this->_model->isProcessLocked());
        $this->_model->processUnlock();
    }

    public function testIsProcessLockedTrue()
    {
        $this->_openFile();
        $testFileHandler = $this->_getTestFileHandler();

        $this->assertTrue($this->_tryGetSharedLock($testFileHandler), 'File must not be locked');
        $this->assertTrue($this->_model->isProcessLocked());

        $this->_unlock($testFileHandler);
    }

    public function testIsProcessLockedFalseWithUnlock()
    {
        $this->_openFile();
        $testFileHandler = $this->_getTestFileHandler();

        $this->assertFalse($this->_model->isProcessLocked(true));
        $this->assertTrue($this->_tryGetSharedLock($testFileHandler), 'File must not be locked');
        $this->assertAttributeSame(false, '_streamLocked', $this->_model);

        $this->_unlock($testFileHandler);
    }

    public function testIsProcessLockedFalseWithoutUnlock()
    {
        $this->_openFile();
        $testFileHandler = $this->_getTestFileHandler();

        $this->assertFalse($this->_model->isProcessLocked());
        $this->assertFalse($this->_tryGetSharedLock($testFileHandler), 'File must be locked');
        $this->assertAttributeSame(true, '_streamLocked', $this->_model);

        $this->_model->processUnlock();
    }
}
