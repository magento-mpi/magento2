<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Pbridge\Model;

/**
 * Pbridge observer
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Observer
{
    /**
     * Cache type configuration
     *
     * @var \Magento\App\Cache\Type\Config
     */
    protected $_configCacheType;

    /**
     * Writer of configuration storage
     *
     * @var \Magento\App\Config\Storage\WriterInterface
     */
    protected $_configWriter;

    /**
     * Core store config
     *
     * @var \Magento\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Construct
     *
     * @param \Magento\App\Config\Storage\WriterInterface $configWriter
     * @param \Magento\App\Cache\Type\Config $configCacheType
     * @param \Magento\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\App\Config\Storage\WriterInterface $configWriter,
        \Magento\App\Cache\Type\Config $configCacheType,
        \Magento\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_configWriter = $configWriter;
        $this->_configCacheType = $configCacheType;
        $this->_scopeConfig = $scopeConfig;
        $this->_storeManager = $storeManager;
    }

    /**
     * Check payment methods availability
     *
     * @param \Magento\Event\Observer $observer
     * @return $this
     */
    public function isPaymentMethodAvailable(\Magento\Event\Observer $observer)
    {
        $method = $observer->getEvent()->getData('method_instance');
        /* @var $quote \Magento\Sales\Model\Quote */
        $quote = $observer->getEvent()->getData('quote');
        $result = $observer->getEvent()->getData('result');
        $storeId = $quote ? $quote->getStoreId() : null;

        if ((bool)$this->_getMethodConfigData(
            'using_pbridge',
            $method,
            $storeId
        ) === true && (bool)$method->getIsDummy() === false
        ) {
            $result->isAvailable = false;
        }
        return $this;
    }

    /**
     * Update Payment Profiles functionality switcher
     * @param \Magento\Event\Observer $observer
     * @return $this
     */
    public function updatePaymentProfileStatus(\Magento\Event\Observer $observer)
    {
        $website = $this->_storeManager->getWebsite($observer->getEvent()->getData('website'));
        $braintreeEnabled = $website->getConfig(
            'payment/braintree_basic/active'
        ) && $website->getConfig(
            'payment/braintree_basic/payment_profiles_enabled'
        );
        $authorizenetEnabled = $website->getConfig(
            'payment/pbridge_authorizenet/active'
        ) && $website->getConfig(
            'payment/pbridge_authorizenet/payment_profiles_enabled'
        );

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
     * @param \Magento\Payment\Model\MethodInterface $method
     * @param int $storeId
     *
     * @return string
     */
    protected function _getMethodConfigData($key, \Magento\Payment\Model\MethodInterface $method, $storeId = null)
    {
        if (!$method->getCode()) {
            return null;
        }
        return $this->_scopeConfig->getValue(
            "payment/{$method->getCode()}/{$key}",
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
