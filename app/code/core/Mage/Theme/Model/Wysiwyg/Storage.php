<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Theme
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Theme_Model_Wysiwyg_Storage extends Mage_Core_Model_Abstract
{
    /**
     * Type font
     */
    const TYPE_FONT = 'font';

    /**
     * Type image
     */
    const TYPE_IMAGE = 'image';

    /**
     * File system model
     *
     * @var Varien_Io_File
     */
    protected $_fileIo;

    /**
     * Storage helper
     *
     * @var Mage_Theme_Helper_Storage
     */
    protected $_helper;

    /**
     * @param Mage_Core_Model_Event_Manager $eventDispatcher
     * @param Mage_Core_Model_Cache $cacheManager
     * @param Varien_Io_File $fileIo
     * @param Mage_Theme_Helper_Storage $helper,
     * @param Mage_Core_Model_Resource_Abstract $resource
     * @param Varien_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Mage_Core_Model_Event_Manager $eventDispatcher,
        Mage_Core_Model_Cache $cacheManager,
        Varien_Io_File $fileIo,
        Mage_Theme_Helper_Storage $helper,
        Mage_Core_Model_Resource_Abstract $resource = null,
        Varien_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_fileIo = $fileIo;
        $this->_helper = $helper;
        parent::__construct($eventDispatcher, $cacheManager, $resource, $resourceCollection, $data);
    }

    /**
     * Upload file
     *
     * @param string $targetPath
     * @return bool
     */
    public function uploadFile($targetPath)
    {
        $uploader = new Mage_Core_Model_File_Uploader('file');
        $uploader->setAllowedExtensions($this->_helper->getAllowedExtensionsByType());
        $uploader->setAllowRenameFiles(true);
        $uploader->setFilesDispersion(false);
        $result = $uploader->save($targetPath);

        if (!$result) {
            Mage::throwException( Mage::helper('Mage_Cms_Helper_Data')->__('Cannot upload file.') );
        }

        $result['cookie'] = array(
            'name'     => session_name(),
            'value'    => $this->_helper->getSession()->getSessionId(),
            'lifetime' => $this->_helper->getSession()->getCookieLifetime(),
            'path'     => $this->_helper->getSession()->getCookiePath(),
            'domain'   => $this->_helper->getSession()->getCookieDomain()
        );

        return $result;
    }

    /**
     * Create folder
     *
     * @param string $name
     * @param string $path
     * @return array
     */
    public function createFolder($name, $path)
    {
        if (!preg_match(Mage_Cms_Model_Wysiwyg_Images_Storage::DIRECTORY_NAME_REGEXP, $name)) {
            Mage::throwException($this->_helper->__('Invalid folder name.'));
        }
        if (!$this->_fileIo->isWriteable($path)) {
            $path = $this->_helper->getStorageRoot();
        }

        $newPath = $path . DS . $name;

        if ($this->_fileIo->fileExists($newPath)) {
            Mage::throwException($this->_helper->__('A directory with the same name already exists.'));
        }

        if (!$this->_fileIo->checkAndCreateFolder($newPath)) {
            Mage::throwException(Mage::helper('Mage_Cms_Helper_Data')->__('Cannot create new directory.'));
        }

        $result = array(
            'name'       => $name,
            'short_name' => $this->_helper->getShortFilename($name),
            'path'       => $newPath,
            'id'         => $this->_helper->convertPathToId($newPath)
        );

        return $result;
    }

    /**
     * Delete file
     *
     * @param string $file
     * @return Mage_Theme_Model_Wysiwyg_Storage
     */
    public function deleteFile($file)
    {
        $file = $this->_helper->idDecode($file);
        $path = $this->_helper->getCurrentPath();

        $_filePath = realpath($path . DS . $file);
        if (strpos($_filePath, realpath($path)) === 0 &&
            strpos($_filePath, realpath($this->_helper->getStorageRoot())) === 0
        ) {
            $this->_fileIo->rm($_filePath);
        }
        return $this;
    }

    /**
     * Get directory collection
     *
     * @param string $path
     * @return array
     */
    public function getDirsCollection($path)
    {
        if (!$this->_fileIo->fileExists($path, false)) {
            Mage::throwException($this->_helper->__('A directory with the same name not exists.'));
        }
        return $this->_fileIo->getDirectoriesList($path);
    }

    /**
     * Get files collection
     *
     * @return array
     */
    public function getFilesCollection()
    {
        $this->_fileIo->cd($this->_helper->getCurrentPath());
        $files = $this->_fileIo->ls(Varien_Io_File::GREP_FILES);
        foreach ($files as &$file) {
            $file['id'] = $this->_helper->idEncode($file['text']);
        }
        return $files;
    }

    /**
     * Get directories tree array
     *
     * @return array
     */
    public function getTreeArray()
    {
        $directories = $this->getDirsCollection($this->_helper->getCurrentPath());
        $resultArray = array();
        foreach ($directories as $path) {
            $item = $this->_fileIo->getPathInfo($path);
            $resultArray[] = array(
                'text'  => $this->_helper->getShortFilename($item['basename'], 20),
                'id'    => $this->_helper->convertPathToId($path),
                'cls'   => 'folder'
            );
        }
        return $resultArray;
    }

    /**
     * Delete directory
     *
     * @param string $path
     * @return bool
     */
    public function deleteDirectory($path)
    {
        $rootCmp = rtrim($this->_helper->getStorageRoot(), DIRECTORY_SEPARATOR);
        $pathCmp = rtrim($path, DIRECTORY_SEPARATOR);

        if ($rootCmp == $pathCmp) {
            Mage::throwException(Mage::helper('Mage_Cms_Helper_Data')->__('Cannot delete root directory %s.', $path));
        }

        return $this->_fileIo->rmdirRecursive($path);
    }
}
