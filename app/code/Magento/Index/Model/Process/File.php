<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Index
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Index\Model\Process;

use Magento\Filesystem\FilesystemException;
use Magento\Filesystem\StreamInterface;

/**
 * Process file entity
 */
class File
{
    /**
     * Stream handle instance
     *
     * @var StreamInterface
     */
    protected $_streamHandler;

    /**
     * Is stream locked
     *
     * @var bool
     */
    protected $_streamLocked;

    /**
     * Process lock flag:
     * - true  - if process is already locked by another user
     * - false - if process is locked by us
     * - null  - unknown lock status
     *
     * @var bool
     */
    protected $_processLocked = null;

    /**
     * @param StreamInterface $streamHandler
     */
    public function __construct(StreamInterface $streamHandler)
    {
        $this->_streamHandler = $streamHandler;
    }

    /**
     * Lock process file
     *
     * @param bool $nonBlocking
     * @return bool
     */
    public function processLock($nonBlocking = true)
    {
        $lock = LOCK_EX;
        if ($nonBlocking) {
            $lock = $lock | LOCK_NB;
        }
        try {
            $this->_streamHandler->addLock($lock);
            $this->_streamLocked  = true;
        } catch (FilesystemException $e) {
            $this->_streamLocked = false;
        }
        // true if process is locked by other user
        $this->_processLocked = !$this->_streamLocked;
    }

    /**
     * Unlock process file
     *
     * @return bool
     */
    public function processUnlock()
    {
        $this->_processLocked = null;
        try {
            $this->_streamHandler->unlock();
            $this->_streamLocked = false;
        } catch (FilesystemException $e) {
            $this->_streamLocked = true;
        }
        return !$this->_streamLocked;
    }

    /**
     * Check if process is locked by another user
     *
     * @param bool $needUnlock
     * @return bool|null
     */
    public function isProcessLocked($needUnlock = true)
    {
        if (!$this->_streamHandler) {
            return null;
        }

        if ($this->_processLocked !== null) {
            return $this->_processLocked;
        } else {
            try {
                $this->_streamHandler->addLock(LOCK_EX | LOCK_NB);
                if ($needUnlock) {
                    $this->_streamHandler->unlock();
                    $this->_streamLocked = false;
                } else {
                    $this->_streamLocked = true;
                }
                return false;
            } catch (FilesystemException $e) {
                return true;
            }
        }
    }
}
