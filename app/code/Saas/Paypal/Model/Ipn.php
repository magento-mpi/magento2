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
 * PayPal Instant Payment Notification processor model.
 * Should use payment method's config model for various manipulations.
 */
class Saas_Paypal_Model_Ipn extends Mage_Paypal_Model_Ipn
{
    /**
     * Load and validate order, instantiate proper configuration.
     * Should load right payment method's config
     *
     * @return Mage_Sales_Model_Order
     * @throws Exception
     */
    protected function _getOrder()
    {
        if (empty($this->_order)) {
            // get proper order
            $id = $this->_request['invoice'];
            $this->_order = Mage::getModel('Mage_Sales_Model_Order')->loadByIncrementId($id);
            if (!$this->_order->getId()) {
                throw new Exception(Mage::helper('Mage_Paypal_Helper_Data')->__('Wrong order ID: "%s".', $id));
            }
            // re-initialize config with the method code and store id
            /* @var $methodInstance Mage_Payment_Model_Method_Abstract */
            $methodInstance = $this->_order->getPayment()->getMethodInstance();
            $methodCode = $methodInstance->getCode();
            $config = $methodInstance->getConfig();
            if (!$config || !($config instanceof Mage_Paypal_Model_Config)) {
                $config = Mage::getModel('Mage_Paypal_Model_Config', array($methodCode, $this->_order->getStoreId()));
            } else {
                $config->setStoreId($this->_order->getStoreId());
            }
            $this->_config = $config;
            if (!$this->_config->isMethodActive($methodCode) || !$this->_config->isMethodAvailable($methodCode)) {
                throw new Exception(Mage::helper('Mage_Paypal_Helper_Data')->__('Method "%s" is not available.', $methodCode));
            }

            $this->_verifyOrder();
        }
        return $this->_order;
    }

    /**
     * Validate incoming request data, as PayPal recommends.
     * Fix for getting businessAccount from another place.
     *
     * @throws Exception
     * @see https://cms.paypal.com/cgi-bin/marketingweb?cmd=_render-content&content_ID=developer/e_howto_admin_IPNIntro
     */
    protected function _verifyOrder()
    {
        // verify merchant email intended to receive notification
        $merchantEmail = (method_exists($this->_config, 'getIpnBusinessAccount')) ?
            $this->_config->getIpnBusinessAccount() :
            $this->_config->businessAccount;

        $merchantId = $this->_config->receiver_id;
        if ($merchantEmail && !$merchantId) {
            $receiverEmail = $this->getRequestData('business');
            if (!$receiverEmail) {
                $receiverEmail = $this->getRequestData('receiver_email');
            }
            if ($merchantEmail != $receiverEmail) {
                throw new Exception(Mage::helper('Mage_Paypal_Helper_Data')->__('Requested %s and configured %s merchant emails do not match.', $receiverEmail, $merchantEmail));
            }
        }

        $receiverId = $this->getRequestData('receiver_id');
        if ($merchantId && ($merchantId != $receiverId)) {
            throw new Exception(Mage::helper('Mage_Paypal_Helper_Data')->__('Requested %s and configured %s receiver id do not match.', $merchantId, $receiverId));
        }
    }

    /**
     * Load recurring profile.
     * Should load right payment method's config
     *
     * @return Mage_Sales_Model_Recurring_Profile
     * @throws Exception
     */
    protected function _getRecurringProfile()
    {
        if (empty($this->_recurringProfile)) {
            // get proper recurring profile
            $internalReferenceId = $this->_request['recurring_payment_id'];
            $this->_recurringProfile = Mage::getModel('Mage_Sales_Model_Recurring_Profile')
                ->loadByInternalReferenceId($internalReferenceId);
            if (!$this->_recurringProfile->getId()) {
                throw new Exception(Mage::helper('Mage_Paypal_Helper_Data')->__('Wrong recurring profile INTERNAL_REFERENCE_ID: "%s".', $internalReferenceId));
            }
            // re-initialize config with the method code and store id
            /* @var $methodInstance Mage_Payment_Model_Method_Abstract */
            $methodInstance = $this->_recurringProfile->getMethodInstance();
            $methodCode = $methodInstance->getCode();
            $config = $methodInstance->getConfig();
            if (!$config || !($config instanceof Mage_Paypal_Model_Config)) {
                $config = Mage::getModel('Mage_Paypal_Model_Config',
                    array($methodCode, $this->_recurringProfile->getStoreId())
                );
            } else {
                $config->setStoreId($this->_recurringProfile->getStoreId());
            }
            $this->_config = $config;
            if (!$this->_config->isMethodActive($methodCode) || !$this->_config->isMethodAvailable($methodCode)) {
                throw new Exception(Mage::helper('Mage_Paypal_Helper_Data')->__('Method "%s" is not available.', $methodCode));
            }
        }
        return $this->_recurringProfile;
    }
}
