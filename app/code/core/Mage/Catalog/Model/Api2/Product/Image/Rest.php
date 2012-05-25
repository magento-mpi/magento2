<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Abstract API2 class for product images resource
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Catalog_Model_Api2_Product_Image_Rest extends Mage_Catalog_Model_Api2_Product_Rest
{
    /**
     * Attribute code for media gallery
     */
    const GALLERY_ATTRIBUTE_CODE = 'media_gallery';

    /**
     * Retrieve product image data for customer and guest roles
     *
     * @throws Mage_Api2_Exception
     * @return array
     */
    protected function _retrieve()
    {
        $imageData = array();
        $imageId = (int)$this->getRequest()->getParam('image');
        $galleryData = $this->_getProduct()->getData(self::GALLERY_ATTRIBUTE_CODE);

        if (!isset($galleryData['images']) || !is_array($galleryData['images'])) {
            $this->_critical(self::RESOURCE_NOT_FOUND);
        }
        foreach ($galleryData['images'] as $image) {
            if ($image['value_id'] == $imageId && !$image['disabled']) {
                $imageData = $this->_formatImageData($image);
                break;
            }
        }
        if (empty($imageData)) {
            $this->_critical(self::RESOURCE_NOT_FOUND);
        }
        return $imageData;
    }

    /**
     * Retrieve product images data for customer and guest
     *
     * @return array
     */
    protected function _retrieveCollection()
    {
        $images = array();
        $galleryData = $this->_getProduct()->getData(self::GALLERY_ATTRIBUTE_CODE);
        if (isset($galleryData['images']) && is_array($galleryData['images'])) {
            foreach ($galleryData['images'] as $image) {
                if (!$image['disabled']) {
                    $images[] = $this->_formatImageData($image);
                }
            }
        }
        return $images;
    }

    /**
     * Retrieve media gallery
     *
     * @throws Mage_Api2_Exception
     * @return Mage_Catalog_Model_Product_Attribute_Backend_Media
     */
    protected function _getMediaGallery()
    {
        $attributes = $this->_getProduct()->getTypeInstance()->getSetAttributes($this->_getProduct());

        if (!isset($attributes[self::GALLERY_ATTRIBUTE_CODE])
            || !$attributes[self::GALLERY_ATTRIBUTE_CODE] instanceof Mage_Eav_Model_Entity_Attribute_Abstract
        ) {
            $this->_critical('Requested product does not support images', Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        }
        $galleryAttribute = $attributes[self::GALLERY_ATTRIBUTE_CODE];
        /** @var $mediaGallery Mage_Catalog_Model_Product_Attribute_Backend_Media */
        $mediaGallery = $galleryAttribute->getBackend();
        return $mediaGallery;
    }

    /**
     * Create image data representation for API
     *
     * @param array $image
     * @return array
     */
    protected function _formatImageData($image)
    {
        $result = array(
            'id'        => $image['value_id'],
            'label'     => $image['label'],
            'position'  => $image['position'],
            'exclude'   => $image['disabled'],
            'url'       => $this->_getMediaConfig()->getMediaUrl($image['file']),
            'types'     => $this->_getImageTypesAssignedToProduct($image['file'])
        );
        return $result;
    }

    /**
     * Retrieve image types assigned to product (base, small, thumbnail)
     *
     * @param string $imageFile
     * @return array
     */
    protected function _getImageTypesAssignedToProduct($imageFile)
    {
        $types = array();
        foreach ($this->_getProduct()->getMediaAttributes() as $attribute) {
            if ($this->_getProduct()->getData($attribute->getAttributeCode()) == $imageFile) {
                $types[] = $attribute->getAttributeCode();
            }
        }
        return $types;
    }

    /**
     * Retrieve media config
     *
     * @return Mage_Catalog_Model_Product_Media_Config
     */
    protected function _getMediaConfig()
    {
        return Mage::getSingleton('Mage_Catalog_Model_Product_Media_Config');
    }

    /**
     * Get file URI by its id. File URI is used by media backend to identify image
     *
     * @throws Mage_Api2_Exception
     * @param int $imageId
     * @return string
     */
    protected function _getImageFileById($imageId)
    {
        $file = null;
        $mediaGalleryData = $this->_getProduct()->getData('media_gallery');
        if (!isset($mediaGalleryData['images'])) {
            $this->_critical(self::RESOURCE_NOT_FOUND);
        }
        foreach ($mediaGalleryData['images'] as $image) {
            if ($image['value_id'] == $imageId) {
                $file = $image['file'];
                break;
            }
        }
        if (!($file && $this->_getMediaGallery()->getImage($this->_getProduct(), $file))) {
            $this->_critical(self::RESOURCE_NOT_FOUND);
        }
        return $file;
    }
}
