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

class Storage
{
    /**
     * @var \Magento\App\Dir
     */
    protected $_dirs;

    /**
     * @var \Magento\Index\Model\Process\FileFactory
     */
    protected $_fileFactory;

    /**
     * File handlers by process IDs
     *
     * @var array
     */
    protected $_fileHandlers = array();

    /**
     * Filesystem instance
     *
     * @var \Magento\Filesystem
     */
    protected $_filesystem;

    /**
     * @param \Magento\App\Dir $dirs
     * @param \Magento\Index\Model\Process\FileFactory $fileFactory
     * @param \Magento\Filesystem $filesystem
     */
    public function __construct(
        \Magento\App\Dir $dirs,
        \Magento\Index\Model\Process\FileFactory $fileFactory,
        \Magento\Filesystem $filesystem
    ) {
        $this->_dirs = $dirs;
        $this->_fileFactory   = $fileFactory;
        $this->_filesystem = $filesystem;
    }

    /**
     * Get file handler by process ID
     *
     * @param $processId
     * @return \Magento\Index\Model\Process\File
     */
    public function getFile($processId)
    {
        if (!isset($this->_fileHandlers[$processId])) {
            $varDirectory = $this->_dirs->getDir(\Magento\App\Dir::VAR_DIR) . '/locks';
            try {
                $this->_filesystem->setIsAllowCreateDirectories(true);
                $this->_filesystem->setWorkingDirectory(dirname($varDirectory));
                $this->_filesystem->ensureDirectoryExists($varDirectory);
                $this->_filesystem->setWorkingDirectory($varDirectory);
            } catch (\Magento\Filesystem\FilesystemException $e) {
                $this->_filesystem->setWorkingDirectory(getcwd());
            }

            $fileName = $varDirectory . '/index_process_' . $processId . '.lock';
            $stream = $this->_filesystem->createAndOpenStream($fileName, 'w+');
            $stream->write(date('r'));
            $this->_fileHandlers[$processId] = $this->_fileFactory->create(array('streamHandler' => $stream));;
        }
        return $this->_fileHandlers[$processId];
    }
}
