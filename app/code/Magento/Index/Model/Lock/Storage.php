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
     * @var \Magento\Core\Model\Dir
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
     * @param \Magento\Core\Model\Dir $dirs
     * @param \Magento\Index\Model\Process\FileFactory $fileFactory
     */
    public function __construct(
        \Magento\Core\Model\Dir $dirs,
        \Magento\Index\Model\Process\FileFactory $fileFactory
    ) {
        $this->_dirs = $dirs;
        $this->_fileFactory   = $fileFactory;
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
            $file = $this->_fileFactory->create();
            $varDirectory = $this->_dirs->getDir(\Magento\Core\Model\Dir::VAR_DIR) . DIRECTORY_SEPARATOR . 'locks';
            $file->setAllowCreateFolders(true);

            $file->open(array('path' => $varDirectory));
            $fileName = 'index_process_' . $processId . '.lock';
            $file->streamOpen($fileName);
            $file->streamWrite(date('r'));
            $this->_fileHandlers[$processId] = $file;
        }
        return $this->_fileHandlers[$processId];
    }
}
