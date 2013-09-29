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
 * Gift wrapping adminhtml block for create order items
 *
 * @category   Magento
 * @package    Magento_GiftWrapping
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\GiftWrapping\Block\Adminhtml\Sales\Order\Create;

class Items
    extends \Magento\Adminhtml\Block\Sales\Order\Create\AbstractCreate
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
