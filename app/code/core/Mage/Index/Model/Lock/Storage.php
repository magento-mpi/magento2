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
 * Lock file storage for index processes
 */
class Mage_Index_Model_Lock_Storage
{
    /**
     * @var Mage_Core_Model_Config
     */
    protected $_configuration;

    /**
     * @var Mage_Index_Model_Process_FileFactory
     */
    protected $_fileFactory;

    /**
     * File handlers by process IDs
     *
     * @var array
     */
    protected $_fileHandlers = array();

    /**
     * @param Mage_Core_Model_Config $configuration
     * @param Mage_Index_Model_Process_FileFactory $fileFactory
     */
    public function __construct(
        Mage_Core_Model_Config $configuration,
        Mage_Index_Model_Process_FileFactory $fileFactory
    ) {
        $this->_configuration = $configuration;
        $this->_fileFactory   = $fileFactory;
    }

    /**
     * Get file handler by process ID
     *
     * @param $processId
     * @return Mage_Index_Model_Process_File
     */
    public function getFile($processId)
    {
        if (!isset($this->_fileHandlers[$processId])) {
            $file = $this->_fileFactory->createFromArray();
            $varDirectory = $this->_configuration->getVarDir('locks');
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