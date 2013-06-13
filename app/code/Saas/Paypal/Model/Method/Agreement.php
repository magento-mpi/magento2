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
 * Paypal Billing Agreement implementation which can work with PayPal Permissions
 */
class Saas_Paypal_Model_Method_Agreement extends Mage_Paypal_Model_Method_Agreement
{
    /**
     * Get instance of model for PayPal Payments Pro
     *
     * @return Mage_Core_Model_Abstract
     */
    protected function _getPaypalProModel()
    {
        $helper = $this->_getSaasPaypalHelper();

        return $helper->isEcPermissions() || $helper->isWppPermissions() ? $this->_getPaypalBoardingProModelInstance()
            : $this->_getPaypalProModelInstance();
    }

    /**
     * Billing Agreement cannot be signed when Accelerated Boarding active
     *
     * @param Mage_Sales_Model_Quote $quote
     * @return bool
     */
    protected function _isAvailable($quote)
    {
        return !$this->_getSaasPaypalHelper()->isEcAcceleratedBoarding() && parent::_isAvailable($quote);
    }

    /**
     * Get helper for PayPal module in Saas scope
     *
     * @return Mage_Core_Helper_Abstract
     */
    protected function _getSaasPaypalHelper()
    {
        return Mage::helper('Saas_Paypal_Helper_Data');
    }

    /**
     * Get instance of PayPal Saas_Paypal_Model_Boarding_Pro model
     *
     * @return Saas_Paypal_Model_Boarding_Pro
     */
    protected function _getPaypalBoardingProModelInstance()
    {
        return Mage::getModel('Saas_Paypal_Model_Boarding_Pro');
    }
}
