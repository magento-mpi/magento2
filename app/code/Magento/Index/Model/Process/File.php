<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Index
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Process file entity
 */
namespace Magento\Index\Model\Process;

class File extends \Magento\Io\File
{
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
     * Lock process file
     *
     * @param bool $nonBlocking
     * @return bool
     */
    public function processLock($nonBlocking = true)
    {
        if (!$this->_streamHandler) {
            return false;
        }
        $this->_streamLocked = true;
        $lock = LOCK_EX;
        if ($nonBlocking) {
            $lock = $lock | LOCK_NB;
        }
        $result = flock($this->_streamHandler, $lock);
        // true if process is locked by other user
        $this->_processLocked = !$result;
        return $result;
    }

    /**
     * Unlock process file
     *
     * @return bool
     */
    public function processUnlock()
    {
        $this->_processLocked = null;
        return parent::streamUnlock();
    }

    /**
     * Check if process is locked by another user
     *
     * @param bool $needUnlock
     * @return bool|null
     */
    public function isProcessLocked($needUnlock = false)
    {
        if (!$this->_streamHandler) {
            return null;
        }

        if ($this->_processLocked !== null) {
            return $this->_processLocked;
        } else {
            if (flock($this->_streamHandler, LOCK_EX | LOCK_NB)) {
                if ($needUnlock) {
                    flock($this->_streamHandler, LOCK_UN);
                } else {
                    $this->_streamLocked = true;
                }
                return false;
            }
            return true;
        }
    }
}
