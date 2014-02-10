<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_AdvancedCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml grid product qty column renderer
 *
 * @category   Magento
 * @package    Magento_AdvancedCheckout
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\AdvancedCheckout\Block\Adminhtml\Manage\Grid\Renderer\Wishlist;

class Qty
    extends \Magento\Sales\Block\Adminhtml\Order\Create\Search\Grid\Renderer\Qty
{
    /**
     * Returns whether this qty field must be inactive
     *
     * @param   \Magento\Object $row
     * @return  bool
     */
    protected function _isInactive($row)
    {
        return parent::_isInactive($row->getProduct());
    }
}
