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
 * Theme Image Uploader
 */
class Mage_Core_Model_Theme_Image_Uploader
{
    /**
     * Allowed file extensions to upload
     *
     * @var array
     */
    protected  $_allowedExtensions = array('jpg', 'jpeg', 'gif', 'png', 'xbm', 'wbmp');

    /**
     * @var Mage_Core_Helper_Data
     */
    protected $_helper;

    /**
     * @var Magento_Filesystem
     */
    protected $_filesystem;

    /**
     * @var Zend_File_Transfer_Adapter_Http
     */
    protected $_transferAdapter;

    /**
     * @var Magento_File_UploaderFactory
     */
    protected $_uploaderFactory;


    /**
     * Initialize dependencies
     *
     * @param Mage_Core_Helper_Data $helper
     * @param Magento_Filesystem $filesystem
     * @param Zend_File_Transfer_Adapter_Http $transferAdapter
     * @param Magento_File_UploaderFactory $uploaderFactory
     */
    public function __construct(
        Mage_Core_Helper_Data $helper,
        Magento_Filesystem $filesystem,
        Zend_File_Transfer_Adapter_Http $transferAdapter,
        Magento_File_UploaderFactory $uploaderFactory
    ) {
        $this->_helper = $helper;
        $this->_filesystem = $filesystem;
        $this->_transferAdapter = $transferAdapter;
        $this->_uploaderFactory = $uploaderFactory;
    }

    /**
     * Upload preview image
     *
     * @param string $scope the request key for file
     * @param string $destinationPath path to upload directory
     * @return bool
     * @throws Mage_Core_Exception
     */
    public function uploadPreviewImage($scope, $destinationPath)
    {
        if (!$this->_transferAdapter->isUploaded($scope)) {
            return false;
        }
        if (!$this->_transferAdapter->isValid($scope)) {
            throw new Mage_Core_Exception($this->_helper->__('Uploaded image is not valid'));
        }
        $upload = $this->_uploaderFactory->create(array('fileId' => $scope));
        $upload->setAllowCreateFolders(true);
        $upload->setAllowedExtensions($this->_allowedExtensions);
        $upload->setAllowRenameFiles(true);
        $upload->setFilesDispersion(false);

        if (!$upload->checkAllowedExtension($upload->getFileExtension())) {
            throw new Mage_Core_Exception($this->_helper->__('Invalid image file type.'));
        }
        if (!$upload->save($destinationPath)) {
            throw new Mage_Core_Exception($this->_helper->__('Image can not be saved.'));
        }
        return $destinationPath . DIRECTORY_SEPARATOR . $upload->getUploadedFileName();
    }
}
