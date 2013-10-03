<?php
/**
 * Sku Errors Column Set
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\AdvancedCheckout\Block\Adminhtml\Sku\Errors\Grid\ColumnSet;

class SkuErrors
    extends \Magento\Backend\Block\Widget\Grid\ColumnSet
{
    /**
     * Retrieve row css class for specified item
     *
     * @param \Magento\Object $item
     * @return string
     */
    public function getRowClass(\Magento\Object $item)
    {
        if ($item->getCode() == \Magento\AdvancedCheckout\Helper\Data::ADD_ITEM_STATUS_FAILED_QTY_ALLOWED) {
            return 'qty-not-available';
        }
        return '';
    }
}
