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
 * Catalog category image attribute backend model
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Model_Category_Attribute_Backend_Image extends Magento_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * @var Magento_Core_Model_File_UploaderFactory
     */
    protected $_uploaderFactory;

    /**
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Core_Model_File_UploaderFactory $uploaderFactory
     */
    public function __construct(
        Magento_Core_Model_Logger $logger,
        Magento_Core_Model_File_UploaderFactory $uploaderFactory
    ) {
        parent::__construct($logger);
        $this->_uploaderFactory = $uploaderFactory;
    }

    /**
     * Save uploaded file and set its name to category
     *
     * @param Magento_Object $object
     * @return Magento_Catalog_Model_Category_Attribute_Backend_Image
     */
    public function afterSave($object)
    {
        $value = $object->getData($this->getAttribute()->getName() . '_additional_data');

        // if no image was set - nothing to do
        if (empty($value) && empty($_FILES)) {
            return $this;
        }

        if (is_array($value) && !empty($value['delete'])) {
            $object->setData($this->getAttribute()->getName(), '');
            $this->getAttribute()->getEntity()
                ->saveAttribute($object, $this->getAttribute()->getName());
            return $this;
        }

        $path = Mage::getBaseDir('media') . DS . 'catalog' . DS . 'category' . DS;

        try {
            /** @var $uploader Magento_Core_Model_File_Uploader */
            $uploader = $this->_uploaderFactory->create(array('fileId' => $this->getAttribute()->getName()));
            $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
            $uploader->setAllowRenameFiles(true);
            $result = $uploader->save($path);

            $object->setData($this->getAttribute()->getName(), $result['file']);
            $this->getAttribute()->getEntity()->saveAttribute($object, $this->getAttribute()->getName());
        } catch (Exception $e) {
            if ($e->getCode() != Magento_Core_Model_File_Uploader::TMP_NAME_EMPTY) {
                $this->_logger->logException($e);
            }
        }
        return $this;
    }
}
