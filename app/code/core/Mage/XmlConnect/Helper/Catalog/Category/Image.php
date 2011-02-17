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
 * @package     Mage_XmlConnect
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog image helper
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Helper_Catalog_Category_Image extends Mage_Catalog_Helper_Image
{

    protected $_watermarkImageOpacity;

    /**
     * Init
     * 
     * @param Mage_Catalog_Model_Product $product
     * @param string $attributeName
     * @param string $imageFile
     * @return Mage_XmlConnect_Helper_Catalog_Category_Image
     *
     */
    public function init(Mage_Catalog_Model_Product $product, $attributeName, $imageFile = null)
    {
        return $this;
    }

    /**
     * Init image helper object
     *
     * @param Mage_Catalog_Model_Abstract $category
     * @param string $attributeName
     * @param string $imageFile
     * @return Mage_XmlConnect_Helper_Catalog_Category_Image
     */
    public function initialize(Mage_Catalog_Model_Abstract $category, $attributeName, $imageFile = null)
    {
        $this->_reset();
        $this->_setModel(Mage::getModel('xmlconnect/catalog_category_image'));
        $this->_getModel()->setDestinationSubdir($attributeName);
        $this->setProduct($category);

        $this->setWatermark(Mage::getStoreConfig("design/watermark/{$this->_getModel()->getDestinationSubdir()}_image"));
        $this->setWatermarkImageOpacity(Mage::getStoreConfig("design/watermark/{$this->_getModel()->getDestinationSubdir()}_imageOpacity"));
        $this->setWatermarkPosition(Mage::getStoreConfig("design/watermark/{$this->_getModel()->getDestinationSubdir()}_position"));
        $this->setWatermarkSize(Mage::getStoreConfig("design/watermark/{$this->_getModel()->getDestinationSubdir()}_size"));

        if ($imageFile) {
            $this->setImageFile($imageFile);
        } else {
            /*
             * add for work original size
             */
            $this->_getModel()->setBaseFile( $this->getProduct()->getData($this->_getModel()->getDestinationSubdir()) );
        }
        return $this;
    }

    /**
     * Return placeholder image file path
     * 
     * @return string
     */
    public function getPlaceholder()
    {
        if (!$this->_placeholder) {
            $attr = $this->_getModel()->getDestinationSubdir();
            $this->_placeholder = 'images/xmlconnect/catalog/category/placeholder/'.$attr.'.jpg';
        }
        return $this->_placeholder;
    }

    /**
     * Reset all previos data
     */
    protected function _reset()
    {
        parent::_reset();
        $this->_watermarkImageOpacity = null;
    }

    /**
     * Add watermark to image
     * size param in format 100x200
     *
     * @param string $fileName
     * @param string $position
     * @param string $size
     * @param int $imageOpacity
     * @return Mage_Catalog_Helper_Image
     */
    public function watermark($fileName, $position, $size=null, $imageOpacity=null)
    {
        parent::watermark($fileName, $position)->setWatermarkImageOpacity($imageOpacity);
        return $this;
    }

    /**
     * Set watermark file name
     *
     * @param string $watermark
     * @return Mage_Catalog_Helper_Image
     */
    protected function setWatermark($watermark)
    {
        $this->_watermark = $watermark;
        $this->_getModel()->setWatermarkFile($watermark);
        return $this;
    }

    /**
     * Set watermark position
     *
     * @param string $position
     * @return Mage_Catalog_Helper_Image
     */
    protected function setWatermarkPosition($position)
    {
        $this->_watermarkPosition = $position;
        $this->_getModel()->setWatermarkPosition($position);
        return $this;
    }

    /**
     * Set watermark size
     * param size in format 100x200
     *
     * @param string $size
     * @return Mage_Catalog_Helper_Image
     */
    public function setWatermarkSize($size)
    {
        $this->_watermarkSize = $size;
        $this->_getModel()->setWatermarkSize($this->parseSize($size));
        return $this;
    }

    /**
     * Set watermark image opacity
     *
     * @param int $imageOpacity
     * @return Mage_Catalog_Helper_Image
     */
    public function setWatermarkImageOpacity($imageOpacity)
    {
        $this->_watermarkImageOpacity = $imageOpacity;
        $this->_getModel()->setWatermarkImageOpacity($imageOpacity);
        return $this;
    }

    /**
     * Get watermark image opacity
     *
     * @return int
     */
    protected function getWatermarkImageOpacity()
    {
        if ( $this->_watermarkImageOpacity ) {
            return $this->_watermarkImageOpacity;
        }

        return $this->_getModel()->getWatermarkImageOpacity();
    }

    /**
     * Retrieve original image width
     *
     * @return int|null
     */
    public function getOriginalWidth()
    {
        return $this->_getModel()->getImageProcessor()->getOriginalWidth();
    }

    /**
     * Retrieve original image height
     *
     * @deprecated
     * @return int|null
     */
    public function getOriginalHeigh()
    {
        return $this->getOriginalHeight();
    }

    /**
     * Retrieve original image height
     *
     * @return int|null
     */
    public function getOriginalHeight()
    {
        return $this->_getModel()->getImageProcessor()->getOriginalHeight();
    }

    /**
     * Retrieve Original image size as array
     * 0 - width, 1 - height
     *
     * @return array
     */
    public function getOriginalSizeArray()
    {
        return array(
            $this->getOriginalWidth(),
            $this->getOriginalHeight()
        );
    }

    /**
     * Check - is this file an image
     *
     * @param string $filePath
     * @return bool
     * @throw Mage_Core_Exception
     */
    public function validateUploadFile($filePath)
    {
        if (!getimagesize($filePath)) {
            Mage::throwException(Mage::helper('xmlconnect')->__('Disallowed file type.'));
        }
        return true;
    }
}
