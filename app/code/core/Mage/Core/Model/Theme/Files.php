<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Theme files model class
 */
class Mage_Core_Model_Theme_Files extends Mage_Core_Model_Abstract
{
    /**
     * css file type
     */
    const TYPE_CSS = 'css';

    /**
     * @var Varien_Io_File
     */
    protected $_ioFile;

    /**
     * File path in file system
     *
     * @var string
     */
    protected $_filePath;

    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @param Mage_Core_Model_Event_Manager $eventDispatcher
     * @param Mage_Core_Model_Cache $cacheManager
     * @param Mage_Core_Model_Resource_Abstract $resource
     * @param Varien_Data_Collection_Db $resourceCollection
     * @param Varien_Io_File $ioFile
     * @param Magento_ObjectManager $objectManager
     * @param array $data
     */
    public function __construct(
        Mage_Core_Model_Event_Manager $eventDispatcher,
        Mage_Core_Model_Cache $cacheManager,
        Varien_Io_File $ioFile,
        Magento_ObjectManager $objectManager,
        Mage_Core_Model_Resource_Abstract $resource = null,
        Varien_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        parent::__construct($eventDispatcher, $cacheManager, $resource, $resourceCollection, $data);

        $this->_ioFile = $ioFile;
        $this->_objectManager = $objectManager;
    }

    /**
     * Theme files model initialization
     */
    protected function _construct()
    {
        $this->_init('Mage_Core_Model_Resource_Theme_Files');
    }

    /**
     * Create/update/delete file after save
     * Delete file if only file is empty
     *
     * @return Mage_Core_Model_Theme_Files
     */
    protected function _afterSave()
    {
        if ($this->getContent()) {
            $this->_saveFile();
        } else {
            $this->_deleteFile();
        }
        return parent::_afterSave();
    }

    /**
     * Delete file form file system after delete form db
     *
     * @return Mage_Core_Model_Theme_Files
     */
    protected function _afterDelete()
    {
        $this->_deleteFile();

        return parent::_afterDelete();
    }

    /**
     * Create/update file in file system
     *
     * @return bool|int
     */
    protected function _saveFile()
    {
        return $this->_ioFile->write($this->getFilePath(), $this->getContent());
    }

    /**
     * Delete file form file system
     *
     * @return bool
     */
    protected function _deleteFile()
    {
        return $this->_ioFile->rm($this->getFilePath());
    }

    /**
     * Return file path in file system
     *
     * @return string
     */
    public function getFilePath()
    {
        if ($this->getId() && !$this->_filePath) {
            $this->_filePath = $this->_getDirectory() . DIRECTORY_SEPARATOR . $this->getFileName();
        }
        return $this->_filePath;
    }

    /**
     * Return directory path
     *
     * @return string
     */
    protected function _getDirectory()
    {
        /** @var $design Mage_Core_Model_Design_Package */
        $design = $this->_objectManager->get('Mage_Core_Model_Design_Package');

        $directory = implode(DIRECTORY_SEPARATOR, array(
            $design->getPublicDir(),
            Mage_Core_Model_Design_Package::PUBLIC_CUSTOMIZATION_THEME_DIR,
            $this->getThemeId()
        ));

        $this->_ioFile->checkAndCreateFolder($directory);
        return $directory;
    }
}
