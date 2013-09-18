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

class Gallery extends \Magento\Catalog\Block\Product\View\AbstractView
{
    /**
     * Retrieve list of gallery images
     *
     * @return array|\Magento\Data\Collection
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
}
