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
class Magento_GiftWrapping_Block_Adminhtml_Sales_Order_Create_Items
    extends Magento_Adminhtml_Block_Sales_Order_Create_Abstract
{
    /**
     * Get order item from parent block
     *
     * @return Magento_Sales_Model_Order_Item
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
