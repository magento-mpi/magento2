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
 * Test class for \Magento\Index\Model\Process\File
 */
namespace Magento\Index\Model\Process;

class FileTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test lock name
     */
    const FILE_PATH = 'locks/index_test.lock';

    /**
     * @var \Magento\TestFramework\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var resource
     */
    protected $_testFileHandler;

    /**
     * @var \Magento\Index\Model\Process\File
     */
    protected $_model;

    /**
     * @var \Magento\Filesystem\Directory\WriteInterface
     */
    protected $_varDirectory;

    protected function setUp()
    {
        $this->_objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        $filesystem = $this->_objectManager->create('Magento\Filesystem');
        $this->_varDirectory = $filesystem->getDirectoryWrite(\Magento\Filesystem::VAR_DIR);

        $fullFileName = $this->_varDirectory->getAbsolutePath(self::FILE_PATH);
        $this->_testFileHandler = fopen($fullFileName, 'w');
    }

    protected function tearDown()
    {
        unset($this->_objectManager);
        unset($this->_model);
        unset($this->_varDirectory);
        fclose($this->_testFileHandler);
        unset($this->_testFileHandler);
    }

    /**
     * Open test file
     */
    protected function _openFile()
    {
        $this->_model = $this->_objectManager->create(
            'Magento\Index\Model\Process\File',
            array('streamHandler' => $this->_varDirectory->openFile(self::FILE_PATH, 'w+'))
        );
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

    /**
     * This test can't check non blocking lock case because its required two parallel test processes
     */
    public function testProcessLockSuccessfulLock()
    {
        $this->_openFile();

        // can't take shared lock if file has exclusive lock
        $this->_model->processLock();
        $this->assertFalse($this->_model->isProcessLocked());
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
        $this->_model->processLock();
        $this->assertTrue($this->_model->isProcessLocked());
        $this->assertAttributeSame(false, '_streamLocked', $this->_model);
        $this->assertAttributeSame(true, '_processLocked', $this->_model);

        $this->_unlock();
    }

    public function testProcessUnlock()
    {
        $this->_openFile();
        $this->_model->processLock();
        $this->_model->processUnlock();
        $this->assertFalse($this->_model->isProcessLocked());
        $this->assertAttributeSame(false, '_streamLocked', $this->_model);
        $this->assertAttributeSame(null, '_processLocked', $this->_model);
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

        $this->assertFalse($this->_model->isProcessLocked(false));
        $this->assertFalse($this->_tryGetSharedLock(), 'File must be locked');
        $this->assertAttributeSame(true, '_streamLocked', $this->_model);

        $this->_model->processUnlock();
    }
}
