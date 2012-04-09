<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Pbridge
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Pbridge observer
 *
 * @category    Enterprise
 * @package     Enterprise_Pbridge
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Pbridge_Model_Observer
{
    /**
     * Add HTTP header to response that allows browsers accept third-party cookies
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_Pbridge_Model_Observer
     */
    public function addPrivacyHeader(Varien_Event_Observer $observer)
    {
        /* @var $controllerAction Mage_Core_Controller_Varien_Action */
        $controllerAction = $observer->getEvent()->getData('controller_action');
        $controllerAction->getResponse()->setHeader("P3P", 'CP="CAO PSA OUR"', true);
        return $this;
    }

    /**
     * Check payment methods availability
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_Pbridge_Model_Observer
     */
    public function isPaymentMethodAvailable(Varien_Event_Observer $observer)
    {
        $method = $observer->getEvent()->getData('method_instance');
        /* @var $quote Mage_Sales_Model_Quote */
        $quote = $observer->getEvent()->getData('quote');
        $result = $observer->getEvent()->getData('result');
        $storeId = $quote ? $quote->getStoreId() : null;

        if (((bool)$this->_getMethodConfigData('using_pbridge', $method, $storeId) === true)
            && ((bool)$method->getIsDummy() === false)) {
            $result->isAvailable = false;
        }
        return $this;
    }

    /**
     * Update Payment Profiles functionality switcher
     * @param Varien_Event_Observer $observer
     * @return Enterprise_Pbridge_Model_Observer
     */
    public function updatePaymentProfileStatus(Varien_Event_Observer $observer)
    {
        $website = Mage::app()->getWebsite($observer->getEvent()->getData('website'));
        $braintreeEnabled = $website->getConfig('payment/braintree_basic/active')
            && $website->getConfig('payment/braintree_basic/payment_profiles_enabled');
        $authorizenetEnabled = $website->getConfig('payment/authorizenet/active')
            && $website->getConfig('payment/authorizenet/payment_profiles_enabled');

        $profileStatus = null;

        if ($braintreeEnabled || $authorizenetEnabled) {
            $profileStatus = 1;
        } else {
            $profileStatus = 0;
        }

        if ($profileStatus !== null) {
            $scope = $observer->getEvent()->getData('website') ? 'websites' : 'default';
            Mage::getConfig()->saveConfig('payment/pbridge/profilestatus', $profileStatus, $scope, $website->getId());
            Mage::app()->cleanCache(array(Mage_Core_Model_Config::CACHE_TAG));
        }
        return $this;
    }

    /**
     * Return system config value by key for specified payment method
     *
     * @param string $key
     * @param Mage_Payment_Model_Method_Abstract $method
     * @param int $storeId
     *
     * @return string
     */
    protected function _getMethodConfigData($key, Mage_Payment_Model_Method_Abstract $method, $storeId = null)
    {
        if (!$method->getCode()) {
            return null;
        }
        return Mage::getStoreConfig("payment/{$method->getCode()}/$key", $storeId);
    }
}
