<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog category image attribute backend model
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Category_Attribute_Backend_Image extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * @var Mage_Core_Model_Dir
     */
    protected $_dirs;

    /**
     * @var Magento_Filesystem
     */
    protected $_filesystem;

    /**
     * Initialize class instance state
     *
     * @param Mage_Core_Model_Dir $dirs
     * @param Magento_Filesystem $filesystem
     */
    public function __construct(Mage_Core_Model_Dir $dirs, Magento_Filesystem $filesystem)
    {
        $this->_dirs = $dirs;
        $this->_filesystem = $filesystem;
    }

    /**
     * Get category media directory path
     *
     * @return string
     */
    protected function _getMediaDir()
    {
        return $this->_dirs->getDir(Mage_Core_Model_Dir::MEDIA) . DS . 'catalog' . DS . 'category' . DS;
    }

    /**
     * Save uploaded file and set its name to category
     *
     * @param Varien_Object $object
     * @return Mage_Catalog_Model_Category_Attribute_Backend_Image
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
            $this->_filesystem->delete($this->_getMediaDir() . $value['value'], $this->_getMediaDir());
            return $this;
        }

        $path = $this->_getMediaDir();

        try {
            $uploader = new Mage_Core_Model_File_Uploader($this->getAttribute()->getName());
            $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
            $uploader->setAllowRenameFiles(true);
            $result = $uploader->save($path);

            $object->setData($this->getAttribute()->getName(), $result['file']);
            $this->getAttribute()->getEntity()->saveAttribute($object, $this->getAttribute()->getName());
        } catch (Exception $e) {
            if ($e->getCode() != Mage_Core_Model_File_Uploader::TMP_NAME_EMPTY) {
                Mage::logException($e);
            }
        }
        return $this;
    }

    /**
     * Delete file from filesystem after attribute deletion
     *
     * @param Varien_Object $object
     * @return Mage_Eav_Model_Entity_Attribute_Backend_Abstract
     */
    public function afterDelete($object)
    {
        $attrCode = $this->getAttribute()->getAttributeCode();
        $value = $object->getData($attrCode);
        if (!empty($value)) {
            $this->_filesystem->delete($this->_getMediaDir() . $value, $this->_getMediaDir());
        }
        return parent::afterDelete($object);
    }
}
