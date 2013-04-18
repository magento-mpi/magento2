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
     * Checkout mode type
     *
     * @var string
     */
    protected $_checkoutType = 'Saas_Paypal_Model_Boarding_Express_Checkout';

    /**
     * Redirect to login page
     */
    public function redirectLogin()
    {
        $this->setFlag('', 'no-dispatch', true);
        Mage::getSingleton('Mage_Customer_Model_Session')->setBeforeAuthUrl(Mage::getUrl('checkout/cart'));
        $this->getResponse()->setRedirect(
            Mage::helper('Mage_Core_Helper_Url')->addRequestParam(
                Mage::helper('Mage_Customer_Helper_Data')->getLoginUrl(),
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

            $customer = Mage::getSingleton('Mage_Customer_Model_Session')->getCustomer();
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
                Mage::getUrl('checkout/onepage/success'),
                Mage::getUrl('paypal/boarding_express/cancel'),
                Mage::getUrl('checkout/onepage/success')
            );

            $button = (bool)$this->getRequest()->getParam(Mage_Paypal_Model_Express_Checkout::PAYMENT_INFO_BUTTON);
            $token  = $this->_checkout->start(Mage::getUrl('*/*/return'), Mage::getUrl('*/*/cancel'), $button);
            if ($token && $url = $this->_checkout->getRedirectUrl()) {
                $this->_initToken($token);
                $this->getResponse()->setRedirect($url);
                return;
            }
        } catch (Mage_Core_Exception $e) {
            $this->_getCheckoutSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getCheckoutSession()->addError($this->__('Unable to start Express Checkout.'));
            Mage::logException($e);
        }

        $this->_redirect('checkout/cart');
    }

    /**
     * Instantiate quote and checkout
     *
     * @return mixed
     * @throws Mage_Core_Exception
     */
    protected function _initCheckout()
    {
        $quote = $this->_getQuote();
        if (!$quote->hasItems() || $quote->getHasError()) {
            $this->getResponse()->setHeader('HTTP/1.1','403 Forbidden');
            Mage::throwException(Mage::helper('Mage_Paypal_Helper_Data')->__('Unable to initialize Express Checkout.'));
        }
        $this->_checkout = Mage::getSingleton($this->_checkoutType, array(
            'config' => $this->_config,
            'quote'  => $quote,
        ));

        return $this->_checkout;
    }

    /**
     * Return checkout session object
     *
     * @return Mage_Checkout_Model_Session
     */
    private function _getCheckoutSession()
    {
        return Mage::getSingleton('Mage_Checkout_Model_Session');
    }

    /**
     * Return checkout quote object
     *
     * @return Mage_Sale_Model_Quote
     */
    private function _getQuote()
    {
        if (!$this->_quote) {
            $this->_quote = $this->_getCheckoutSession()->getQuote();
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
        $baseSubtotal = $this->_getCheckoutSession()->getQuote()->getBaseSubtotal();
        $minAmountActive = Mage::getStoreConfig('sales/minimum_order/active');
        $minAmount = $minAmountActive ? Mage::getStoreConfig('sales/minimum_order/amount') : 0;
        if ($baseSubtotal >= $minAmount) {
            return true;
        }
        return false;
    }
}
