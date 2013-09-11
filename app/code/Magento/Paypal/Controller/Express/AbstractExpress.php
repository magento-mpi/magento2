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
 * Abstract Express Checkout Controller
 */
namespace Magento\Paypal\Controller\Express;

abstract class AbstractExpress extends \Magento\Core\Controller\Front\Action
{
    /**
     * @var \Magento\Paypal\Model\Express\Checkout
     */
    protected $_checkout = null;

    /**
     * Internal cache of checkout models
     *
     * @var array
     */
    protected $_checkoutTypes = array();

    /**
     * @var \Magento\Paypal\Model\Config
     */
    protected $_config = null;

    /**
     * @var \Magento\Sales\Model\Quote
     */
    protected $_quote = false;

    /**
     * Config mode type
     *
     * @var string
     */
    protected $_configType;

    /**
     * Config method type
     *
     * @var string
     */
    protected $_configMethod;

    /**
     * Checkout mode type
     *
     * @var string
     */
    protected $_checkoutType;

    /**
     * Instantiate config
     */
    protected function _construct()
    {
        parent::_construct();
        $parameters = array('params' => array($this->_configMethod));
        $this->_config = \Mage::getModel($this->_configType, $parameters);
    }

    /**
     * Start Express Checkout by requesting initial token and dispatching customer to PayPal
     */
    public function startAction()
    {
        try {
            $this->_initCheckout();

            if ($this->_getQuote()->getIsMultiShipping()) {
                $this->_getQuote()->setIsMultiShipping(false);
                $this->_getQuote()->removeAllAddresses();
            }

            $customer = \Mage::getSingleton('Magento\Customer\Model\Session')->getCustomer();
            if ($customer && $customer->getId()) {
                $this->_checkout->setCustomerWithAddressChange(
                    $customer, $this->_getQuote()->getBillingAddress(), $this->_getQuote()->getShippingAddress()
                );
            }

            // billing agreement
            $isBARequested = (bool)$this->getRequest()
                ->getParam(\Magento\Paypal\Model\Express\Checkout::PAYMENT_INFO_TRANSPORT_BILLING_AGREEMENT);
            if ($customer && $customer->getId()) {
                $this->_checkout->setIsBillingAgreementRequested($isBARequested);
            }

            // giropay
            $this->_checkout->prepareGiropayUrls(
                \Mage::getUrl('checkout/onepage/success'),
                \Mage::getUrl('paypal/express/cancel'),
                \Mage::getUrl('checkout/onepage/success')
            );

            $token = $this->_checkout->start(\Mage::getUrl('*/*/return'), \Mage::getUrl('*/*/cancel'));
            if ($token && $url = $this->_checkout->getRedirectUrl()) {
                $this->_initToken($token);
                $this->getResponse()->setRedirect($url);
                return;
            }
        } catch (\Magento\Core\Exception $e) {
            $this->_getCheckoutSession()->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->_getCheckoutSession()->addError(__('We can\'t start Express Checkout.'));
            \Mage::logException($e);
        }

        $this->_redirect('checkout/cart');
    }

    /**
     * Return shipping options items for shipping address from request
     */
    public function shippingOptionsCallbackAction()
    {
        try {
            $quoteId = $this->getRequest()->getParam('quote_id');
            $this->_quote = \Mage::getModel('Magento\Sales\Model\Quote')->load($quoteId);
            $this->_initCheckout();
            $response = $this->_checkout->getShippingOptionsCallbackResponse($this->getRequest()->getParams());
            $this->getResponse()->setBody($response);
        } catch (\Exception $e) {
            \Mage::logException($e);
        }
    }

    /**
     * Cancel Express Checkout
     */
    public function cancelAction()
    {
        try {
            $this->_initToken(false);
            // TODO verify if this logic of order cancellation is deprecated
            // if there is an order - cancel it
            $orderId = $this->_getCheckoutSession()->getLastOrderId();
            $order = ($orderId) ? \Mage::getModel('Magento\Sales\Model\Order')->load($orderId) : false;
            if ($order && $order->getId() && $order->getQuoteId() == $this->_getCheckoutSession()->getQuoteId()) {
                $order->cancel()->save();
                $this->_getCheckoutSession()
                    ->unsLastQuoteId()
                    ->unsLastSuccessQuoteId()
                    ->unsLastOrderId()
                    ->unsLastRealOrderId()
                    ->addSuccess(__('Express Checkout and Order have been canceled.'))
                ;
            } else {
                $this->_getCheckoutSession()->addSuccess(__('Express Checkout has been canceled.'));
            }
        } catch (\Magento\Core\Exception $e) {
            $this->_getCheckoutSession()->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->_getCheckoutSession()->addError(__('Unable to cancel Express Checkout'));
            \Mage::logException($e);
        }

        $this->_redirect('checkout/cart');
    }

    /**
     * Return from PayPal and dispatch customer to order review page
     */
    public function returnAction()
    {
        try {
            $this->_initCheckout();
            $this->_checkout->returnFromPaypal($this->_initToken());
            $this->_redirect('*/*/review');
            return;
        }
        catch (\Magento\Core\Exception $e) {
            \Mage::getSingleton('Magento\Checkout\Model\Session')->addError($e->getMessage());
        }
        catch (\Exception $e) {
            \Mage::getSingleton('Magento\Checkout\Model\Session')->addError(__('We can\'t process Express Checkout approval.'));
            \Mage::logException($e);
        }
        $this->_redirect('checkout/cart');
    }

    /**
     * Review order after returning from PayPal
     */
    public function reviewAction()
    {
        try {
            $this->_initCheckout();
            $this->_checkout->prepareOrderReview($this->_initToken());
            $this->loadLayout();
            $this->_initLayoutMessages('Magento_Paypal_Model_Session');
            $reviewBlock = $this->getLayout()->getBlock('paypal.express.review');
            $reviewBlock->setQuote($this->_getQuote());
            $reviewBlock->getChildBlock('details')->setQuote($this->_getQuote());
            if ($reviewBlock->getChildBlock('shipping_method')) {
                $reviewBlock->getChildBlock('shipping_method')->setQuote($this->_getQuote());
            }
            $this->renderLayout();
            return;
        }
        catch (\Magento\Core\Exception $e) {
            \Mage::getSingleton('Magento\Checkout\Model\Session')->addError($e->getMessage());
        }
        catch (\Exception $e) {
            \Mage::getSingleton('Magento\Checkout\Model\Session')->addError(
                __('We can\'t initialize Express Checkout review.')
            );
            \Mage::logException($e);
        }
        $this->_redirect('checkout/cart');
    }

    /**
     * Dispatch customer back to PayPal for editing payment information
     */
    public function editAction()
    {
        try {
            $this->getResponse()->setRedirect($this->_config->getExpressCheckoutEditUrl($this->_initToken()));
        }
        catch (\Magento\Core\Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $this->_redirect('*/*/review');
        }
    }

    /**
     * Update shipping method (combined action for ajax and regular request)
     */
    public function saveShippingMethodAction()
    {
        try {
            $isAjax = $this->getRequest()->getParam('isAjax');
            $this->_initCheckout();
            $this->_checkout->updateShippingMethod($this->getRequest()->getParam('shipping_method'));
            if ($isAjax) {
                $this->loadLayout('paypal_express_review_details');
                $this->getResponse()->setBody($this->getLayout()->getBlock('root')
                    ->setQuote($this->_getQuote())
                    ->toHtml());
                return;
            }
        } catch (\Magento\Core\Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->_getSession()->addError(__('We can\'t update shipping method.'));
            \Mage::logException($e);
        }
        if ($isAjax) {
            $this->getResponse()->setBody('<script type="text/javascript">window.location.href = '
                . \Mage::getUrl('*/*/review') . ';</script>');
        } else {
            $this->_redirect('*/*/review');
        }
    }

    /**
     * Update Order (combined action for ajax and regular request)
     */
    public function updateShippingMethodsAction()
    {
        try {
            $this->_initCheckout();
            $this->_checkout->prepareOrderReview($this->_initToken());
            $this->loadLayout('paypal_express_review');

            $this->getResponse()->setBody($this->getLayout()->getBlock('express.review.shipping.method')
                ->setQuote($this->_getQuote())
                ->toHtml());
            return;
        } catch (\Magento\Core\Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->_getSession()->addError(__('We can\'t update Order data.'));
            \Mage::logException($e);
        }
        $this->getResponse()->setBody('<script type="text/javascript">window.location.href = '
            . \Mage::getUrl('*/*/review') . ';</script>');
    }

    /**
     * Update Order (combined action for ajax and regular request)
     */
    public function updateOrderAction()
    {
        try {
            $isAjax = $this->getRequest()->getParam('isAjax');
            $this->_initCheckout();
            $this->_checkout->updateOrder($this->getRequest()->getParams());
            if ($isAjax) {
                $this->loadLayout('paypal_express_review_details');
                $this->getResponse()->setBody($this->getLayout()->getBlock('root')
                    ->setQuote($this->_getQuote())
                    ->toHtml());
                return;
            }
        } catch (\Magento\Core\Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->_getSession()->addError(__('We can\'t update Order data.'));
            \Mage::logException($e);
        }
        if ($isAjax) {
            $this->getResponse()->setBody('<script type="text/javascript">window.location.href = '
                . \Mage::getUrl('*/*/review') . ';</script>');
        } else {
            $this->_redirect('*/*/review');
        }
    }

    /**
     * Submit the order
     */
    public function placeOrderAction()
    {
        try {
            $requiredAgreements = \Mage::helper('Magento\Checkout\Helper\Data')->getRequiredAgreementIds();
            if ($requiredAgreements) {
                $postedAgreements = array_keys($this->getRequest()->getPost('agreement', array()));
                if (array_diff($requiredAgreements, $postedAgreements)) {
                    \Mage::throwException(__('Please agree to all the terms and conditions before placing the order.'));
                }
            }

            $this->_initCheckout();
            $this->_checkout->place($this->_initToken());

            // prepare session to success or cancellation page
            $session = $this->_getCheckoutSession();
            $session->clearHelperData();

            // "last successful quote"
            $quoteId = $this->_getQuote()->getId();
            $session->setLastQuoteId($quoteId)->setLastSuccessQuoteId($quoteId);

            // an order may be created
            $order = $this->_checkout->getOrder();
            if ($order) {
                $session->setLastOrderId($order->getId())
                    ->setLastRealOrderId($order->getIncrementId());
                // as well a billing agreement can be created
                $agreement = $this->_checkout->getBillingAgreement();
                if ($agreement) {
                    $session->setLastBillingAgreementId($agreement->getId());
                }
            }

            // recurring profiles may be created along with the order or without it
            $profiles = $this->_checkout->getRecurringPaymentProfiles();
            if ($profiles) {
                $ids = array();
                foreach($profiles as $profile) {
                    $ids[] = $profile->getId();
                }
                $session->setLastRecurringProfileIds($ids);
            }

            // redirect if PayPal specified some URL (for example, to Giropay bank)
            $url = $this->_checkout->getRedirectUrl();
            if ($url) {
                $this->getResponse()->setRedirect($url);
                return;
            }
            $this->_initToken(false); // no need in token anymore
            $this->_redirect('checkout/onepage/success');
            return;
        }
        catch (\Magento\Core\Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }
        catch (\Exception $e) {
            $this->_getSession()->addError(__('We can\'t place the order.'));
            \Mage::logException($e);
        }
        $this->_redirect('*/*/review');
    }

    /**
     * Instantiate quote and checkout
     * @throws \Magento\Core\Exception
     */
    private function _initCheckout()
    {
        $quote = $this->_getQuote();
        if (!$quote->hasItems() || $quote->getHasError()) {
            $this->getResponse()->setHeader('HTTP/1.1','403 Forbidden');
            \Mage::throwException(__('We can\'t initialize Express Checkout.'));
        }
        if (false === isset($this->_checkoutTypes[$this->_checkoutType])) {
            $parameters = array(
                'params' => array(
                    'quote' => $quote,
                    'config' => $this->_config,
                ),
            );
            $this->_checkoutTypes[$this->_checkoutType] = \Mage::getModel($this->_checkoutType, $parameters);
        }
        $this->_checkout = $this->_checkoutTypes[$this->_checkoutType];
    }

    /**
     * Search for proper checkout token in request or session or (un)set specified one
     * Combined getter/setter
     *
     * @param string $setToken
     * @return \Magento\Paypal\Controller\Express|string
     */
    protected function _initToken($setToken = null)
    {
        if (null !== $setToken) {
            if (false === $setToken) {
                // security measure for avoid unsetting token twice
                if (!$this->_getSession()->getExpressCheckoutToken()) {
                    \Mage::throwException(__('PayPal Express Checkout Token does not exist.'));
                }
                $this->_getSession()->unsExpressCheckoutToken();
            } else {
                $this->_getSession()->setExpressCheckoutToken($setToken);
            }
            return $this;
        }
        if ($setToken = $this->getRequest()->getParam('token')) {
            if ($setToken !== $this->_getSession()->getExpressCheckoutToken()) {
                \Mage::throwException(__('A wrong PayPal Express Checkout Token is specified.'));
            }
        } else {
            $setToken = $this->_getSession()->getExpressCheckoutToken();
        }
        return $setToken;
    }

    /**
     * PayPal session instance getter
     *
     * @return \Magento\Core\Model\Session\Generic
     */
    private function _getSession()
    {
        return \Mage::getSingleton('Magento_Paypal_Model_Session');
    }

    /**
     * Return checkout session object
     *
     * @return \Magento\Checkout\Model\Session
     */
    private function _getCheckoutSession()
    {
        return \Mage::getSingleton('Magento\Checkout\Model\Session');
    }

    /**
     * Return checkout quote object
     *
     * @return \Magento\Sales\Model\Quote
     */
    private function _getQuote()
    {
        if (!$this->_quote) {
            $this->_quote = $this->_getCheckoutSession()->getQuote();
        }
        return $this->_quote;
    }

    /**
     * Redirect to login page
     *
     */
    public function redirectLogin()
    {
        $this->setFlag('', 'no-dispatch', true);
        $this->getResponse()->setRedirect(
            \Mage::helper('Magento\Core\Helper\Url')->addRequestParam(
                \Mage::helper('Magento\Customer\Helper\Data')->getLoginUrl(),
                array('context' => 'checkout')
            )
        );
    }
}
