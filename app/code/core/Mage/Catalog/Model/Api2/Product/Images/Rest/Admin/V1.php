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
 * API2 for product Images
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Api2_Products_Images_Rest_Admin_V1 extends Mage_Catalog_Model_Api2_Products_Images_Rest
{
    /**
     * Product category assign
     *
     * @param array $data
     * @return string
     */
    protected function _create(array $data)
    {
        $required = array('file_content', 'file_mime_type');
        $notEmpty = array('file_content', 'file_mime_type');
        $this->_validate($data, $required, $notEmpty);

        $productId = (int) $this->getRequest()->getParam('id');
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        /* @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('catalog/product')->setStoreId($storeId)->load($productId);
        if (!$product->getId()) {
            $this->_critical('Product not found', Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        }

        /* @var $gallery Mage_Catalog_Model_Resource_Eav_Mysql4_Attribute */
        $gallery = $this->_getGalleryAttribute($product);

        if (!isset($data['file_content']) || !isset($data['file_mime_type'])) {
            $this->_critical('The image is not specified', Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        }

        $fileContent = @base64_decode($data['file_content'], true);
        if (!$fileContent) {
            $this->_critical('The image contents is not valid base64 data', Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        }
        unset($data['file_content']);

        $tmpDirectory = Mage::getBaseDir('var') . DS . 'api' . DS . Mage::getSingleton('api/session')->getSessionId();

        if (isset($data['file_name']) && $data['file_name']) {
            $fileName  = $data['file_name'];
        } else {
            $fileName  = 'image';
        }
        $fileName .= '.' . $this->_getExtensionByMimeType($data['file_mime_type']);

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

        return true;
//        return $gallery->getBackend()->getRenamedImage($file);
    }

    /**
     * Retrieve product images data
     *
     * @return array
     */
    protected function _retrieve()
    {
        $return = array();

        $productId = $this->getRequest()->getParam('id');
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        /* @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('catalog/product')->setStoreId($storeId)->load($productId);
        if (!$product->getId()) {
            $this->_critical('Product not found', Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        }

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
}
