<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Adminhtml_Block_Catalog_Product_Edit_Js extends Magento_Adminhtml_Block_Template
{
    /**
     * Get currently edited product
     *
     * @return Magento_Catalog_Model_Product
     */
    public function getProduct()
    {
        return Mage::registry('current_product');
    }

    /**
     * Get store object of curently edited product
     *
     * @return Magento_Core_Model_Store
     */
    public function getStore()
    {
        $product = $this->getProduct();
        if ($product) {
            return Mage::app()->getStore($product->getStoreId());
        }
        return Mage::app()->getStore();
    }
}
