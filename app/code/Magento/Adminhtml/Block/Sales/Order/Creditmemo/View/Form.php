<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Creditmemo view form
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Sales_Order_Creditmemo_View_Form extends Magento_Adminhtml_Block_Sales_Order_Abstract
{
    /**
     * Retrieve invoice order
     *
     * @return Magento_Sales_Model_Order
     */
    public function getOrder()
    {
        return $this->getCreditmemo()->getOrder();
    }

    /**
     * Retrieve source
     *
     * @return Magento_Sales_Model_Order_Creditmemo
     */
    public function getSource()
    {
        return $this->getCreditmemo();
    }

    /**
     * Retrieve order totals block settings
     *
     * @return array
     */
    public function getOrderTotalData()
    {
        return array(
            'grand_total_title' => __('Total Refund'),
        );
    }

    /**
     * Retrieve creditmemo model instance
     *
     * @return Magento_Sales_Model_Order_Creditmemo
     */
    public function getCreditmemo()
    {
        return $this->_coreRegistry->registry('current_creditmemo');
    }

    public function getOrderUrl()
    {
        return $this->getUrl('*/sales_order/view', array('order_id' => $this->getCreditmemo()->getOrderId()));
    }
}
