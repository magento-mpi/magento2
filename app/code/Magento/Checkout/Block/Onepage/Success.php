<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * One page checkout success page
 *
 * @category   Magento
 * @package    Magento_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Checkout_Block_Onepage_Success extends Magento_Core_Block_Template
{
    /**
     * See if the order has state, visible on frontend
     *
     * @return bool
     */
    public function isOrderVisible()
    {
        return (bool)$this->_getData('is_order_visible');
    }

    /**
     * Getter for recurring profile view page
     *
     * @param $profile
     */
    public function getProfileUrl(\Magento\Object $profile)
    {
        return $this->getUrl('sales/recurring_profile/view', array('profile' => $profile->getId()));
    }

    /**
     * Initialize data and prepare it for output
     */
    protected function _beforeToHtml()
    {
        $this->_prepareLastOrder();
        $this->_prepareLastBillingAgreement();
        $this->_prepareLastRecurringProfiles();
        return parent::_beforeToHtml();
    }

    /**
     * Get last order ID from session, fetch it and check whether it can be viewed, printed etc
     */
    protected function _prepareLastOrder()
    {
        $orderId = Mage::getSingleton('Magento_Checkout_Model_Session')->getLastOrderId();
        if ($orderId) {
            $order = Mage::getModel('Magento_Sales_Model_Order')->load($orderId);
            if ($order->getId()) {
                $isVisible = !in_array($order->getState(),
                    Mage::getSingleton('Magento_Sales_Model_Order_Config')->getInvisibleOnFrontStates());
                $this->addData(array(
                    'is_order_visible' => $isVisible,
                    'view_order_url' => $this->getUrl('sales/order/view/', array('order_id' => $orderId)),
                    'print_url' => $this->getUrl('sales/order/print', array('order_id'=> $orderId)),
                    'can_print_order' => $isVisible,
                    'can_view_order'  => Mage::getSingleton('Magento_Customer_Model_Session')->isLoggedIn() && $isVisible,
                    'order_id'  => $order->getIncrementId(),
                ));
            }
        }
    }

    /**
     * Prepare billing agreement data from an identifier in the session
     */
    protected function _prepareLastBillingAgreement()
    {
        $agreementId = Mage::getSingleton('Magento_Checkout_Model_Session')->getLastBillingAgreementId();
        $customerId = Mage::getSingleton('Magento_Customer_Model_Session')->getCustomerId();
        if ($agreementId && $customerId) {
            $agreement = Mage::getModel('Magento_Sales_Model_Billing_Agreement')->load($agreementId);
            if ($agreement->getId() && $customerId == $agreement->getCustomerId()) {
                $this->addData(array(
                    'agreement_ref_id' => $agreement->getReferenceId(),
                    'agreement_url' => $this->getUrl('sales/billing_agreement/view',
                        array('agreement' => $agreementId)
                    ),
                ));
            }
        }
    }

    /**
     * Prepare recurring payment profiles from the session
     */
    protected function _prepareLastRecurringProfiles()
    {
        $profileIds = Mage::getSingleton('Magento_Checkout_Model_Session')->getLastRecurringProfileIds();
        if ($profileIds && is_array($profileIds)) {
            $collection = Mage::getModel('Magento_Sales_Model_Recurring_Profile')->getCollection()
                ->addFieldToFilter('profile_id', array('in' => $profileIds))
            ;
            $profiles = array();
            foreach ($collection as $profile) {
                $profiles[] = $profile;
            }
            if ($profiles) {
                $this->setRecurringProfiles($profiles);
                if (Mage::getSingleton('Magento_Customer_Model_Session')->isLoggedIn()) {
                    $this->setCanViewProfiles(true);
                }
            }
        }
    }
}
