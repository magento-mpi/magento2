<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftWrapping
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Gift wrapping adminhtml block for view order items
 *
 * @category   Magento
 * @package    Magento_GiftWrapping
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\GiftWrapping\Block\Adminhtml\Sales\Order\View;

class Items
    extends Magento_Adminhtml_Block_Sales_Items_Abstract
{
    /**
     * Get order item from parent block
     *
     * @return \Magento\Sales\Model\Order\Item
     */
    public function getItem()
    {
        return $this->getParentBlock()->getData('item');
    }

    /**
     * Prepare html output
     *
     * @return string
     */
    protected function _toHtml()
    {
        $_item = $this->getItem();
        if ($_item && $_item->getGwId()) {
            return parent::_toHtml();
        } else {
            return false;
        }
    }
}
