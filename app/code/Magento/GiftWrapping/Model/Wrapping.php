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
        if (Mage::app()->hasSingleStore()) {
            $this->setData('website_ids', array_keys(
                Mage::getSingleton('Magento_Core_Model_System_Store')->getWebsiteOptionHash()));
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
     * @return Magento_GiftWrapping_Model_Wrapping
     */
    public function setStoreId($storeId = null)
    {
        $this->_store = Mage::app()->getStore($storeId);
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
        if ($value instanceof \Magento\File\Uploader) {
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
            $uploader = new Magento_Core_Model_File_Uploader($imageFieldName);
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
        if ($value instanceof \Magento\File\Uploader) {
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
     * @see Magento_GiftWrapping_Block_Adminhtml_Giftwrapping_Helper_Image::__getUrl()
     *
     * @return string|boolean
     */
    public function getImageUrl()
    {
        if ($this->getTmpImage()) {
            return Mage::getBaseUrl('media') . self::TMP_IMAGE_PATH . '/' . $this->getTmpImage();
        }
        if ($this->getImage()) {
            return Mage::getBaseUrl('media') . self::IMAGE_PATH . '/' . $this->getImage();
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
        $path = Mage::getBaseDir('media') . DS . strtr(self::IMAGE_PATH, '/', DS);
        if (!is_dir($path)) {
            $ioAdapter = new \Magento\Io\File();
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
        return Mage::getBaseDir('media') . DS . strtr(self::TMP_IMAGE_PATH, '/', DS);
    }
}
