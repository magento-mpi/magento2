<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Quick style file uploader
 */
class Magento_DesignEditor_Model_Editor_Tools_QuickStyles_ImageUploader extends Magento_Object
{
    /**
     * Quick style images path prefix
     */
    const PATH_PREFIX_QUICK_STYLE = 'quick_style_images';

    /**
     * Storage path
     *
     * @var string
     */
    protected $_storagePath;

    /**
     * @var Magento_Filesystem
     */
    protected $_filesystem;

    /**
     * @var Magento_Core_Model_File_UploaderFactory
     */
    protected $_uploaderFactory;

    /**
     * Allowed extensions
     *
     * @var array
     */
    protected $_allowedExtensions = array('jpg', 'jpeg', 'gif', 'png');


    /**
     * Generic constructor of change instance
     *
     * @param Magento_Core_Model_File_UploaderFactory $uploaderFactory
     * @param Magento_Filesystem $filesystem
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_File_UploaderFactory $uploaderFactory,
        Magento_Filesystem $filesystem,
        array $data = array()
    ) {
        $this->_uploaderFactory = $uploaderFactory;
        $this->_filesystem = $filesystem;
        parent::__construct($data);
    }

    /**
     * Get storage folder
     *
     * @return string
     */
    public function getStoragePath()
    {
        if (null === $this->_storagePath) {
            $this->_storagePath = implode(Magento_Filesystem::DIRECTORY_SEPARATOR, array(
                Magento_Filesystem::fixSeparator($this->_getTheme()->getCustomization()->getCustomizationPath()),
                self::PATH_PREFIX_QUICK_STYLE,
            ));
        }
        return $this->_storagePath;
    }

    /**
     * Set storage path
     *
     * @param string $path
     * @return Magento_DesignEditor_Model_Editor_Tools_QuickStyles_ImageUploader
     */
    public function setStoragePath($path)
    {
        $this->_storagePath = $path;
        return $this;
    }

    /**
     * Get theme
     *
     * @return Magento_Core_Model_Theme
     * @throws InvalidArgumentException
     */
    protected function _getTheme()
    {
        /** @var $theme Magento_Core_Model_Theme */
        $theme = $this->getTheme();
        if (!$theme->getId()) {
            throw new InvalidArgumentException('Theme was not found.');
        }
        return $theme;
    }

    /**
     * Upload image file
     *
     * @param string $key
     * @return array
     */
    public function uploadFile($key)
    {
        $result = array();
        /** @var $uploader Magento_Core_Model_File_Uploader */
        $uploader = $this->_uploaderFactory->create(array('fileId' => $key));
        $uploader->setAllowedExtensions($this->_allowedExtensions);
        $uploader->setAllowRenameFiles(true);
        $uploader->setAllowCreateFolders(true);

        if (!$uploader->save($this->getStoragePath())) {
            /** @todo add translator */
            Mage::throwException('Cannot upload file.');
        }
        $result['css_path'] = implode(
            '/', array('..', self::PATH_PREFIX_QUICK_STYLE, $uploader->getUploadedFileName())
        );
        $result['name'] = $uploader->getUploadedFileName();
        return $result;
    }

    /**
     * Remove file
     *
     * @param string $file
     * @return Magento_DesignEditor_Model_Editor_Tools_QuickStyles_ImageUploader
     */
    public function removeFile($file)
    {
        $path = $this->getStoragePath();
        $filePath = $this->_filesystem->normalizePath($path . '/' . $file);

        if ($this->_filesystem->isPathInDirectory($filePath, $path)
            && $this->_filesystem->isPathInDirectory($filePath, $this->getStoragePath())
        ) {
            $this->_filesystem->delete($filePath);
        }

        return $this;
    }
}
