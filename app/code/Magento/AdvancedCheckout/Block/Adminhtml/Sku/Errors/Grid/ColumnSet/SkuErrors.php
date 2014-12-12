<?php
/**
 * Sku Errors Column Set
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\AdvancedCheckout\Block\Adminhtml\Sku\Errors\Grid\ColumnSet;

class SkuErrors extends \Magento\Backend\Block\Widget\Grid\ColumnSet
{
    /**
     * Retrieve row css class for specified item
     *
     * @param \Magento\Framework\Object $item
     * @return string
     */
    public function getRowClass(\Magento\Framework\Object $item)
    {
        if ($item->getCode() == \Magento\AdvancedCheckout\Helper\Data::ADD_ITEM_STATUS_FAILED_QTY_ALLOWED) {
            return 'qty-not-available';
        }
        return '';
    }
}
