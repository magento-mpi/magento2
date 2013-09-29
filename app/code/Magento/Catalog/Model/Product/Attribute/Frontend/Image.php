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
 * Product image attribute frontend
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Model\Product\Attribute\Frontend;

class Image extends \Magento\Eav\Model\Entity\Attribute\Frontend\AbstractFrontend
{
    /**
     * Returns url to product image
     *
     * @param  \Magento\Catalog\Model\Product $product
     * @return string|false
     */
    public function getUrl($product)
    {
        $image = $product->getData($this->getAttribute()->getAttributeCode());
        if ($image) {
            $url = \Mage::app()->getStore($product->getStore())->getBaseUrl('media') . 'catalog/product/' . $image;
        } else {
            $url = false;
        }
        return $url;
    }
}
