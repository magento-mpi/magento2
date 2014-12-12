<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/**
 * Adminhtml grid product qty column renderer
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\AdvancedCheckout\Block\Adminhtml\Manage\Grid\Renderer\Wishlist;

class Qty extends \Magento\Sales\Block\Adminhtml\Order\Create\Search\Grid\Renderer\Qty
{
    /**
     * Returns whether this qty field must be inactive
     *
     * @param   \Magento\Framework\Object $row
     * @return  bool
     */
    protected function _isInactive($row)
    {
        return parent::_isInactive($row->getProduct());
    }
}
