<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Index
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Magento_Index_Model_Process_File
 */
class Magento_Index_Model_Process_FileTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test lock name
     */
    const FILE_NAME = 'index_test.lock';

    /**
     * @var Magento_TestFramework_ObjectManager
     */
    protected $_objectManager;

    /**
     * @var string
     */
    protected $_fileDirectory;

    /**
     * @var resource
     */
    protected $_testFileHandler;

    /**
     * @var Magento_Index_Model_Process_File
     */
    protected $_model;

    protected function setUp()
    {
        $this->_objectManager   = Mage::getObjectManager();
        $this->_model           = $this->_objectManager->create('Magento_Index_Model_Process_File');
        /** @var $dir Magento_Core_Model_Dir */
        $dir = $this->_objectManager->get('Magento_Core_Model_Dir');
        $this->_fileDirectory   = $dir->getDir(Magento_Core_Model_Dir::VAR_DIR) . DIRECTORY_SEPARATOR . 'locks';
        $fullFileName           = $this->_fileDirectory . DIRECTORY_SEPARATOR . self::FILE_NAME;
        $this->_testFileHandler = fopen($fullFileName, 'w');
    }

    protected function tearDown()
    {
        unset($this->_objectManager);
        unset($this->_model);
        unset($this->_fileDirectory);
        fclose($this->_testFileHandler);
        unset($this->_testFileHandler);
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
     * Get shared lock for test file handler
     *
     * @return bool
     */
    protected function _tryGetSharedLock()
    {
        return flock($this->_testFileHandler, LOCK_SH | LOCK_NB);
    }

    /**
     * Unlock test file handler
     */
    protected function _unlock()
    {
        flock($this->_testFileHandler, LOCK_UN);
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

        // can't take shared lock if file has exclusive lock
        $this->assertTrue($this->_model->processLock());
        $this->assertFalse($this->_tryGetSharedLock(), 'File must be locked');
        $this->assertAttributeSame(true, '_streamLocked', $this->_model);
        $this->assertAttributeSame(false, '_processLocked', $this->_model);

        $this->_model->processUnlock();
    }

    public function testProcessFailedLock()
    {
        $this->_openFile();

        // can't take exclusive lock if file has shared lock
        $this->assertTrue($this->_tryGetSharedLock(), 'File must not be locked');
        $this->assertFalse($this->_model->processLock());
        $this->assertAttributeSame(true, '_streamLocked', $this->_model);
        $this->assertAttributeSame(true, '_processLocked', $this->_model);

        $this->_unlock();
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

        $this->assertTrue($this->_tryGetSharedLock(), 'File must not be locked');
        $this->assertTrue($this->_model->isProcessLocked());

        $this->_unlock();
    }

    public function testIsProcessLockedFalseWithUnlock()
    {
        $this->_openFile();

        $this->assertFalse($this->_model->isProcessLocked(true));
        $this->assertTrue($this->_tryGetSharedLock(), 'File must not be locked');
        $this->assertAttributeSame(false, '_streamLocked', $this->_model);

        $this->_unlock();
    }

    public function testIsProcessLockedFalseWithoutUnlock()
    {
        $this->_openFile();

        $this->assertFalse($this->_model->isProcessLocked());
        $this->assertFalse($this->_tryGetSharedLock(), 'File must be locked');
        $this->assertAttributeSame(true, '_streamLocked', $this->_model);

        $this->_model->processUnlock();
    }
}
