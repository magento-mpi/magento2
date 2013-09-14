<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 *
 * PayPal Standard Checkout Module
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Paypal\Model;

class Standard extends \Magento\Payment\Model\Method\AbstractMethod
{
    protected $_code  = \Magento\Paypal\Model\Config::METHOD_WPS;
    protected $_formBlockType = 'Magento\Paypal\Block\Standard\Form';
    protected $_infoBlockType = 'Magento\Paypal\Block\Payment\Info';
    protected $_isInitializeNeeded      = true;
    protected $_canUseInternal          = false;
    protected $_canUseForMultishipping  = false;

    /**
     * Config instance
     * @var \Magento\Paypal\Model\Config
     */
    protected $_config = null;

    /**
     * Whether method is available for specified currency
     *
     * @param string $currencyCode
     * @return bool
     */
    public function canUseForCurrency($currencyCode)
    {
        return $this->getConfig()->isCurrencyCodeSupported($currencyCode);
    }

    /**
     * Get paypal session namespace
     *
     * @return \Magento\Core\Model\Session\Generic
     */
    public function getSession()
    {
        return \Mage::getSingleton('Magento\Paypal\Model\Session');
    }

    /**
     * Get checkout session namespace
     *
     * @return \Magento\Checkout\Model\Session
     */
    public function getCheckout()
    {
        return \Mage::getSingleton('Magento\Checkout\Model\Session');
    }

    /**
     * Get current quote
     *
     * @return \Magento\Sales\Model\Quote
     */
    public function getQuote()
    {
        return $this->getCheckout()->getQuote();
    }

    /**
     * Create main block for standard form
     *
     */
    public function createFormBlock($name)
    {
        $block = $this->getLayout()->createBlock('Magento\Paypal\Block\Standard\Form', $name)
            ->setMethod('paypal_standard')
            ->setPayment($this->getPayment())
            ->setTemplate('standard/form.phtml');

        return $block;
    }

    /**
     * Return Order place redirect url
     *
     * @return string
     */
    public function getOrderPlaceRedirectUrl()
    {
          return \Mage::getUrl('paypal/standard/redirect', array('_secure' => true));
    }

    /**
     * Return form field array
     *
     * @return array
     */
    public function getStandardCheckoutFormFields()
    {
        $orderIncrementId = $this->getCheckout()->getLastRealOrderId();
        $order = \Mage::getModel('Magento\Sales\Model\Order')->loadByIncrementId($orderIncrementId);
        /* @var $api \Magento\Paypal\Model\Api\Standard */
        $api = \Mage::getModel('Magento\Paypal\Model\Api\Standard')->setConfigObject($this->getConfig());
        $api->setOrderId($orderIncrementId)
            ->setCurrencyCode($order->getBaseCurrencyCode())
            //->setPaymentAction()
            ->setOrder($order)
            ->setNotifyUrl(\Mage::getUrl('paypal/ipn/'))
            ->setReturnUrl(\Mage::getUrl('paypal/standard/success'))
            ->setCancelUrl(\Mage::getUrl('paypal/standard/cancel'));

        // export address
        $isOrderVirtual = $order->getIsVirtual();
        $address = $isOrderVirtual ? $order->getBillingAddress() : $order->getShippingAddress();
        if ($isOrderVirtual) {
            $api->setNoShipping(true);
        } elseif ($address->validate()) {
            $api->setAddress($address);
        }

        // add cart totals and line items
        $parameters = array('params' => array($order));
        $api->setPaypalCart(\Mage::getModel('Magento\Paypal\Model\Cart', $parameters))
            ->setIsLineItemsEnabled($this->_config->lineItemsEnabled)
        ;
        $api->setCartSummary($this->_getAggregatedCartSummary());
        $api->setLocale($api->getLocaleCode());
        $result = $api->getStandardCheckoutRequest();
        return $result;
    }

    /**
     * Instantiate state and set it to state object
     * @param string $paymentAction
     * @param \Magento\Object
     */
    public function initialize($paymentAction, $stateObject)
    {
        $state = \Magento\Sales\Model\Order::STATE_PENDING_PAYMENT;
        $stateObject->setState($state);
        $stateObject->setStatus('pending_payment');
        $stateObject->setIsNotified(false);
    }

    /**
     * Config instance getter
     * @return \Magento\Paypal\Model\Config
     */
    public function getConfig()
    {
        if (null === $this->_config) {
            $params = array($this->_code);
            if ($store = $this->getStore()) {
                $params[] = is_object($store) ? $store->getId() : $store;
            }
            $this->_config = \Mage::getModel('Magento\Paypal\Model\Config', array('params' => $params));
        }
        return $this->_config;
    }

    /**
     * Check whether payment method can be used
     * @param \Magento\Sales\Model\Quote
     * @return bool
     */
    public function isAvailable($quote = null)
    {
        if (parent::isAvailable($quote) && $this->getConfig()->isMethodAvailable()) {
            return true;
        }
        return false;
    }

    /**
     * Custom getter for payment configuration
     *
     * @param string $field
     * @param int $storeId
     * @return mixed
     */
    public function getConfigData($field, $storeId = null)
    {
        return $this->getConfig()->$field;
    }

    /**
     * Aggregated cart summary label getter
     *
     * @return string
     */
    private function _getAggregatedCartSummary()
    {
        if ($this->_config->lineItemsSummary) {
            return $this->_config->lineItemsSummary;
        }
        return \Mage::app()->getStore($this->getStore())->getFrontendName();
    }
}
