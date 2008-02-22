<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog product media gallery attribute backend model
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Ivan Chepurnyi <ivan.chepurnoy@varien.com>
 */
class Mage_Catalog_Model_Product_Attribute_Backend_Media extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * Load attribute data after product loaded
     *
     * @param Mage_Catalog_Model_Product $object
     */
    public function afterLoad($object)
    {
        $attrCode = $this->getAttribute()->getAttributeCode();
        $value = array();
        $value['images'] = array();
        $value['values'] = array();
        $valueIdToIndex = array();

        foreach ($this->_getResource()->loadGallery($object, $this) as $image) {
            $value['images'][] = $image;
            $valueIdToIndex[$image['value_id']] = count($value['images'])-1;
        }

        $imageTypeLabels = array();

        if (count($valueIdToIndex)>0) {
            $imagesByType = $this->_getResource()->loadGalleryImages(
                $object->getStoreId(),
                array_keys($valueIdToIndex)
            );

            foreach ($imagesByType as $imageByType) {
                $index = $valueIdToIndex[$imageByType['value_id']];
                $value['values'][$imageByType['type']] = &$value['images'][$index]['file'];
                $imageTypeLabels[$imageByType['type']] = &$value['images'][$index]['label'];
            }
        }

        $object->setData($attrCode, $value);
        foreach ($this->_getConfig()->getImageTypes() as $imageTypeId => $imageType) {
            if(isset($value['values'][$imageTypeId])) {
                $object->setData($imageType['attribute'], $value['values'][$imageTypeId]);
                if(!$object->hasData($imageType['attribute'] . '_label')) {
                    $object->setData($imageType['attribute'], $imageTypeLabels[$imageTypeId]);
                }
            }
        }
    }

    public function beforeSave($object)
    {

    }

    public function afterSave($object)
    {
        $attrCode = $this->getAttribute()->getAttributeCode();
        $value = $object->getData($attrCode);
        if (!is_array($value) || !isset($value['images']) || !isset($value['values'])) {
            return;
        }

        if(!is_array($value['images']) && strlen($value['images']) > 0) {
           $value['images'] = Zend_Json::decode($value['images']);
        }

        if(!is_array($value['values']) && strlen($value['values']) > 0) {
           $value['values'] = Zend_Json::decode($value['values']);
        }

        $toDelete = array();
        $filesToValueIds = array();
        foreach ($value['images'] as $image) {
            if(!empty($image['remove'])) {
                if(isset($image['value_id'])) {
                    $toDelete[] = $image['value_id'];
                }
                continue;
            }

            if(!isset($image['value_id'])) {
                $data = array();
                $data['entity_id']      = $object->getId();
                $data['entity_type_id'] = $object->getEntityTypeId();
                $data['attribute_id']   = $this->getAttribute()->getId();
                $data['value']          = $this->_moveImageFromTmp($image['file']);
                $image['value_id']      = $this->_getResource()->insertGallery($data);
            }

            $filesToValueIds[$image['file']] = $image['value_id'];

            $this->_getResource()->deleteGalleryValueInStore($image['value_id'], $object->getStoreId());

            // Add per store labels, position, disabled
            $data = array();
            $data['value_id'] = $image['value_id'];
            $data['label']    = $image['label'];
            $data['position'] = $image['position'];
            $data['disabled'] = $image['disabled'];
            $data['store_id'] = $object->getStoreId();

            $this->_getResource()->insertGalleryValueInStore($data);
        }

        $this->_getResource()->deleteGallery($toDelete);

        if (count($filesToValueIds) > 0) {
            $this->_getResource()->deleteGalleryImagesInStore(
                array_values($filesToValueIds),
                $object->getStoreId()
            );

            foreach ($this->_getConfig()->getImageTypes() as $imageTypeId => $imageType) {
                if (isset($value['values'][$imageTypeId])
                    && isset($filesToValueIds[$value['values'][$imageTypeId]])) {

                    $data = array();
                    $data['value_id'] = $filesToValueIds[$value['values'][$imageTypeId]];
                    $data['store_id'] = $object->getStoreId();
                    $data['type']     = $imageTypeId;

                    $this->_getResource()->insertGalleryImageInStore($data);
                }
            }
        }
    }

    /**
     * Retrieve resource model
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Attribute_Backend_Media
     */
    protected function _getResource()
    {
        return Mage::getResourceSingleton('catalog/product_attribute_backend_media');
    }

    /**
     * Retrive media config
     *
     * @return Mage_Catalog_Model_Product_Media_Config
     */
    protected function _getConfig()
    {
        return Mage::getSingleton('catalog/product_media_config');
    }

    /**
     * Move image from temporary directory to normal
     *
     * @param string $file
     * @return string
     */
    protected function _moveImageFromTmp($file)
    {
        $ioObject = new Varien_Io_File();
        $destDirectory = dirname($this->_getConfig()->getMediaPath($file));
        try {
            $ioObject->open(array('path'=>$destDirectory));
        } catch (Exception $e) {
            $ioObject->mkdir($destDirectory, 0777, true);
            $ioObject->open(array('path'=>$destDirectory));
        }

        $destFile = dirname($file) . $ioObject->dirsep()
                  . Varien_File_Uploader::getNewFileName($this->_getConfig()->getMediaPath($file));

        $ioObject->mv(
            $this->_getConfig()->getTmpMediaPath($file),
            $this->_getConfig()->getMediaPath($destFile)
        );

        return $destFile;
    }
} // Class Mage_Catalog_Model_Product_Attribute_Backend_Media End