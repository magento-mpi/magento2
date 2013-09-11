<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Adminhtml\Block\Catalog\Product\Edit;

class Js extends \Magento\Adminhtml\Block\Template
{
    /**
     * Get currently edited product
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        return \Mage::registry('current_product');
    }

    /**
     * Get store object of curently edited product
     *
     * @return \Magento\Core\Model\Store
     */
    public function getStore()
    {
        $product = $this->getProduct();
        if ($product) {
            return \Mage::app()->getStore($product->getStoreId());
        }
        return \Mage::app()->getStore();
    }
}
