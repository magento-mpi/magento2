<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * PayPal module observer
 */
class Saas_Paypal_Model_Observer extends Mage_Paypal_Model_Observer
{
    /**
     * Update boarding status for current boarding method
     * if it has been changed
     *
     * @deprecated
     *
     * @param Varien_Event_Observer $observer
     * @return Saas_Paypal_Model_Observer
     */
    public function updateChangedBoardingStatus($observer)
    {
        if (Mage::getSingleton('Mage_Backend_Model_Session')->getData('onboarding_changed')) {
            $this->_updateBoardingStatus();
            Mage::getSingleton('Mage_Backend_Model_Session')->setData('onboarding_changed', false);
        }

        return $this;
    }

    /**
     * Update boarding status for current boarding method
     * after user is logged in.
     *
     * @deprecated
     *
     * @param Varien_Event_Observer $observer
     * @return Saas_Paypal_Model_Observer
     */
    public function updateBoardingStatusAfterLogin($observer)
    {
        $this->_updateBoardingStatus();
        return $this;
    }

    /**
     * Update boarding status main function.
     *
     * @deprecated
     */
    protected function _updateBoardingStatus()
    {
        try {
            Mage::getModel('Saas_Paypal_Model_Boarding_Onboarding')
                ->updateMethodStatus();
        } catch (Exception $e) {
        }
    }

    /**
     * Add link to configure permissions if they are not granted yet. Payment review actions.
     *
     * @param Varien_Event_Observer $observer
     */
    public function reviewPaymentDispatchAfter(Varien_Event_Observer $observer)
    {
        $order = Mage::registry('current_order');
        if ($order) {
            $this->_checkApiPermissions($order);
        }
    }

    /**
     * Add link to configure permissions if they are not granted yet. Invoice capture action.
     *
     * @param Varien_Event_Observer $observer
     */
    public function invoiceCaptureDispatchAfter(Varien_Event_Observer $observer)
    {
        $invoice = Mage::registry('current_invoice');
        if ($invoice && $invoice->getOrder()) {
            $this->_checkApiPermissions($invoice->getOrder());
        }
    }

    /**
     * Add link to configure permissions if they are not granted yet. Credit memo save action.
     *
     * @param Varien_Event_Observer $observer
     */
    public function creditMemoSaveDispatchAfter(Varien_Event_Observer $observer)
    {
        $request = $observer->getEvent()->getControllerAction()->getRequest();
        $data = $request->getPost('creditmemo');
        if (array_key_exists('do_offline', $data) && (int)$data['do_offline'] === 1) {
            return;
        }
        $creditmemo = Mage::registry('current_creditmemo');
        if ($creditmemo && $creditmemo->getOrder()) {
            $this->_checkApiPermissions($creditmemo->getOrder());
        }
    }

    /**
     * Add link to payment configuration if permissions are not granted yet
     *
     * @param Mage_Sales_Model_Order $order
     */
    protected function _checkApiPermissions(Mage_Sales_Model_Order $order)
    {
        $method = Saas_Paypal_Model_Boarding_Config::METHOD_EXPRESS_BOARDING;
        if (
            (string)$order->getPayment()->getData('method') === $method
            && Mage::helper('Saas_Paypal_Helper_Data')->isEcAcceleratedBoarding()
        ) {
            $notice = Mage::helper('Saas_Paypal_Helper_Data')->__(
                'To grant permissions to Magento Go for PayPal Express Checkout, click  <a href="%s">here</a>',
                Mage::helper('Saas_Saas_Helper_Data')->getUrl('*/system_config/edit', array('section' => 'payment'))
            );
            Mage::getSingleton('Mage_Backend_Model_Session')->addNotice($notice);
        }
    }
}
