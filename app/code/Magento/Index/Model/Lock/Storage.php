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
class Magento_Index_Model_Lock_Storage
{
    /**
     * @var Magento_Core_Model_Dir
     */
    protected $_dirs;

    /**
     * @var Magento_Index_Model_Process_FileFactory
     */
    protected $_fileFactory;

    /**
     * File handlers by process IDs
     *
     * @var array
     */
    protected $_fileHandlers = array();

    /**
     * @param Magento_Core_Model_Dir $dirs
     * @param Magento_Index_Model_Process_FileFactory $fileFactory
     */
    public function __construct(
        Magento_Core_Model_Dir $dirs,
        Magento_Index_Model_Process_FileFactory $fileFactory
    ) {
        $this->_dirs = $dirs;
        $this->_fileFactory   = $fileFactory;
    }

    /**
     * Get file handler by process ID
     *
     * @param $processId
     * @return Magento_Index_Model_Process_File
     */
    public function getFile($processId)
    {
        if (!isset($this->_fileHandlers[$processId])) {
            $file = $this->_fileFactory->create();
            $varDirectory = $this->_dirs->getDir(Magento_Core_Model_Dir::VAR_DIR) . DIRECTORY_SEPARATOR . 'locks';
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
