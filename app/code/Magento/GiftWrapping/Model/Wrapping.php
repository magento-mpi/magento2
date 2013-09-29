<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftWrapping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Gift Wrapping model
 *
 */
class Magento_GiftWrapping_Model_Wrapping extends Magento_Core_Model_Abstract
{
    /**
     * Relative path to folder to store wrapping image to
     */
    const IMAGE_PATH = 'wrapping';

    /**
     * Relative path to folder to store temporary wrapping image to
     */
    const TMP_IMAGE_PATH = 'tmp/wrapping';

    /**
     * Current store id
     *
     * @var int|null
     */
    protected $_store = null;

    /**
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var Magento_Core_Model_System_Store
     */
    protected $_systemStore;

    /**
     * @var Magento_Core_Model_Dir
     */
    protected $_dir;

    /**
     * @var Magento_Core_Model_File_UploaderFactory
     */
    protected $_uploaderFactory;

    /**
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Model_System_Store $systemStore
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Model_Dir $dir
     * @param Magento_Core_Model_File_UploaderFactory $uploaderFactory
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Model_System_Store $systemStore,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Model_Dir $dir,
        Magento_Core_Model_File_UploaderFactory $uploaderFactory,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_storeManager = $storeManager;
        $this->_systemStore = $systemStore;
        $this->_dir = $dir;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->_uploaderFactory = $uploaderFactory;
    }

    /**
     * Intialize model
     *
     * @return void
     */
    protected function _construct ()
    {
        $this->_init('Magento_GiftWrapping_Model_Resource_Wrapping');
    }

    /**
     * Perform actions before object save.
     *
     * @return void
     */
    protected function _beforeSave()
    {
        if ($this->_storeManager->hasSingleStore()) {
            $this->setData('website_ids', array_keys(
                $this->_systemStore->getWebsiteOptionHash()));
        }
        if ($this->hasTmpImage()) {
            $baseImageName = $this->getTmpImage();
            $sourcePath = $this->_getTmpImageFolderAbsolutePath() . DS . $baseImageName;
            $destPath = $this->_getImageFolderAbsolutePath() . DS . $baseImageName;
            if (file_exists($sourcePath) && is_file($sourcePath)) {
                copy($sourcePath, $destPath);
                @unlink($sourcePath);
                $this->setData('image', $baseImageName);
            }
        }
        parent::_beforeSave();
    }

    /**
     * Perform actions after object save.
     *
     * @return void
     */
    protected function _afterSave()
    {
        $this->_getResource()->saveWrappingStoreData($this);
        $this->_getResource()->saveWrappingWebsiteData($this);
    }

    /**
     * Get wrapping associated website ids
     *
     * @return array
     */
    public function getWebsiteIds()
    {
        if (!$this->hasData('website_ids')) {
            $this->setData('website_ids', $this->_getResource()->getWebsiteIds($this->getId()));
        }
        return $this->_getData('website_ids');
    }

    /**
     * Set store id
     *
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId = null)
    {
        $this->_store = $this->_storeManager->getStore($storeId);
        return $this;
    }

    /**
     * Retrieve store
     *
     * @return Magento_Core_Model_Store
     */
    public function getStore()
    {
        if ($this->_store === null) {
            $this->setStoreId();
        }

        return $this->_store;
    }

    /**
     * Retrieve store id
     *
     * @return int
     */
    public function getStoreId()
    {
        return $this->getStore()->getId();
    }

    /**
     * Set wrapping image
     *
     * @param string|null|Magento_Core_Model_File_Uploader $value
     * @return Magento_GiftWrapping_Model_Wrapping
     */
    public function setImage($value)
    {
        //in the current version should be used instance of Magento_Core_Model_File_Uploader
        if ($value instanceof Magento_File_Uploader) {
            $value->save($this->_getImageFolderAbsolutePath());
            $value = $value->getUploadedFileName();
        }
        $this->setData('image', $value);
        return $this;
    }

    /**
     * Attach uploaded image to wrapping
     *
     * @param string $imageFieldName
     * @param bool $isTemporary
     * @return Magento_GiftWrapping_Model_Wrapping
     */
    public function attachUploadedImage($imageFieldName, $isTemporary = false)
    {
        $isUploaded = true;
        try {
            /** @var $uploader Magento_Core_Model_File_Uploader */
            $uploader = $this->_uploaderFactory->create(array('fileId' => $imageFieldName));
            $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
            $uploader->setAllowRenameFiles(true);
            $uploader->setAllowCreateFolders(true);
            $uploader->setFilesDispersion(false);
        } catch (Exception $e) {
            $isUploaded = false;
        }
        if ($isUploaded) {
            if ($isTemporary) {
                $this->setTmpImage($uploader);
            } else {
                $this->setImage($uploader);
            }
        }
        return $this;
    }

    /**
     * Set temporary wrapping image
     *
     * @param string|null|Magento_Core_Model_File_Uploader $value
     * @return Magento_GiftWrapping_Model_Wrapping
     */
    public function setTmpImage($value)
    {
        //in the current version should be used instance of Magento_Core_Model_File_Uploader
        if ($value instanceof Magento_File_Uploader) {
            // Delete previous temporary image if exists
            $this->unsTmpImage();
            $value->save($this->_getTmpImageFolderAbsolutePath());
            $value = $value->getUploadedFileName();
        }
        $this->setData('tmp_image', $value);
        // Override gift wrapping image name
        $this->setData('image', $value);
        return $this;
    }

    /**
     * Delete temporary wrapping image
     *
     * @return Magento_GiftWrapping_Model_Wrapping
     */
    public function unsTmpImage()
    {
        if ($this->hasTmpImage()) {
            $tmpImagePath =  $this->_getTmpImageFolderAbsolutePath() . DS . $this->getTmpImage();
            if (file_exists($tmpImagePath) && is_file($tmpImagePath)) {
                @unlink($tmpImagePath);
            }
            $this->unsetData('tmp_image');
        }
        return $this;
    }

    /**
     * Retrieve wrapping image url
     * Function returns url of a temporary wrapping image if it exists
     *
     * @see Magento_GiftWrapping_Block_Adminhtml_Giftwrapping_Helper_Image::_getUrl()
     *
     * @return string|boolean
     */
    public function getImageUrl()
    {
        if ($this->getTmpImage()) {
            return $this->_storeManager->getStore()
                ->getBaseUrl('media') . self::TMP_IMAGE_PATH . '/' . $this->getTmpImage();
        }
        if ($this->getImage()) {
            return $this->_storeManager->getStore()->getBaseUrl('media') . self::IMAGE_PATH . '/' . $this->getImage();
        }

        return false;
    }

    /**
     * Retrieve absolute path to folder to store wrapping image to
     *
     * @return string
     */
    protected function _getImageFolderAbsolutePath()
    {
        $path = $this->_dir->getDir('media') . DS . strtr(self::IMAGE_PATH, '/', DS);
        if (!is_dir($path)) {
            $ioAdapter = new Magento_Io_File();
            $ioAdapter->checkAndCreateFolder($path);
        }
        return $path;
    }

    /**
     * Retrieve absolute path to folder to store temporary wrapping image to
     *
     * @return string
     */
    protected function _getTmpImageFolderAbsolutePath()
    {
        return $this->_dir->getDir('media') . DS . strtr(self::TMP_IMAGE_PATH, '/', DS);
    }
}
