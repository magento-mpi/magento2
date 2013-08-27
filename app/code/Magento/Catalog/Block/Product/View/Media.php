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
 * Simple product data view
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Block_Product_View_Media extends Magento_Catalog_Block_Product_View_Abstract
{
    /**
     * Flag, that defines whether gallery is disabled
     *
     * @var boolean
     */
    protected $_isGalleryDisabled;

    /**
     * Retrieve list of gallery images
     *
     * @return array|Magento_Data_Collection
     */
    public function getGalleryImages()
    {
        if ($this->_isGalleryDisabled) {
            return array();
        }
        $collection = $this->getProduct()->getMediaGalleryImages();
        return $collection;
    }

    /**
     * Retrieve gallery url
     *
     * @param null|Magento_Object $image
     * @return string
     */
    public function getGalleryUrl($image = null)
    {
        $params = array('id' => $this->getProduct()->getId());
        if ($image) {
            $params['image'] = $image->getValueId();
        }
        return $this->getUrl('catalog/product/gallery', $params);
    }

    /**
     * Disable gallery
     */
    public function disableGallery()
    {
        $this->_isGalleryDisabled = true;
    }
}
