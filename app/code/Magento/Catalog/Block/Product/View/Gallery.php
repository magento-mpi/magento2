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
namespace Magento\Catalog\Block\Product\View;

use Magento\Data\Collection;

class Gallery extends \Magento\Catalog\Block\Product\View\AbstractView
{
    /**
     * Retrieve list of gallery images
     *
     * @return array|Collection
     */
    public function getGalleryImages()
    {
        return $this->getProduct()->getMediaGalleryImages();
    }

    /**
     * Retrieve gallery url
     *
     * @param null|\Magento\Object $image
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
     * Get gallery image url
     *
     * @param \Magento\Object $image
     * @param string $type
     * @param boolean $whiteBorders
     * @param null|number $width
     * @param null|number $height
     * @return string
     */
    public function getImageUrl($image, $type, $whiteBorders = false, $width = null, $height = null)
    {
        $product = $this->getProduct();
        $img  = $this->_imageHelper->init($product, $type, $image->getFile());
        if ($whiteBorders) {
            $img->constrainOnly(TRUE)->keepAspectRatio(TRUE)->keepFrame(FALSE);
        }
        if ($width || $height) {
            $img->resize($width, $height);
        }
        return (string)$img;
    }

    /**
     * Is product main image
     *
     * @param \Magento\Object $image
     * @return bool
     */
    public function isMainImage($image)
    {
        $product = $this->getProduct();
        return $product->getImage() == $image->getFile();
    }
}
