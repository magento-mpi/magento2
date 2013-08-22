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
class Magento_Catalog_Model_Product_Attribute_Frontend_Image extends Magento_Eav_Model_Entity_Attribute_Frontend_Abstract
{
    /**
     * Returns url to product image
     *
     * @param  Magento_Catalog_Model_Product $product
     * @return string|false
     */
    public function getUrl($product)
    {
        $image = $product->getData($this->getAttribute()->getAttributeCode());
        if ($image) {
            $url = Mage::app()->getStore($product->getStore())->getBaseUrl('media') . 'catalog/product/' . $image;
        } else {
            $url = false;
        }
        return $url;
    }
}
