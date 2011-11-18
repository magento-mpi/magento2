<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Adminhtml_Block_Catalog_Product_Edit_Js extends Mage_Adminhtml_Block_Template
{
    /**
     * Get currently edited product
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        return Mage::registry('current_product');
    }

    /**
     * Get store object of curently edited product
     *
     * @return Mage_Core_Model_Store
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
