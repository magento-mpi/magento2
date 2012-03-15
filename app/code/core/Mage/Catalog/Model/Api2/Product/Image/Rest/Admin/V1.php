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
 * API2 for product categories
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Api2_Product_Image_Rest_Admin_V1 extends Mage_Catalog_Model_Api2_Product_Image_Rest
{
    /**
     * Product category assign
     *
     * @param array $data
     * @return string
     */
    protected function _create(array $data)
    {
        /* @var $validator Mage_Catalog_Model_Api2_Product_Image_Validator_Image */
        $validator = Mage::getModel('catalog/api2_product_image_validator_image', array('resource' => $this));
        if (!$validator->isValidData($data)) {
            foreach ($validator->getErrors() as $error) {
                $this->_error($error, Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
            }
            $this->_critical(self::RESOURCE_DATA_PRE_VALIDATION_ERROR);
        }

        /* @var $product Mage_Catalog_Model_Product */
        $product = $this->_getProduct();

        /* @var $gallery Mage_Catalog_Model_Resource_Eav_Mysql4_Attribute */
        $gallery = $this->_getGalleryAttribute($product);

        $fileContent = @base64_decode($data['file_content'], true);
        if (!$fileContent) {
            $this->_critical('The image contents is not valid base64 data', Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        }
        unset($data['file_content']);

        $tmpDirectory = Mage::getBaseDir('var') . DS . 'api' . DS . Mage::getSingleton('api/session')->getSessionId();

        $fileName = $this->_getFileName($data);

        $ioAdapter = new Varien_Io_File();
        try {
            // Create temporary directory for api
            $ioAdapter->checkAndCreateFolder($tmpDirectory);
            $ioAdapter->open(array('path'=>$tmpDirectory));
            // Write image file
            $ioAdapter->write($fileName, $fileContent, 0666);
            unset($fileContent);

            // try to create Image object - it fails with Exception if image is not supported
            try {
                new Varien_Image($tmpDirectory . DS . $fileName);
            } catch (Exception $e) {
                // Remove temporary directory
                $ioAdapter->rmdir($tmpDirectory, true);
                $this->_critical($e->getMessage(), Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR);
            }

            // Adding image to gallery
            $file = $gallery->getBackend()->addImage(
                $product,
                $tmpDirectory . DS . $fileName,
                null,
                true
            );

            // Remove temporary directory
            $ioAdapter->rmdir($tmpDirectory, true);

            $gallery->getBackend()->updateImage($product, $file, $data);

            if (isset($data['types'])) {
                $gallery->getBackend()->setMediaAttribute($product, $data['types'], $file);
            }

            $product->save();
        } catch (Mage_Core_Exception $e) {
            $this->_critical($e->getMessage(), Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR);
        } catch (Exception $e) {
            $this->_critical(self::RESOURCE_UNKNOWN_ERROR);
        }

        return $this->_getImageLocation($this->_getCreatedImageId($product, $file));
    }

    /**
     * Get added image ID
     *
     * @param Mage_Catalog_Model_Product $product
     * @param string $file
     * @return int
     */
    protected function _getCreatedImageId($product, $file)
    {
        $imageId = null;

        /* @var $gallery Mage_Catalog_Model_Resource_Eav_Mysql4_Attribute */
        $gallery = $this->_getGalleryAttribute($product);

        $imageData = Mage::getResourceModel('catalog/product_attribute_backend_media')
            ->loadGallery($product, $gallery->getBackend());
        foreach ($imageData as $image) {
            if ($image['file'] == $file) {
                $imageId = $image['value_id'];
                break;
            }
        }
        if (!$imageId) {
            $this->_critical('Unknown error during image save', Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR);
        }
        return $imageId;
    }

    /**
     * Retrieve product images data
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
            if ($image['value_id'] == $imageId) {
                $result = $this->_imageToArray($image, $product);
                break;
            }
        }
        if (empty($result)) {
            $this->_critical('Product image not found', Mage_Api2_Model_Server::HTTP_NOT_FOUND);
        }

        return $result;
    }

    protected function _update(array $data)
    {
        $imageId = (int) $this->getRequest()->getParam('image');

        /* @var $product Mage_Catalog_Model_Product */
        $product = $this->_getProduct();

        $file = $this->_getImageFileById($product, $imageId);

        $gallery = $this->_getGalleryAttribute($product);
        $gallery->getBackend()->updateImage($product, $file, $data);

        if (isset($data['types']) && is_array($data['types'])) {
            $oldTypes = array();
            foreach ($product->getMediaAttributes() as $attribute) {
                if ($product->getData($attribute->getAttributeCode()) == $file) {
                     $oldTypes[] = $attribute->getAttributeCode();
                }
            }
            $clear = array_diff($oldTypes, $data['types']);
            if (count($clear) > 0) {
                $gallery->getBackend()->clearMediaAttribute($product, $clear);
            }

            $gallery->getBackend()->setMediaAttribute($product, $data['types'], $file);
        }

        try {
            $product->save();
        } catch (Mage_Core_Exception $e) {
            $this->_critical($e->getMessage(), Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR);
        } catch (Exception $e) {
            $this->_critical(self::RESOURCE_INTERNAL_ERROR);
        }

        return true;
    }

    /**
     * Product category unassign
     */
    protected function _delete()
    {
        $imageId = (int) $this->getRequest()->getParam('image');

        /* @var $product Mage_Catalog_Model_Product */
        $product = $this->_getProduct();

        $file = $this->_getImageFileById($product, $imageId);

        $gallery = $this->_getGalleryAttribute($product);
        $gallery->getBackend()->removeImage($product, $file);

        try {
            $product->save();
        } catch (Mage_Core_Exception $e) {
            $this->_critical($e->getMessage(), Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR);
        } catch (Exception $e) {
            $this->_critical(self::RESOURCE_INTERNAL_ERROR);
        }

        return true;
    }

    /**
     * Retrieve product images data
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
            $result[] = $this->_imageToArray($image, $product);
        }

        return $result;
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
            if (is_null($store)) {
                $store = Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID;
            }
            $store = Mage::app()->getStore($store);
        } catch (Mage_Core_Model_Store_Exception $e) {
            // store does not exist
            $this->_critical('Requested store is invalid', Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        }
        return $store;
    }

    /**
     * Get image resource location
     *
     * @param int $imageId
     * @return string URL
     */
    protected function _getImageLocation($imageId)
    {
        /* @var $apiTypeRoute Mage_Api2_Model_Route_ApiType */
        $apiTypeRoute = Mage::getModel('api2/route_apiType');

        $chain = $apiTypeRoute->chain(
            new Zend_Controller_Router_Route($this->getConfig()->getRouteWithEntityTypeAction($this->getResourceType()))
        );
        $params = array(
            'api_type' => $this->getRequest()->getApiType(),
            'id'       => $this->getRequest()->getParam('id'),
            'image'    => $imageId
        );
        $uri = $chain->assemble($params);

        return '/' . $uri;
    }

    /**
     * Check if product is available (for customer and guest)
     *
     * @param Mage_Catalog_Model_Product $product
     * @return bool
     */
    protected function _isProductAvailable($product)
    {
        return true;
    }
}
