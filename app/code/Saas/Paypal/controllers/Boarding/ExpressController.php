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
 * Express Checkout Controller
 */
class Saas_Paypal_Boarding_ExpressController extends Mage_Paypal_Controller_Express_Abstract
{
    /**
     * Config mode type
     *
     * @var string
     */
    protected $_configType = 'Saas_Paypal_Model_Boarding_Config';

    /**
     * Config method type
     *
     * @var string
     */
    protected $_configMethod = Saas_Paypal_Model_Boarding_Config::METHOD_EXPRESS_BOARDING;

    /**
     * Helper factory
     *
     * @var Mage_Core_Model_Factory_Helper
     */
    protected $_helperFactory;

    /**
     * Store config model
     *
     * @var Mage_Core_Model_Store_Config
     */
    protected $_storeConfig;

    /**
     * Checkout session model
     *
     * @var Mage_Checkout_Model_Session
     */
    protected $_checkoutSession;

    /**
     * Customer session model
     *
     * @var Mage_Customer_Model_Session
     */
    protected $_customerSession;

    /**
     * URL Model instance
     *
     * @var Mage_Core_Model_Url
     */
    protected $_url;

    /**
     * Logger
     *
     * @var Mage_Core_Model_Logger
     */
    protected $_logger;

    /**
     * Checkout mode type
     *
     * @var string
     */
    protected $_checkoutType = 'Saas_Paypal_Model_Boarding_Express_Checkout';

    public function __construct(
        Mage_Core_Controller_Varien_Action_Context $context,
        Mage_Core_Model_Factory_Helper $helperFactory,
        Mage_Core_Model_Store_Config $storeConfig,
        Mage_Checkout_Model_Session $checkoutSession,
        Mage_Customer_Model_Session $customerSession,
        Mage_Core_Model_Url $url,
        Mage_Core_Model_Logger $logger,
        $areaCode = null
    ) {
        parent::__construct($context, $areaCode);
        $this->_helperFactory = $helperFactory;
        $this->_storeConfig = $storeConfig;
        $this->_checkoutSession = $checkoutSession;
        $this->_customerSession = $customerSession;
        $this->_url = $url;
        $this->_logger = $logger;
    }

    /**
     * Redirect to login page
     */
    public function redirectLogin()
    {
        $this->setFlag('', 'no-dispatch', true);
        $this->_customerSession->setBeforeAuthUrl($this->_url->getUrl('checkout/cart'));
        $this->getResponse()->setRedirect(
            $this->_helperFactory->get('Mage_Core_Helper_Url')->addRequestParam(
                $this->_helperFactory->get('Mage_Customer_Helper_Data')->getLoginUrl(),
                array('context' => 'checkout')
            )
        );
    }

    /**
     * Start Express Checkout by requesting initial token and dispatching customer to PayPal
     */
    public function startAction()
    {
        if (!$this->_getValidationMinAmount()) {
            $this->_redirect('checkout/cart');
            return;
        }

        try {
            $this->_initCheckout();

            $customer = $this->_customerSession->getCustomer();
            if ($customer && $customer->getId()) {
                $this->_checkout->setCustomerWithAddressChange(
                    $customer, $this->_getQuote()->getBillingAddress(), $this->_getQuote()->getShippingAddress()
                );
            }

            // billing agreement
            $isBARequested = (bool)$this->getRequest()
                ->getParam(Mage_Paypal_Model_Express_Checkout::PAYMENT_INFO_TRANSPORT_BILLING_AGREEMENT);
            if ($customer && $customer->getId()) {
                $this->_checkout->setIsBillingAgreementRequested($isBARequested);
            }

            // giropay
            $this->_checkout->prepareGiropayUrls(
                $this->_url->getUrl('checkout/onepage/success'),
                $this->_url->getUrl('paypal/boarding_express/cancel'),
                $this->_url->getUrl('checkout/onepage/success')
            );

            $button = (bool)$this->getRequest()->getParam(Mage_Paypal_Model_Express_Checkout::PAYMENT_INFO_BUTTON);
            $token  = $this->_checkout->start($this->_url->getUrl('*/*/return'),
                $this->_url->getUrl('*/*/cancel'), $button);
            if ($token && $url = $this->_checkout->getRedirectUrl()) {
                $this->_initToken($token);
                $this->getResponse()->setRedirect($url);
                return;
            }
        } catch (Mage_Core_Exception $e) {
            $this->_checkoutSession->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_checkoutSession->addError($this->__('Unable to start Express Checkout.'));
            $this->_logger->logException($e);
        }

        $this->_redirect('checkout/cart');
    }

    /**
     * Return checkout quote object
     *
     * @return Mage_Sale_Model_Quote
     */
    private function _getQuote()
    {
        if (!$this->_quote) {
            $this->_quote = $this->_checkoutSession->getQuote();
        }
        return $this->_quote;
    }

    /**
     * Return validation if min amount is active and is set
     *
     * @return bool
     */
    private function _getValidationMinAmount()
    {
        $baseSubtotal = $this->_checkoutSession->getQuote()->getBaseSubtotal();
        $minAmountActive = $this->_storeConfig->getConfig('sales/minimum_order/active');
        $minAmount = $minAmountActive ? $this->_storeConfig->getConfig('sales/minimum_order/amount') : 0;

        return $baseSubtotal >= $minAmount;
    }
}
