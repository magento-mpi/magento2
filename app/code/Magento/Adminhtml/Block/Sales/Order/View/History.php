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
 * Order history block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Sales_Order_View_History extends Magento_Adminhtml_Block_Template
{
    protected function _prepareLayout()
    {
        $onclick = "submitAndReloadArea($('order_history_block').parentNode, '".$this->getSubmitUrl()."')";
        $button = $this->getLayout()->createBlock('Magento_Adminhtml_Block_Widget_Button')
            ->setData(array(
                'label'   => __('Submit Comment'),
                'class'   => 'save',
                'onclick' => $onclick
            ));
        $this->setChild('submit_button', $button);
        return parent::_prepareLayout();
    }

    public function getStatuses()
    {
        $state = $this->getOrder()->getState();
        $statuses = $this->getOrder()->getConfig()->getStateStatuses($state);
        return $statuses;
    }

    public function canSendCommentEmail()
    {
        return Mage::helper('Magento_Sales_Helper_Data')->canSendOrderCommentEmail($this->getOrder()->getStore()->getId());
    }

    /**
     * Retrieve order model
     *
     * @return Magento_Sales_Model_Order
     */
    public function getOrder()
    {
        return Mage::registry('sales_order');
    }

    public function canAddComment()
    {
        return $this->_authorization->isAllowed('Magento_Sales::comment') &&
               $this->getOrder()->canComment();
    }

    public function getSubmitUrl()
    {
        return $this->getUrl('*/*/addComment', array('order_id'=>$this->getOrder()->getId()));
    }

    /**
     * Customer Notification Applicable check method
     *
     * @param  Magento_Sales_Model_Order_Status_History $history
     * @return boolean
     */
    public function isCustomerNotificationNotApplicable(Magento_Sales_Model_Order_Status_History $history)
    {
        return $history->isCustomerNotificationNotApplicable();
    }
}
