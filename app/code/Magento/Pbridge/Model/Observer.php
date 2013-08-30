<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Pbridge observer
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Pbridge_Model_Observer
{
    /**
     * @var Magento_Core_Model_Cache_Type_Config
     */
    protected $_configCacheType;

    /**
     * Writer of configuration storage
     *
     * @var Magento_Core_Model_Config_Storage_WriterInterface
     */
    protected $_configWriter;

    /**
     * @param Magento_Core_Model_Config_Storage_WriterInterface $configWriter
     * @param Magento_Core_Model_Cache_Type_Config $configCacheType
     */
    public function __construct(
        Magento_Core_Model_Config_Storage_WriterInterface $configWriter,
        Magento_Core_Model_Cache_Type_Config $configCacheType
    ) {
        $this->_configWriter = $configWriter;
        $this->_configCacheType = $configCacheType;
    }

    /**
     * Add HTTP header to response that allows browsers accept third-party cookies
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_Pbridge_Model_Observer
     */
    public function addPrivacyHeader(Magento_Event_Observer $observer)
    {
        /* @var $controllerAction Magento_Core_Controller_Varien_Action */
        $controllerAction = $observer->getEvent()->getData('controller_action');
        $controllerAction->getResponse()->setHeader("P3P", 'CP="CAO PSA OUR"', true);
        return $this;
    }

    /**
     * Check payment methods availability
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_Pbridge_Model_Observer
     */
    public function isPaymentMethodAvailable(Magento_Event_Observer $observer)
    {
        $method = $observer->getEvent()->getData('method_instance');
        /* @var $quote Magento_Sales_Model_Quote */
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
     * @param Magento_Event_Observer $observer
     * @return Magento_Pbridge_Model_Observer
     */
    public function updatePaymentProfileStatus(Magento_Event_Observer $observer)
    {
        $website = Mage::app()->getWebsite($observer->getEvent()->getData('website'));
        $braintreeEnabled = $website->getConfig('payment/braintree_basic/active')
            && $website->getConfig('payment/braintree_basic/payment_profiles_enabled');
        $authorizenetEnabled = $website->getConfig('payment/pbridge_authorizenet/active')
            && $website->getConfig('payment/pbridge_authorizenet/payment_profiles_enabled');

        $profileStatus = null;

        if ($braintreeEnabled || $authorizenetEnabled) {
            $profileStatus = 1;
        } else {
            $profileStatus = 0;
        }

        if ($profileStatus !== null) {
            $scope = $observer->getEvent()->getData('website') ? 'websites' : 'default';
            $this->_configWriter->save('payment/pbridge/profilestatus', $profileStatus, $scope, $website->getId());
            $this->_configCacheType->clean();
        }
        return $this;
    }

    /**
     * Return system config value by key for specified payment method
     *
     * @param string $key
     * @param Magento_Payment_Model_Method_Abstract $method
     * @param int $storeId
     *
     * @return string
     */
    protected function _getMethodConfigData($key, Magento_Payment_Model_Method_Abstract $method, $storeId = null)
    {
        if (!$method->getCode()) {
            return null;
        }
        return Mage::getStoreConfig("payment/{$method->getCode()}/$key", $storeId);
    }
}
