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
class Magento_GiftMessage_Block_Adminhtml_Sales_Order_View_Giftoptions extends Magento_Adminhtml_Block_Template
{
    /**
     * Get order item object from parent block
     *
     * @return Magento_Sales_Model_Order_Item
     */
    public function getItem()
    {
        return $this->getParentBlock()->getData('item');
    }
}
