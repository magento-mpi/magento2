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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Abstract API2 class for product categories
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Catalog_Model_Api2_Product_Image_Rest extends Mage_Catalog_Model_Api2_Product_Image
{
    /**
     * Attribute code for media gallery
     */
    const ATTRIBUTE_CODE = 'media_gallery';

    /**
     * Allowed mime types for image
     *
     * @var array
     */
    protected $_mimeTypes = array(
        'image/jpeg' => 'jpg',
        'image/gif'  => 'gif',
        'image/png'  => 'png'
    );

    protected $_product;

    /**
     * Load product by its SKU or ID
     *
     * @return Mage_Catalog_Model_Product
     */
    protected function _getProduct()
    {
        if (is_null($this->_product)) {
            $productId = $this->getRequest()->getParam('id');
            /* @var $productHelper Mage_Catalog_Helper_Product */
            $productHelper = Mage::helper('catalog/product');
            $product = $productHelper->getProduct($productId, $this->_getStore()->getId());
            if (!($product->getId())) {
                $this->_critical(self::RESOURCE_NOT_FOUND);
            }
            // check if product belongs to website of current store
            if ($this->getRequest()->getParam('store')) {
                $isValidWebsite = in_array($this->_getStore()->getWebsiteId(), $product->getWebsiteIds());
                if (!$isValidWebsite) {
                    $this->_critical(self::RESOURCE_NOT_FOUND);
                }
            }
            if (!$this->_isProductAvailable($product)) {
                $this->_critical(self::RESOURCE_NOT_FOUND);
            }
            $this->_product = $product;
        }
        return $this->_product;
    }

    /**
     * Check if product is available (for customer and guest)
     *
     * @param Mage_Catalog_Model_Product $product
     * @return bool
     */
    protected function _isProductAvailable($product)
    {
        $isVisible = ($product->getVisibility() != Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE);
        $isEnabled = ($product->getStatus() == Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
        return $isVisible && $isEnabled;
    }

    /**
     * Check if store exist by its code or ID
     *
     * @return Mage_Core_Model_Store
     */
    protected function _getStore()
    {
        $store = $this->getRequest()->getParam('store');
        try {
            if (!$store) {
                $store = Mage::app()->getDefaultStoreView();
            } else {
                $store = Mage::app()->getStore($store);
            }
        } catch (Mage_Core_Model_Store_Exception $e) {
            // store does not exist
            $this->_critical('Requested store is invalid', Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        }
        return $store;
    }

    /**
     * Retrieve gallery attribute from product
     *
     * @param Mage_Catalog_Model_Product $product
     * @param Mage_Catalog_Model_Resource_Eav_Mysql4_Attribute|boolean
     */
    protected function _getGalleryAttribute($product)
    {
        $attributes = $product->getTypeInstance(true)->getSetAttributes($product);

        if (!isset($attributes[self::ATTRIBUTE_CODE])) {
            $this->_critical('Requested product doesn\'t support images', Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        }

        return $attributes[self::ATTRIBUTE_CODE];
    }

    /**
     * Converts image to api array data
     *
     * @param array $image
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */
    protected function _imageToArray(&$image, $product)
    {
        $result = array(
            'id'        => $image['value_id'],
            'label'     => $image['label'],
            'position'  => $image['position'],
            'exclude'   => $image['disabled'],
            'url'       => $this->_getMediaConfig()->getMediaUrl($image['file']),
            'types'     => array()
        );

        foreach ($product->getMediaAttributes() as $attribute) {
            if ($product->getData($attribute->getAttributeCode()) == $image['file']) {
                $result['types'][] = $attribute->getAttributeCode();
            }
        }

        return $result;
    }

    /**
     * Retrieve media config
     *
     * @return Mage_Catalog_Model_Product_Media_Config
     */
    protected function _getMediaConfig()
    {
        return Mage::getSingleton('catalog/product_media_config');
    }

    /**
     * Create file name from received data
     *
     * @param array $data
     * @return string
     */
    protected function _getFileName($data)
    {
        $fileName  = 'image';
        if (isset($data['file_name']) && $data['file_name']) {
            $fileName  = $data['file_name'];
        }
        $fileName .= '.' . $this->_getExtensionByMimeType($data['file_mime_type']);

        return $fileName;
    }

    /**
     * Get file uri by its id. File uri is used by media backend to identify image.
     *
     * @param Mage_Catalog_Model_Product $product
     * @param int $imageId
     * @return type
     */
    protected function _getImageFileById($product, $imageId)
    {
        $file = null;
        $mediaGalleryData = $product->getData('media_gallery');
        if (!isset($mediaGalleryData['images'])) {
            $this->_critical(self::RESOURCE_NOT_FOUND);
        }
        foreach ($mediaGalleryData['images'] as $image) {
            if ($image['value_id'] == $imageId) {
                $file = $image['file'];
                break;
            }
        }
        if (!$file) {
            $this->_critical(self::RESOURCE_NOT_FOUND);
        }

        $gallery = $this->_getGalleryAttribute($product);
        if (!$gallery->getBackend()->getImage($product, $file)) {
            $this->_critical(self::RESOURCE_NOT_FOUND);
        }

        return $file;
    }

    protected function _getExtensionByMimeType($mimeType)
    {
        if (!array_key_exists($mimeType, $this->_mimeTypes)) {
            $this->_critical('Unsuppoted image mime type', Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        }
        return $this->_mimeTypes[$mimeType];
    }

    /**
     * Retrieve product images data for customer and guest
     *
     * @return array
     */
    protected function _retrieve()
    {

        $imageId = (int) $this->getRequest()->getParam('image');

        /* @var $product Mage_Catalog_Model_Product */
        $product = $this->_getProduct();

        $galleryData = $product->getData(self::ATTRIBUTE_CODE);

        if (!isset($galleryData['images']) || !is_array($galleryData['images'])) {
            $this->_critical('Product image not found', Mage_Api2_Model_Server::HTTP_NOT_FOUND);
        }

        $result = array();

        foreach ($galleryData['images'] as &$image) {
            if ($image['value_id'] == $imageId && !$image['disabled']) {
                $result = $this->_imageToArray($image, $product);
                break;
            }
        }
        if (empty($result)) {
            $this->_critical('Product image not found', Mage_Api2_Model_Server::HTTP_NOT_FOUND);
        }

        return $result;
    }

    /**
     * Retrieve product images data for customer and guest
     *
     * @return array
     */
    protected function _retrieveCollection()
    {
        $return = array();

        /* @var $product Mage_Catalog_Model_Product */
        $product = $this->_getProduct();

        /* @var $gallery Mage_Catalog_Model_Resource_Eav_Mysql4_Attribute */
        $gallery = $this->_getGalleryAttribute($product);

        $galleryData = $product->getData(self::ATTRIBUTE_CODE);

        if (!isset($galleryData['images']) || !is_array($galleryData['images'])) {
            return array();
        }

        $result = array();

        foreach ($galleryData['images'] as &$image) {
            if (!$image['disabled']) {
                $result[] = $this->_imageToArray($image, $product);
            }
        }

        return $result;
    }
}
