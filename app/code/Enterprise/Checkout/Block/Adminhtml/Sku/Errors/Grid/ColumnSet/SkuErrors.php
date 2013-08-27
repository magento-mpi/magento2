<?php
/**
 * Sku Errors Column Set
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Enterprise_Checkout_Block_Adminhtml_Sku_Errors_Grid_ColumnSet_SkuErrors
    extends Magento_Backend_Block_Widget_Grid_ColumnSet
{
    /**
     * Retrieve row css class for specified item
     *
     * @param Magento_Object $item
     * @return string
     */
    public function getRowClass(Magento_Object $item)
    {
        if ($item->getCode() == Enterprise_Checkout_Helper_Data::ADD_ITEM_STATUS_FAILED_QTY_ALLOWED) {
            return 'qty-not-available';
        }
        return '';
    }
}
