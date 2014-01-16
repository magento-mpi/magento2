<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Order information tab
 *
 * @category   Magento
 * @package    Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Block\Adminhtml\Order\View\Tab;

class Info
    extends \Magento\Sales\Block\Adminhtml\Order\AbstractOrder
    implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * Retrieve order model instance
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->_coreRegistry->registry('current_order');
    }

    /**
     * Retrieve source model instance
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getSource()
    {
        return $this->getOrder();
    }

    /**
     * Retrieve order totals block settings
     *
     * @return array
     */
    public function getOrderTotalData()
    {
        return array(
            'can_display_total_due'      => true,
            'can_display_total_paid'     => true,
            'can_display_total_refunded' => true,
        );
    }

    public function getOrderInfoData()
    {
        return array(
            'no_use_order_link' => true,
        );
    }

    public function getTrackingHtml()
    {
        return $this->getChildHtml('order_tracking');
    }

    public function getItemsHtml()
    {
        return $this->getChildHtml('order_items');
    }

    /**
     * Retrieve gift options container block html
     *
     * @return string
     */
    public function getGiftOptionsHtml()
    {
        return $this->getChildHtml('gift_options');
    }

    public function getPaymentHtml()
    {
        return $this->getChildHtml('order_payment');
    }

    /**
     * @return string
     */
    public function getShippingHtml()
    {
        if (!$this->getOrder()->getIsVirtual()) {
            return $this->getChildHtml('order_shipping_view');
        }
        return '';
    }

    public function getViewUrl($orderId)
    {
        return $this->getUrl('sales/*/*', array('order_id'=>$orderId));
    }

    /**
     * ######################## TAB settings #################################
     */
    public function getTabLabel()
    {
        return __('Information');
    }

    public function getTabTitle()
    {
        return __('Order Information');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }
}
