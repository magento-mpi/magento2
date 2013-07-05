<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 *
 * @category    Mage
 * @package     Mage_Core
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Mage_Backend_Block_Widget_Grid_ColumnSet_Additional extends Mage_Backend_Block_Widget_Grid_ColumnSet
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
