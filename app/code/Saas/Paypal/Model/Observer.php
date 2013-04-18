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
     * when admin open paypal configuration page
     *
     * @param Varien_Event_Observer $observer
     * @return Saas_Paypal_Model_Observer
     */
    public function updateBoardingStatus($observer)
    {
        $request = $observer->getEvent()->getControllerAction()->getRequest();
        if ($request->getParam('section') == 'payment') {
            $token = (string)$request->getParam('request_token');
            $code  = (string)$request->getParam('verification_code');

            if ($token && $code) {
                $onboarding = Mage::getModel('Saas_Paypal_Model_Boarding_Onboarding');
                $onboarding->updateMethodStatus($token, $code);
            }
        }

        return $this;
    }

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
        } catch (Exception $e) {}
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

    /**
     * Load country dependent PayPal solutions system configuration
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function loadCountryDependentSolutionsConfig(Varien_Event_Observer $observer)
    {
        $requestParam = Mage_Paypal_Block_Adminhtml_System_Config_Field_Country::REQUEST_PARAM_COUNTRY;
        $countryCode  = Mage::app()->getRequest()->getParam($requestParam);
        if (is_null($countryCode) || preg_match('/^[a-zA-Z]{2}$/', $countryCode) == 0) {
            //TODO should to know correct model
            $countryCode = (string)Mage::getSingleton('adminhtml/config_data')
                ->getConfigDataValue('paypal/general/merchant_country');
        }
        if (empty($countryCode)) {
            $countryCode = Mage::helper('core')->getDefaultCountry();
        }

        $paymentGroups   = $observer->getEvent()->getConfig()->getNode('sections/payment/groups');

        $this->extendParentConfig($paymentGroups, 'paypal_payments/*/backend_config/' . $countryCode);
        $this->appendChildFields($paymentGroups, 'paypal_payments/*');
        $this->extendParentConfig($paymentGroups, 'paypal_notice/backend_config/' . $countryCode);
        $this->extendParentConfig($paymentGroups, 'paypal_group_all_in_one/backend_config/' . $countryCode);
        $this->extendParentConfig($paymentGroups, 'paypal_payment_solutions/backend_config/' . $countryCode);
    }

    /**
     * Extend/Override config nodes with country dependent configs
     *
     * @param Mage_Core_Model_Config_Element $paymentGroups
     * @param string $groupsXpath
     * @return Mage_Paypal_Model_Observer
     */
    public function extendParentConfig(Mage_Core_Model_Config_Element $paymentGroups, $groupsXpath)
    {
        $paymentsConfigs = $paymentGroups->xpath($groupsXpath);
        if ($paymentsConfigs) {
            foreach ($paymentsConfigs as $config) {
                /* @var $config Mage_Core_Model_Config_Element */
                $parent = $config->getParent()->getParent();
                $parent->extend($config, true);
            }
        }
        return $this;
    }

    /**
     * Append fields to payment groups
     *
     * @param Mage_Core_Model_Config_Element $paymentGroups
     * @param string $groupsXpath
     * @return Mage_Paypal_Model_Observer
     */
    public function appendChildFields(Mage_Core_Model_Config_Element $paymentGroups, $groupsXpath)
    {
        $payments = $paymentGroups->xpath($groupsXpath);
        foreach ($payments as $payment) {
            /* @var $payment Mage_Core_Model_Config_Element */
            if ((int)$payment->include) {
                $fields = $paymentGroups->xpath((string)$payment->group . '/fields');
                if (isset($fields[0])) {
                    $fields[0]->appendChild($payment, true);
                }
            }
        }
        return $this;
    }
}
