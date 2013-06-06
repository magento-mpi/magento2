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
 * Rewritten model to use only one model of Express Checkout from three existing models.
 */
class Saas_Paypal_Model_ExpressUk extends Mage_PaypalUk_Model_Express
{
    /**
     * Express Checkout Onboarding method model
     *
     * @var Saas_Paypal_Model_Boarding_Express
     */
    protected $_onboardingEc = null;

    /**
     * EC PE won't be available if the EC is available
     *
     * @param Mage_Sales_Model_Quote $quote
     * @return bool
     */
    public function isAvailable($quote = null)
    {
        if (!parent::isAvailable($quote)) {
            return false;
        }
        if (!$this->_ecInstance) {
            $this->_ecInstance = Mage::helper('Mage_Payment_Helper_Data')
                ->getMethodInstance(Mage_Paypal_Model_Config::METHOD_WPP_EXPRESS);
        }
        if (!$this->_onboardingEc) {
            $this->_onboardingEc = Mage::helper('Mage_Payment_Helper_Data')
                ->getMethodInstance(Saas_Paypal_Model_Boarding_Config::METHOD_EXPRESS_BOARDING);
        }
        if ($quote) {
            $this->_ecInstance->setStore($quote->getStoreId());
            $this->_onboardingEc->setStore($quote->getStoreId());
        }
        return !$this->_onboardingEc->isAvailable() && !$this->_ecInstance->isAvailable();
    }
}
