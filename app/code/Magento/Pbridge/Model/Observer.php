<?php
/**
 * {license_notice}
 *
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
     * @var \Magento\Framework\App\Cache\Type\Config
     */
    protected $_configCacheType;

    /**
     * Writer of configuration storage
     *
     * @var \Magento\Framework\App\Config\Storage\WriterInterface
     */
    protected $_configWriter;

    /**
     * Core store config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * Store manager
     *
     * @var \Magento\Framework\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Core data
     *
     * @var \Magento\Core\Helper\Data
     */
    protected $_coreData;

    /**
     * @var \Magento\Framework\App\ViewInterface
     */
    protected $_view;

    /**
     * Construct
     *
     * @param \Magento\Framework\App\Config\Storage\WriterInterface $configWriter
     * @param \Magento\Framework\App\Cache\Type\Config $configCacheType
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Framework\App\ViewInterface $view
     */
    public function __construct(
        \Magento\Framework\App\Config\Storage\WriterInterface $configWriter,
        \Magento\Framework\App\Cache\Type\Config $configCacheType,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\StoreManagerInterface $storeManager,
        \Magento\Framework\Registry $registry,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Framework\App\ViewInterface $view
    ) {
        $this->_configWriter = $configWriter;
        $this->_configCacheType = $configCacheType;
        $this->_scopeConfig = $scopeConfig;
        $this->_storeManager = $storeManager;
        $this->_coreRegistry = $registry;
        $this->_coreData = $coreData;
        $this->_view = $view;
    }

    /**
     * Check payment methods availability
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function isPaymentMethodAvailable(\Magento\Framework\Event\Observer $observer)
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
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function updatePaymentProfileStatus(\Magento\Framework\Event\Observer $observer)
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

    /**
     * Save order into registry to use it in the overloaded controller.
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function saveOrderAfterSubmit(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getEvent()->getData('order');
        $this->_coreRegistry->register('pbridge_order', $order, true);
        return $this;
    }

    /**
     * Set data for response of frontend saveOrder action
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function setResponseAfterSaveOrder(\Magento\Framework\Event\Observer $observer)
    {
        /* @var $order \Magento\Sales\Model\Order */
        $order = $this->_coreRegistry->registry('pbridge_order');
        if ($order && $order->getId()) {
            $payment = $order->getPayment();
            if ($payment && $payment->getMethodInstance()->getIsPendingOrderRequired()) {
                /* @var $controller \Magento\Framework\App\Action\Action */
                $controller = $observer->getEvent()->getData('controller_action');
                $result = $this->_coreData->jsonDecode($controller->getResponse()->getBody('default'));
                if (empty($result['error'])) {
                    $this->_view->loadLayout('checkout_onepage_review');
                    /** @var \Magento\Pbridge\Block\Checkout\Payment\Review\Iframe $block */
                    $block = $this->_view->getLayout()->createBlock(
                        'Magento\Pbridge\Block\Checkout\Payment\Review\Iframe'
                    );
                    $block->setMethod($payment->getMethodInstance())
                        ->setRedirectUrlSuccess($payment->getMethodInstance()->getRedirectUrlSuccess())
                        ->setRedirectUrlError($payment->getMethodInstance()->getRedirectUrlError());
                    $html = $block->getIframeBlock()->toHtml();
                    $result['update_section'] = array(
                        'name' => 'pbridgeiframe',
                        'html' => $html
                    );
                    $result['redirect'] = false;
                    $result['success'] = false;
                    $controller->getResponse()->clearHeader('Location');
                    $controller->getResponse()->representJson($this->_coreData->jsonEncode($result));
                }
            }
        }

        return $this;
    }
}
