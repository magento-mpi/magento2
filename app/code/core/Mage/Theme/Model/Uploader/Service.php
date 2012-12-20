<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Theme
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Theme file uploader service
 */
class Mage_Theme_Model_Uploader_Service extends Mage_Core_Model_Abstract
{
    /**
     * Uploaded file path
     *
     * @var string|null
     */
    protected $_filePath;

    /**
     * File system helper
     *
     * @var Varien_Io_File
     */
    protected $_fileIo;

    /**
     * File uploader
     *
     * @var Mage_Core_Model_File_Uploader
     */
    protected $_uploader;

    /**
     * @param Mage_Core_Model_Event_Manager $eventDispatcher
     * @param Mage_Core_Model_Cache $cacheManager
     * @param array $data
     * @param Mage_Core_Model_Resource_Abstract $resource
     * @param Varien_Data_Collection_Db $resourceCollection
     * @param Varien_Io_File $fileIo
     */
    public function __construct(
        Mage_Core_Model_Event_Manager $eventDispatcher,
        Mage_Core_Model_Cache $cacheManager,
        Varien_Io_File $fileIo,
        Mage_Core_Model_Resource_Abstract $resource = null,
        Varien_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_fileIo = $fileIo;
        parent::__construct($eventDispatcher, $cacheManager, $resource, $resourceCollection, $data);
    }

    /**
     * Get destination directory
     *
     * @return string
     */
    protected function _getDestinationDir()
    {
        return Mage::getBaseDir('media') . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR . 'theme';
    }

    /**
     * Upload css file
     *
     * @param string $type
     * @return Mage_Theme_Model_Service
     */
    public function uploadCssFile($type)
    {
        $fileUploader = Mage::getObjectManager()->get('Mage_Core_Model_File_Uploader', array($type));
        $fileUploader->setAllowedExtensions(array('css'));
        $fileUploader->setAllowRenameFiles(true);
        $fileUploader->setAllowCreateFolders(true);

        $destinationDir = $this->_getDestinationDir();

        $fileUploader->save($destinationDir);
        $this->setFilePath($destinationDir . DIRECTORY_SEPARATOR . $fileUploader->getUploadedFileName());
        return $this;
    }

    /**
     * Get uploaded file content
     *
     * @return string
     */
    public function getFileContent()
    {
        return $this->_fileIo->read($this->getFilePath());
    }

    /**
     * Remove temporary data
     *
     * @return bool
     */
    public function removeTemporaryData()
    {
        return $this->_fileIo->rmdirRecursive($this->_getDestinationDir());
    }
}
