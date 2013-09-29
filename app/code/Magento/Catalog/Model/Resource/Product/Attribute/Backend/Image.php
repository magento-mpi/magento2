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
 * Product image attribute backend
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Model_Resource_Product_Attribute_Backend_Image
    extends Magento_Eav_Model_Entity_Attribute_Backend_Abstract
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
     * After save
     *
     * @param Magento_Object $object
     * @return Magento_Catalog_Model_Resource_Product_Attribute_Backend_Image
     */
    public function afterSave($object)
    {
        $value = $object->getData($this->getAttribute()->getName());

        if (is_array($value) && !empty($value['delete'])) {
            $object->setData($this->getAttribute()->getName(), '');
            $this->getAttribute()->getEntity()
                ->saveAttribute($object, $this->getAttribute()->getName());
            return;
        }

        try {
            /** @var $uploader Magento_Core_Model_File_Uploader */
            $uploader = $this->_uploaderFactory->create(array('fileId' => $this->getAttribute()->getName()));
            $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(true);
        } catch (Exception $e){
            return $this;
        }
        $uploader->save(Mage::getBaseDir('media') . '/catalog/product');

        $fileName = $uploader->getUploadedFileName();
        if ($fileName) {
            $object->setData($this->getAttribute()->getName(), $fileName);
            $this->getAttribute()->getEntity()
                 ->saveAttribute($object, $this->getAttribute()->getName());
        }
        return $this;
    }
}
