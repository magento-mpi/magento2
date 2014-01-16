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
 * Lock file storage for index processes
 */
namespace Magento\Index\Model\Lock;

use Magento\Index\Model\Process\File;
use Magento\Index\Model\Process\FileFactory;
use Magento\Filesystem;
use Magento\Filesystem\Directory\WriteInterface;

class Storage
{
    /**
     * @var FileFactory
     */
    protected $_fileFactory;

    /**
     * File handlers by process IDs
     *
     * @var array
     */
    protected $_fileHandlers = array();

    /**
     * Directory instance
     *
     * @var WriteInterface
     */
    protected $_varDirectory;

    /**
     * @param FileFactory $fileFactory
     * @param Filesystem $filesystem
     */
    public function __construct(
        \Magento\Index\Model\Process\FileFactory $fileFactory,
        \Magento\Filesystem $filesystem
    ) {
        $this->_fileFactory   = $fileFactory;
        $this->_varDirectory = $filesystem->getDirectoryWrite(\Magento\Filesystem::VAR_DIR);
    }

    /**
     * Get file handler by process ID
     *
     * @param mixed $processId
     * @return File
     */
    public function getFile($processId)
    {
        if (!isset($this->_fileHandlers[$processId])) {
            $this->_varDirectory->create('locks');
            $fileName = 'locks/index_process_' . $processId . '.lock';
            $stream = $this->_varDirectory->openFile($fileName, 'w+');
            $stream->write(date('r'));
            $this->_fileHandlers[$processId] = $this->_fileFactory->create(array('streamHandler' => $stream));
        }
        return $this->_fileHandlers[$processId];
    }
}
