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
namespace Magento\GiftWrapping\Model;

use Magento\Filesystem\Directory\WriteInterface;

class Wrapping extends \Magento\Core\Model\AbstractModel
{
    /**
     * Relative path to folder to store wrapping image to
     */
    const IMAGE_PATH = 'wrapping/';

    /**
     * Relative path to folder to store temporary wrapping image to
     */
    const IMAGE_TMP_PATH = 'tmp/wrapping/';

    /**
     * Current store id
     *
     * @var int|null
     */
    protected $_store = null;

    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Core\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @var WriteInterface
     */
    protected $_mediaDirectory;

    /**
     * @var \Magento\Core\Model\File\UploaderFactory
     */
    protected $_uploaderFactory;

    /**
     * @param \Magento\Core\Model\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Core\Model\File\UploaderFactory $uploaderFactory
     * @param \Magento\Core\Model\System\Store $systemStore
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\App\Filesystem $filesystem
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Core\Model\File\UploaderFactory $uploaderFactory,
        \Magento\Core\Model\System\Store $systemStore,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\App\Filesystem $filesystem,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_storeManager = $storeManager;
        $this->_systemStore = $systemStore;
        $this->_mediaDirectory = $filesystem->getDirectoryWrite(\Magento\App\Filesystem::MEDIA_DIR);
        $this->_uploaderFactory = $uploaderFactory;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Intialize model
     *
     * @return void
     */
    protected function _construct ()
    {
        $this->_init('Magento\GiftWrapping\Model\Resource\Wrapping');
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
            $sourcePath = self::IMAGE_TMP_PATH . $baseImageName;
            $destPath = self::IMAGE_PATH . $baseImageName;
            if ($this->_mediaDirectory->isFile($sourcePath)) {
                $this->_mediaDirectory->renameFile($sourcePath, $destPath);
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
     * @return \Magento\Core\Model\Store
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
     * @param string|null|\Magento\Core\Model\File\Uploader $value
     * @return \Magento\GiftWrapping\Model\Wrapping
     */
    public function setImage($value)
    {
        //in the current version should be used instance of \Magento\Core\Model\File\Uploader
        if ($value instanceof \Magento\File\Uploader) {
            $value->save($this->_mediaDirectory->getAbsolutePath(self::IMAGE_PATH));
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
     * @return \Magento\GiftWrapping\Model\Wrapping
     */
    public function attachUploadedImage($imageFieldName, $isTemporary = false)
    {
        $isUploaded = true;
        try {
            /** @var $uploader \Magento\Core\Model\File\Uploader */
            $uploader = $this->_uploaderFactory->create(array('fileId' => $imageFieldName));
            $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
            $uploader->setAllowRenameFiles(true);
            $uploader->setAllowCreateFolders(true);
            $uploader->setFilesDispersion(false);
        } catch (\Exception $e) {
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
     * @param string|null|\Magento\Core\Model\File\Uploader $value
     * @return \Magento\GiftWrapping\Model\Wrapping
     */
    public function setTmpImage($value)
    {
        //in the current version should be used instance of \Magento\Core\Model\File\Uploader
        if ($value instanceof \Magento\File\Uploader) {
            // Delete previous temporary image if exists
            $this->unsTmpImage();
            $value->save($this->_mediaDirectory->getAbsolutePath(self::IMAGE_TMP_PATH));
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
     * @return \Magento\GiftWrapping\Model\Wrapping
     */
    public function unsTmpImage()
    {
        if ($this->hasTmpImage()) {
            $tmpImagePath =  self::IMAGE_TMP_PATH . $this->getTmpImage();
            if ($this->_mediaDirectory->isExist($tmpImagePath)) {
                $this->_mediaDirectory->delete($tmpImagePath);
            }
            $this->unsetData('tmp_image');
        }
        return $this;
    }

    /**
     * Retrieve wrapping image url
     * Function returns url of a temporary wrapping image if it exists
     *
     * @see \Magento\GiftWrapping\Block\Adminhtml\Giftwrapping\Helper\Image::_getUrl()
     *
     * @return string|boolean
     */
    public function getImageUrl()
    {
        if ($this->getTmpImage()) {
            return $this->_storeManager->getStore()->getBaseUrl('media') . self::IMAGE_TMP_PATH . $this->getTmpImage();
        }
        if ($this->getImage()) {
            return $this->_storeManager->getStore()->getBaseUrl('media') . self::IMAGE_PATH . $this->getImage();
        }
        return false;
    }
}
