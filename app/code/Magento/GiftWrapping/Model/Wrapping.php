<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftWrapping\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\Framework\Exception\InputException;

/**
 * Gift Wrapping model
 *
 */
class Wrapping extends \Magento\Framework\Model\AbstractModel
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
     * Permitted extensions for wrapping image
     *
     * @var array
     */
    protected $_imageAllowedExtensions = ['jpg', 'jpeg', 'gif', 'png'];

    /**
     * Current store id
     *
     * @var int|null
     */
    protected $_store = null;

    /**
     * @var \Magento\Framework\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Store\Model\System\Store
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
     * @var \Magento\GiftWrapping\Model\Wrapping\Validator
     */
    protected $_validator;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Core\Model\File\UploaderFactory $uploaderFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Filesystem $filesystem
     * @param \Magento\GiftWrapping\Model\Wrapping\Validator $validator
     * @param \Magento\Framework\Model\Resource\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Core\Model\File\UploaderFactory $uploaderFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Framework\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Filesystem $filesystem,
        \Magento\GiftWrapping\Model\Wrapping\Validator $validator,
        \Magento\Framework\Model\Resource\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_storeManager = $storeManager;
        $this->_systemStore = $systemStore;
        $this->_mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA_DIR);
        $this->_uploaderFactory = $uploaderFactory;
        $this->_validator = $validator;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize model
     *
     * @return void
     */
    protected function _construct()
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
        if (!$this->hasData('website_ids') && $this->_storeManager->hasSingleStore()) {
            $this->setData('website_ids', array_keys($this->_systemStore->getWebsiteOptionHash()));
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
     * @return array|null
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
     * @return \Magento\Store\Model\Store
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
     * @return $this
     */
    public function setImage($value)
    {
        //in the current version should be used instance of \Magento\Core\Model\File\Uploader
        if ($value instanceof \Magento\Framework\File\Uploader) {
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
     * @return $this
     */
    public function attachUploadedImage($imageFieldName, $isTemporary = false)
    {
        $isUploaded = true;
        try {
            /** @var $uploader \Magento\Core\Model\File\Uploader */
            $uploader = $this->_uploaderFactory->create(array('fileId' => $imageFieldName));
            $uploader->setAllowedExtensions($this->_imageAllowedExtensions);
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
     * Set image through file contents and return new file name if succeed
     *
     * @param string $fileName
     * @param string $imageContent
     * @return bool|string
     * @throws InputException
     */
    public function attachBinaryImage($fileName, $imageContent)
    {
        if (empty($fileName) || empty($imageContent)) {
            return false;
        }
        $fileNameExtension = pathinfo($fileName, PATHINFO_EXTENSION);
        if (!in_array($fileNameExtension, $this->_imageAllowedExtensions)) {
            throw new InputException(
                'The image extension "%1" not allowed.',
                [$fileNameExtension]
            );
        }
        if (!preg_match('/^[^\\/?*:";<>()|{}\\\\]+$/', $fileName)) {
            throw new InputException('Provided image name contains forbidden characters.');
        }

        $imageProperties = @getimagesizefromstring($imageContent);
        if (empty($imageProperties)) {
            throw new InputException('The image content must be valid data.');
        }
        $sourceMimeType = $imageProperties['mime'];
        if (strpos($sourceMimeType, 'image/') !== 0) {
            throw new InputException('The image MIME type is not valid or not supported.');
        }

        $filePath = $this->_mediaDirectory->getAbsolutePath(self::IMAGE_PATH . $fileName);
        // avoid file names conflicts
        $newFileName = \Magento\Core\Model\File\Uploader::getNewFileName($filePath);
        $result = $this->_mediaDirectory->writeFile(self::IMAGE_TMP_PATH . $newFileName, $imageContent);
        if ($result) {
            $this->setTmpImage($fileName);
            return $newFileName;
        }
        return false;
    }

    /**
     * Set temporary wrapping image
     *
     * @param string|null|\Magento\Core\Model\File\Uploader $value
     * @return $this
     */
    public function setTmpImage($value)
    {
        //in the current version should be used instance of \Magento\Core\Model\File\Uploader
        if ($value instanceof \Magento\Framework\File\Uploader) {
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
     * @return $this
     */
    public function unsTmpImage()
    {
        if ($this->hasTmpImage()) {
            $tmpImagePath = self::IMAGE_TMP_PATH . $this->getTmpImage();
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
     * @return string|false
     */
    public function getImageUrl()
    {
        if ($this->getTmpImage()) {
            return $this->_storeManager->getStore()->getBaseUrl(
                \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
            ) . self::IMAGE_TMP_PATH . $this->getTmpImage();
        }
        if ($this->getImage()) {
            return $this->_storeManager->getStore()->getBaseUrl(
                \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
            ) . self::IMAGE_PATH . $this->getImage();
        }
        return false;
    }


    /**
     * {@inheritdoc}
     */
    protected function _getValidationRulesBeforeSave()
    {
        return $this->_validator;
    }
}
