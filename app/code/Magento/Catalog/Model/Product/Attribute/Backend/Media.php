<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog product media gallery attribute backend model
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Model_Product_Attribute_Backend_Media extends Magento_Eav_Model_Entity_Attribute_Backend_Abstract
{
    protected $_renamedImages = array();

    /**
     * Resource model
     *
     * @var Magento_Catalog_Model_Resource_Product_Attribute_Backend_Media
     */
    protected $_resourceModel;

    /**
     * @var Magento_Catalog_Model_Product_Media_Config
     */
    protected $_mediaConfig;

    /**
     * @var Magento_Filesystem $filesystem
     */
    protected $_filesystem;

    /**
     * @var string
     */
    protected $_baseMediaPath;

    /**
     * @var string
     */
    protected $_baseTmpMediaPath;

    /**
     * Core data
     *
     * @var Magento_Core_Helper_Data
     */
    protected $_coreData = null;

    /**
     * Core file storage database
     *
     * @var Magento_Core_Helper_File_Storage_Database
     */
    protected $_fileStorageDb = null;

    /**
     * Core event manager proxy
     *
     * @var Magento_Core_Model_Event_Manager
     */
    protected $_eventManager = null;

    /**
     * Product factory
     *
     * @var Magento_Catalog_Model_Resource_ProductFactory
     */
    protected $_productFactory;

    /**
     * Construct
     *
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Catalog_Model_Resource_ProductFactory $productFactory
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Helper_File_Storage_Database $fileStorageDb
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Catalog_Model_Product_Media_Config $mediaConfig
     * @param Magento_Core_Model_Dir $dirs
     * @param Magento_Filesystem $filesystem
     * @param Magento_Catalog_Model_Resource_Product_Attribute_Backend_Media $resourceProductAttribute
     */
    public function __construct(
        Magento_Core_Model_Logger $logger,
        Magento_Catalog_Model_Resource_ProductFactory $productFactory,
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Helper_File_Storage_Database $fileStorageDb,
        Magento_Core_Helper_Data $coreData,
        Magento_Catalog_Model_Product_Media_Config $mediaConfig,
        Magento_Core_Model_Dir $dirs,
        Magento_Filesystem $filesystem,
        Magento_Catalog_Model_Resource_Product_Attribute_Backend_Media $resourceProductAttribute
    ) {
        $this->_productFactory = $productFactory;
        $this->_eventManager = $eventManager;
        $this->_fileStorageDb = $fileStorageDb;
        $this->_coreData = $coreData;
        $this->_resourceModel = $resourceProductAttribute;
        $this->_mediaConfig = $mediaConfig;
        $this->_filesystem = $filesystem;
        $this->_filesystem->setIsAllowCreateDirectories(true);
        $this->_filesystem->setWorkingDirectory($dirs->getDir(Magento_Core_Model_Dir::MEDIA));
        $this->_baseMediaPath = $this->_mediaConfig->getBaseMediaPath();
        $this->_baseTmpMediaPath = $this->_mediaConfig->getBaseTmpMediaPath();
        $this->_filesystem->ensureDirectoryExists($this->_baseMediaPath);
        $this->_filesystem->ensureDirectoryExists($this->_baseTmpMediaPath);

        parent::__construct($logger);
    }

    /**
     * Load attribute data after product loaded
     *
     * @param Magento_Catalog_Model_Product $object
     * @return Magento_Eav_Model_Entity_Attribute_Backend_Abstract
     */
    public function afterLoad($object)
    {
        $attrCode = $this->getAttribute()->getAttributeCode();
        $value = array();
        $value['images'] = array();
        $value['values'] = array();
        $localAttributes = array('label', 'position', 'disabled');

        foreach ($this->_getResource()->loadGallery($object, $this) as $image) {
            foreach ($localAttributes as $localAttribute) {
                if (is_null($image[$localAttribute])) {
                    $image[$localAttribute] = $this->_getDefaultValue($localAttribute, $image);
                }
            }
            $value['images'][] = $image;
        }

        $object->setData($attrCode, $value);
        return $this;
    }

    protected function _getDefaultValue($key, &$image)
    {
        if (isset($image[$key . '_default'])) {
            return $image[$key . '_default'];
        }

        return '';
    }

    /**
     * Validate media_gallery attribute data
     *
     * @param Magento_Catalog_Model_Product $object
     * @throws Magento_Core_Exception
     * @return bool
     */
    public function validate($object)
    {
        if ($this->getAttribute()->getIsRequired()) {
            $value = $object->getData($this->getAttribute()->getAttributeCode());
            if ($this->getAttribute()->isValueEmpty($value)) {
                if (!(is_array($value) && count($value) > 0)) {
                    return false;
                }
            }
        }
        if ($this->getAttribute()->getIsUnique()) {
            if (!$this->getAttribute()->getEntity()->checkAttributeUniqueValue($this->getAttribute(), $object)) {
                $label = $this->getAttribute()->getFrontend()->getLabel();
                throw new Magento_Core_Exception(
                    __('The value of attribute "%1" must be unique.', $label)
                );
            }
        }

        return true;
    }

    public function beforeSave($object)
    {
        $attrCode = $this->getAttribute()->getAttributeCode();
        $value = $object->getData($attrCode);
        if (!is_array($value) || !isset($value['images'])) {
            return;
        }

        if (!is_array($value['images']) && strlen($value['images']) > 0) {
            $value['images'] = $this->_coreData->jsonDecode($value['images']);
        }

        if (!is_array($value['images'])) {
            $value['images'] = array();
        }



        $clearImages = array();
        $newImages   = array();
        $existImages = array();
        if ($object->getIsDuplicate()!=true) {
            foreach ($value['images'] as &$image) {
                if (!empty($image['removed'])) {
                    $clearImages[] = $image['file'];
                } else if (empty($image['value_id'])) {
                    $newFile                   = $this->_moveImageFromTmp($image['file']);
                    $image['new_file'] = $newFile;
                    $newImages[$image['file']] = $image;
                    $this->_renamedImages[$image['file']] = $newFile;
                    $image['file']             = $newFile;
                } else {
                    $existImages[$image['file']] = $image;
                }
            }
        } else {
            // For duplicating we need copy original images.
            $duplicate = array();
            foreach ($value['images'] as &$image) {
                if (empty($image['value_id'])) {
                    continue;
                }
                $duplicate[$image['value_id']] = $this->_copyImage($image['file']);
                $newImages[$image['file']] = $duplicate[$image['value_id']];
            }

            $value['duplicate'] = $duplicate;
        }

        foreach ($object->getMediaAttributes() as $mediaAttribute) {
            $mediaAttrCode = $mediaAttribute->getAttributeCode();
            $attrData = $object->getData($mediaAttrCode);

            if (in_array($attrData, $clearImages)) {
                $object->setData($mediaAttrCode, 'no_selection');
            }

            if (in_array($attrData, array_keys($newImages))) {
                $object->setData($mediaAttrCode, $newImages[$attrData]['new_file']);
                $object->setData($mediaAttrCode.'_label', $newImages[$attrData]['label']);
            }

            if (in_array($attrData, array_keys($existImages))) {
                $object->setData($mediaAttrCode.'_label', $existImages[$attrData]['label']);
            }
        }

        $object->setData($attrCode, $value);

        return $this;
    }

    /**
     * Retrieve renamed image name
     *
     * @param string $file
     * @return string
     */
    public function getRenamedImage($file)
    {
        if (isset($this->_renamedImages[$file])) {
            return $this->_renamedImages[$file];
        }

        return $file;
    }

    public function afterSave($object)
    {
        if ($object->getIsDuplicate() == true) {
            $this->duplicate($object);
            return;
        }

        $attrCode = $this->getAttribute()->getAttributeCode();
        $value = $object->getData($attrCode);
        if (!is_array($value) || !isset($value['images']) || $object->isLockedAttribute($attrCode)) {
            return;
        }

        $storeId = $object->getStoreId();

        $storeIds = $object->getStoreIds();
        $storeIds[] = Magento_Core_Model_AppInterface::ADMIN_STORE_ID;

        // remove current storeId
        $storeIds = array_flip($storeIds);
        unset($storeIds[$storeId]);
        $storeIds = array_keys($storeIds);

        $images = $this->_productFactory->create()
            ->getAssignedImages($object, $storeIds);

        $picturesInOtherStores = array();
        foreach ($images as $image) {
            $picturesInOtherStores[$image['filepath']] = true;
        }

        $toDelete = array();
        $filesToValueIds = array();
        foreach ($value['images'] as &$image) {
            if (!empty($image['removed'])) {
                if (!empty($image['value_id']) && !isset($picturesInOtherStores[$image['file']])) {
                    $toDelete[] = $image['value_id'];
                }
                continue;
            }

            if (empty($image['value_id'])) {
                $data = array();
                $data['entity_id']      = $object->getId();
                $data['attribute_id']   = $this->getAttribute()->getId();
                $data['value']          = $image['file'];
                $image['value_id']      = $this->_getResource()->insertGallery($data);
            }

            $this->_getResource()->deleteGalleryValueInStore($image['value_id'], $object->getStoreId());

            // Add per store labels, position, disabled
            $data = array();
            $data['value_id'] = $image['value_id'];

            $data['label'] = isset($image['label']) ? $image['label'] : '';
            $data['position'] = isset($image['position']) ? (int)$image['position'] : 0;
            $data['disabled'] = isset($image['disabled']) ? (int)$image['disabled'] : 0;
            $data['store_id'] = (int) $object->getStoreId();

            $this->_getResource()->insertGalleryValueInStore($data);
        }

        $this->_getResource()->deleteGallery($toDelete);
    }

    /**
     * Add image to media gallery and return new filename
     *
     * @param Magento_Catalog_Model_Product $product
     * @param string                     $file              file path of image in file system
     * @param string|array               $mediaAttribute    code of attribute with type 'media_image',
     *                                                      leave blank if image should be only in gallery
     * @param boolean                    $move              if true, it will move source file
     * @param boolean                    $exclude           mark image as disabled in product page view
     * @return string
     */
    public function addImage(Magento_Catalog_Model_Product $product, $file,
        $mediaAttribute = null, $move = false, $exclude = true
    ) {
        if (!$this->_filesystem->isFile($file, $this->_baseTmpMediaPath)) {
            throw new Magento_Core_Exception(__('The image does not exist.'));
        }

        $pathinfo = pathinfo($file);
        $imgExtensions = array('jpg','jpeg','gif','png');
        if (!isset($pathinfo['extension']) || !in_array(strtolower($pathinfo['extension']), $imgExtensions)) {
            throw new Magento_Core_Exception(__('Please correct the image file type.'));
        }

        $fileName       = Magento_Core_Model_File_Uploader::getCorrectFileName($pathinfo['basename']);
        $dispretionPath = Magento_Core_Model_File_Uploader::getDispretionPath($fileName);
        $fileName       = $dispretionPath . DS . $fileName;

        $fileName = $this->_getNotDuplicatedFilename($fileName, $dispretionPath);

        $destinationFile = $this->_mediaConfig->getTmpMediaPath($fileName);

        try {
            /** @var $storageHelper Magento_Core_Helper_File_Storage_Database */
            $storageHelper = $this->_fileStorageDb;
            if ($move) {
                $this->_filesystem->rename($file, $destinationFile, $this->_baseTmpMediaPath);

                //If this is used, filesystem should be configured properly
                $storageHelper->saveFile($this->_mediaConfig->getTmpMediaShortUrl($fileName));
            } else {
                $this->_filesystem->copy($file, $destinationFile, $this->_baseTmpMediaPath);

                $storageHelper->saveFile($this->_mediaConfig->getTmpMediaShortUrl($fileName));
                $this->_filesystem->changePermissions($destinationFile, 0777, false, $this->_baseTmpMediaPath);
            }
        } catch (Exception $e) {
            throw new Magento_Core_Exception(
                __('We couldn\'t move this file: %1.', $e->getMessage())
            );
        }

        $fileName = str_replace(DS, '/', $fileName);

        $attrCode = $this->getAttribute()->getAttributeCode();
        $mediaGalleryData = $product->getData($attrCode);
        $position = 0;
        if (!is_array($mediaGalleryData)) {
            $mediaGalleryData = array(
                'images' => array()
            );
        }

        foreach ($mediaGalleryData['images'] as &$image) {
            if (isset($image['position']) && $image['position'] > $position) {
                $position = $image['position'];
            }
        }

        $position++;
        $mediaGalleryData['images'][] = array(
            'file'     => $fileName,
            'position' => $position,
            'label'    => '',
            'disabled' => (int) $exclude
        );

        $product->setData($attrCode, $mediaGalleryData);

        if (!is_null($mediaAttribute)) {
            $this->setMediaAttribute($product, $mediaAttribute, $fileName);
        }

        return $fileName;
    }

    /**
     * Add images with different media attributes.
     * Image will be added only once if the same image is used with different media attributes
     *
     * @param Magento_Catalog_Model_Product $product
     * @param array $fileAndAttributesArray array of arrays of filename and corresponding media attribute
     * @param string $filePath path, where image cand be found
     * @param boolean $move if true, it will move source file
     * @param boolean $exclude mark image as disabled in product page view
     * @return array array of parallel arrays with original and renamed files
     */
    public function addImagesWithDifferentMediaAttributes(Magento_Catalog_Model_Product $product,
        $fileAndAttributesArray, $filePath = '', $move = false, $exclude = true
    ) {
        $alreadyAddedFiles = array();
        $alreadyAddedFilesNames = array();

        foreach ($fileAndAttributesArray as $key => $value) {
            $keyInAddedFiles = array_search($value['file'], $alreadyAddedFiles, true);
            if ($keyInAddedFiles === false) {
                $savedFileName = $this->addImage($product, $filePath . $value['file'], null, $move, $exclude);
                $alreadyAddedFiles[$key] = $value['file'];
                $alreadyAddedFilesNames[$key] = $savedFileName;
            } else {
                $savedFileName = $alreadyAddedFilesNames[$keyInAddedFiles];
            }

            if (!is_null($value['mediaAttribute'])) {
                $this->setMediaAttribute($product, $value['mediaAttribute'], $savedFileName);
            }

        }

        return array('alreadyAddedFiles' => $alreadyAddedFiles, 'alreadyAddedFilesNames' => $alreadyAddedFilesNames);
    }

    /**
     * Update image in gallery
     *
     * @param Magento_Catalog_Model_Product $product
     * @param string $file
     * @param array $data
     * @return Magento_Catalog_Model_Product_Attribute_Backend_Media
     */
    public function updateImage(Magento_Catalog_Model_Product $product, $file, $data)
    {
        $fieldsMap = array(
            'label'    => 'label',
            'position' => 'position',
            'disabled' => 'disabled',
            'exclude'  => 'disabled'
        );

        $attrCode = $this->getAttribute()->getAttributeCode();

        $mediaGalleryData = $product->getData($attrCode);

        if (!isset($mediaGalleryData['images']) || !is_array($mediaGalleryData['images'])) {
            return $this;
        }

        foreach ($mediaGalleryData['images'] as &$image) {
            if ($image['file'] == $file) {
                foreach ($fieldsMap as $mappedField=>$realField) {
                    if (isset($data[$mappedField])) {
                        $image[$realField] = $data[$mappedField];
                    }
                }
            }
        }

        $product->setData($attrCode, $mediaGalleryData);
        return $this;
    }

    /**
     * Remove image from gallery
     *
     * @param Magento_Catalog_Model_Product $product
     * @param string $file
     * @return Magento_Catalog_Model_Product_Attribute_Backend_Media
     */
    public function removeImage(Magento_Catalog_Model_Product $product, $file)
    {
        $attrCode = $this->getAttribute()->getAttributeCode();

        $mediaGalleryData = $product->getData($attrCode);

        if (!isset($mediaGalleryData['images']) || !is_array($mediaGalleryData['images'])) {
            return $this;
        }

        foreach ($mediaGalleryData['images'] as &$image) {
            if ($image['file'] == $file) {
                $image['removed'] = 1;
            }
        }

        $product->setData($attrCode, $mediaGalleryData);

        return $this;
    }

    /**
     * Retrive image from gallery
     *
     * @param Magento_Catalog_Model_Product $product
     * @param string $file
     * @return array|boolean
     */
    public function getImage(Magento_Catalog_Model_Product $product, $file)
    {
        $attrCode = $this->getAttribute()->getAttributeCode();
        $mediaGalleryData = $product->getData($attrCode);
        if (!isset($mediaGalleryData['images']) || !is_array($mediaGalleryData['images'])) {
            return false;
        }

        foreach ($mediaGalleryData['images'] as $image) {
            if ($image['file'] == $file) {
                return $image;
            }
        }

        return false;
    }

    /**
     * Clear media attribute value
     *
     * @param Magento_Catalog_Model_Product $product
     * @param string|array $mediaAttribute
     * @return Magento_Catalog_Model_Product_Attribute_Backend_Media
     */
    public function clearMediaAttribute(Magento_Catalog_Model_Product $product, $mediaAttribute)
    {
        $mediaAttributeCodes = array_keys($product->getMediaAttributes());

        if (is_array($mediaAttribute)) {
            foreach ($mediaAttribute as $atttribute) {
                if (in_array($atttribute, $mediaAttributeCodes)) {
                    $product->setData($atttribute, null);
                }
            }
        } elseif (in_array($mediaAttribute, $mediaAttributeCodes)) {
            $product->setData($mediaAttribute, null);
        }

        return $this;
    }

    /**
     * Set media attribute value
     *
     * @param Magento_Catalog_Model_Product $product
     * @param string|array $mediaAttribute
     * @param string $value
     * @return Magento_Catalog_Model_Product_Attribute_Backend_Media
     */
    public function setMediaAttribute(Magento_Catalog_Model_Product $product, $mediaAttribute, $value)
    {
        $mediaAttributeCodes = array_keys($product->getMediaAttributes());

        if (is_array($mediaAttribute)) {
            foreach ($mediaAttribute as $atttribute) {
                if (in_array($atttribute, $mediaAttributeCodes)) {
                    $product->setData($atttribute, $value);
                }
            }
        } elseif (in_array($mediaAttribute, $mediaAttributeCodes)) {
            $product->setData($mediaAttribute, $value);
        }

        return $this;
    }

    /**
     * Retrieve resource model
     *
     * @return Magento_Catalog_Model_Resource_Product_Attribute_Backend_Media
     */
    protected function _getResource()
    {
        return $this->_resourceModel;
    }

    /**
     * Move image from temporary directory to normal
     *
     * @param string $file
     * @return string
     */
    protected function _moveImageFromTmp($file)
    {
        if (strrpos($file, '.tmp') == strlen($file)-4) {
            $file = substr($file, 0, strlen($file)-4);
        }
        $destinationFile = $this->_getUniqueFileName($file);

        /** @var $storageHelper Magento_Core_Helper_File_Storage_Database */
        $storageHelper = $this->_fileStorageDb;

        if ($storageHelper->checkDbUsage()) {
            $storageHelper->renameFile(
                $this->_mediaConfig->getTmpMediaShortUrl($file),
                $this->_mediaConfig->getMediaShortUrl($destinationFile)
            );

            $this->_filesystem->delete($this->_mediaConfig->getTmpMediaPath($file), $this->_baseTmpMediaPath);
            $this->_filesystem->delete($this->_mediaConfig->getMediaPath($destinationFile), $this->_baseMediaPath);
        } else {
            $this->_filesystem->rename(
                $this->_mediaConfig->getTmpMediaPath($file),
                $this->_mediaConfig->getMediaPath($destinationFile),
                $this->_baseTmpMediaPath,
                $this->_baseMediaPath
            );
        }

        return str_replace(DS, '/', $destinationFile);
    }

    /**
     * Check whether file to move exists. Getting unique name
     *
     * @param <type> $file
     * @return string
     */
    protected function _getUniqueFileName($file)
    {
        if ($this->_fileStorageDb->checkDbUsage()) {
            $destFile = $this->_fileStorageDb
                ->getUniqueFilename(
                    $this->_mediaConfig->getBaseMediaUrlAddition(),
                    $file
                );
        } else {
            $destFile = dirname($file) . DS
                . Magento_Core_Model_File_Uploader::getNewFileName($this->_mediaConfig->getMediaPath($file));
        }

        return $destFile;
    }

    /**
     * Copy image and return new filename.
     *
     * @param string $file
     * @return string
     */
    protected function _copyImage($file)
    {
        try {
            $destinationFile = $this->_getUniqueFileName($file);

            if (!$this->_filesystem->isFile($this->_mediaConfig->getMediaPath($file), $this->_baseMediaPath)) {
                throw new Exception();
            }

            if ($this->_fileStorageDb->checkDbUsage()) {
                $this->_fileStorageDb
                    ->copyFile($this->_mediaConfig->getMediaShortUrl($file),
                               $this->_mediaConfig->getMediaShortUrl($destinationFile));

                $this->_filesystem->delete($this->_mediaConfig->getMediaPath($destinationFile), $this->_baseMediaPath);
            } else {
                $this->_filesystem->copy(
                    $this->_mediaConfig->getMediaPath($file),
                    $this->_mediaConfig->getMediaPath($destinationFile),
                    $this->_baseMediaPath
                );
            }

            return str_replace(DS, '/', $destinationFile);
        } catch (Exception $e) {
            $file = $this->_mediaConfig->getMediaPath($file);
            throw new Magento_Core_Exception(
                __('We couldn\'t copy file %1. Please delete media with non-existing images and try again.', $file)
            );
        }
    }

    public function duplicate($object)
    {
        $attrCode = $this->getAttribute()->getAttributeCode();
        $mediaGalleryData = $object->getData($attrCode);

        if (!isset($mediaGalleryData['images']) || !is_array($mediaGalleryData['images'])) {
            return $this;
        }

        $this->_getResource()->duplicate(
            $this,
            (isset($mediaGalleryData['duplicate']) ? $mediaGalleryData['duplicate'] : array()),
            $object->getOriginalId(),
            $object->getId()
        );

        return $this;
    }

    /**
     * Get filename which is not duplicated with other files in media temporary and media directories
     *
     * @param String $fileName
     * @param String $dispretionPath
     * @return String
     */
    protected function _getNotDuplicatedFilename($fileName, $dispretionPath)
    {
        $fileMediaName = $dispretionPath . DS
                  . Magento_Core_Model_File_Uploader::getNewFileName($this->_mediaConfig->getMediaPath($fileName));
        $fileTmpMediaName = $dispretionPath . DS
                  . Magento_Core_Model_File_Uploader::getNewFileName($this->_mediaConfig->getTmpMediaPath($fileName));

        if ($fileMediaName != $fileTmpMediaName) {
            if ($fileMediaName != $fileName) {
                return $this->_getNotDuplicatedFileName($fileMediaName, $dispretionPath);
            } elseif ($fileTmpMediaName != $fileName) {
                return $this->_getNotDuplicatedFilename($fileTmpMediaName, $dispretionPath);
            }
        }

        return $fileMediaName;
    }

    /**
     * Retrieve data for update attribute
     *
     * @param  Magento_Catalog_Model_Product $object
     * @return array
     */
    public function getAffectedFields($object)
    {
        $data = array();
        $images = (array)$object->getData($this->getAttribute()->getName());
        $tableName = $this->_getResource()->getMainTable();
        foreach ($images['images'] as $value) {
            $data[$tableName][] = array(
                'attribute_id' => $this->getAttribute()->getAttributeId(),
                'value_id' => $value['value_id'],
                'entity_id' => $object->getId(),
            );
        }
        return $data;
    }

    /**
     * Attribute value is not to be saved in a conventional way, separate table is used to store the complex value
     *
     * {@inheritdoc}
     */
    public function isScalar()
    {
        return false;
    }
}
