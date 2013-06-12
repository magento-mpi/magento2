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
     * @var Mage_Core_Model_Cache_Type_Config
     */
    protected $_configCacheType;

    /**
     * Writer of configuration storage
     *
     * @var Mage_Core_Model_Config_Storage_WriterInterface
     */
    protected $_configWriter;

    /**
     * @param Mage_Core_Model_Config_Storage_WriterInterface $configWriter
     * @param Mage_Core_Model_Cache_Type_Config $configCacheType
     */
    public function __construct(
        Mage_Core_Model_Config_Storage_WriterInterface $configWriter,
        Mage_Core_Model_Cache_Type_Config $configCacheType
    ) {
        $this->_configWriter = $configWriter;
        $this->_configCacheType = $configCacheType;
    }

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

    /**
     * Save order into registry to use it in the overloaded controller.
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_Paypal_Model_Observer
     */
    public function saveOrderAfterSubmit(Varien_Event_Observer $observer)
    {
        /* @var $order Mage_Sales_Model_Order */
        $order = $observer->getEvent()->getData('order');
        Mage::register('pbridge_order', $order, true);
        return $this;
    }

    /**
     * Set data for response of frontend saveOrder action
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_Paypal_Model_Observer
     */
    public function setResponseAfterSaveOrder(Varien_Event_Observer $observer)
    {
        /* @var $order Mage_Sales_Model_Order */
        $order = Mage::registry('pbridge_order');
        if ($order && $order->getId()) {
            $payment = $order->getPayment();
            if ($payment && $payment->getMethodInstance()->getIsPendingOrderRequired()) {
                /* @var $controller Mage_Core_Controller_Varien_Action */
                $controller = $observer->getEvent()->getData('controller_action');
                $result = Mage::helper('Mage_Core_Helper_Data')->jsonDecode(
                    $controller->getResponse()->getBody('default'),
                    Zend_Json::TYPE_ARRAY
                );
                if (empty($result['error'])) {
                    $controller->loadLayout('checkout_onepage_review');
                    $block = $controller->getLayout()->createBlock('Enterprise_Pbridge_Block_Checkout_Payment_Review_Iframe');
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
                    $controller->getResponse()->setBody(Mage::helper('Mage_Core_Helper_Data')->jsonEncode($result));
                }
            }
        }

        return $this;
    }
}
