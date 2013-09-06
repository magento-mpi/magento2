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
 * Catalog product price block
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
 class Magento_Catalog_Block_Product_View_Price extends Magento_Core_Block_Template
 {
    public function getPrice()
    {
        $product = Mage::registry('product');
        /*if($product->isConfigurable()) {
            $price = $product->getCalculatedPrice((array)$this->getRequest()->getParam('super_attribute', array()));
            return Mage::app()->getStore()->formatPrice($price);
        }*/

        return $product->getFormatedPrice();
    }
 }
