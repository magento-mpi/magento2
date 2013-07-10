<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_Checkout_Block_Adminhtml_Sku_Errors_Grid_ColumnSet_Additional
    extends Mage_Backend_Block_Widget_Grid_ColumnSet
{
    /**
     * Retrieve row css class for specified item
     *
     * @param Varien_Object $item
     * @return string
     */
    public function getRowClass(Varien_Object $item)
    {
        if ($item->getCode() == Enterprise_Checkout_Helper_Data::ADD_ITEM_STATUS_FAILED_QTY_ALLOWED) {
            return 'qty-not-available';
        }
        return '';
    }
}
