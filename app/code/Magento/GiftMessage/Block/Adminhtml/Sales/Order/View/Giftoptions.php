<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftMessage
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml sales order view gift options block
 *
 * @category   Magento
 * @package    Magento_GiftMessage
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\GiftMessage\Block\Adminhtml\Sales\Order\View;

class Giftoptions extends \Magento\Adminhtml\Block\Template
{
    /**
     * Get order item object from parent block
     *
     * @return \Magento\Sales\Model\Order\Item
     */
    public function getItem()
    {
        return $this->getParentBlock()->getData('item');
    }
}
