<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Paypal\Controller\Express;

/**
 * Abstract Express Checkout Controller
 */
abstract class AbstractExpress extends \Magento\App\Action\Action
    implements  \Magento\Checkout\Controller\ExpressRedirectInterface
{
    /**
     * @var \Magento\Paypal\Model\Express\Checkout
     */
    protected $_checkout;

    /**
     * Internal cache of checkout models
     *
     * @var array
     */
    protected $_checkoutTypes = array();

    /**
     * @var \Magento\Paypal\Model\Config
     */
    protected $_config;

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
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Sales\Model\QuoteFactory
     */
    protected $_quoteFactory;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $_orderFactory;

    /**
     * @var \Magento\Paypal\Model\Express\Checkout\Factory
     */
    protected $_checkoutFactory;

    /**
     * @var \Magento\Session\Generic
     */
    protected $_paypalSession;

    /**
     * @param \Magento\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Sales\Model\QuoteFactory $quoteFactory
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Magento\Paypal\Model\Express\Checkout\Factory $checkoutFactory
     * @param \Magento\Session\Generic $paypalSession
     */
    public function __construct(
        \Magento\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Sales\Model\QuoteFactory $quoteFactory,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Paypal\Model\Express\Checkout\Factory $checkoutFactory,
        \Magento\Session\Generic $paypalSession
    ) {
        $this->_customerSession = $customerSession;
        $this->_quoteFactory = $quoteFactory;
        $this->_checkoutSession = $checkoutSession;
        $this->_orderFactory = $orderFactory;
        $this->_checkoutFactory = $checkoutFactory;
        $this->_paypalSession = $paypalSession;
        parent::__construct($context);
        $parameters = array('params' => array($this->_configMethod));
        $this->_config = $this->_objectManager->create($this->_configType, $parameters);
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

            $customer = $this->_customerSession->getCustomer();
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
                $this->_url->getUrl('checkout/onepage/success'),
                $this->_url->getUrl('paypal/express/cancel'),
                $this->_url->getUrl('checkout/onepage/success')
            );

            $token = $this->_checkout->start(
                $this->_url->getUrl('*/*/return'),
                $this->_url->getUrl('*/*/cancel')
            );
            if ($token && $url = $this->_checkout->getRedirectUrl()) {
                $this->_initToken($token);
                $this->getResponse()->setRedirect($url);
                return;
            }
        } catch (\Magento\Core\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addError(__('We can\'t start Express Checkout.'));
            $this->_objectManager->get('Magento\Logger')->logException($e);
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
            $this->_quote = $this->_quoteFactory->create()->load($quoteId);
            $this->_initCheckout();
            $response = $this->_checkout->getShippingOptionsCallbackResponse($this->getRequest()->getParams());
            $this->getResponse()->setBody($response);
        } catch (\Exception $e) {
            $this->_objectManager->get('Magento\Logger')->logException($e);
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
            $order = ($orderId) ? $this->_orderFactory->create()->load($orderId) : false;
            if ($order && $order->getId() && $order->getQuoteId() == $this->_getCheckoutSession()->getQuoteId()) {
                $order->cancel()->save();
                $this->_getCheckoutSession()
                    ->unsLastQuoteId()
                    ->unsLastSuccessQuoteId()
                    ->unsLastOrderId()
                    ->unsLastRealOrderId();
                $this->messageManager->addSuccess(__('Express Checkout and Order have been canceled.'));
            } else {
                $this->messageManager->addSuccess(__('Express Checkout has been canceled.'));
            }
        } catch (\Magento\Core\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addError(__('Unable to cancel Express Checkout'));
            $this->_objectManager->get('Magento\Logger')->logException($e);
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
        } catch (\Magento\Core\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addError(__('We can\'t process Express Checkout approval.'));
            $this->_objectManager->get('Magento\Logger')->logException($e);
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
            $this->_view->loadLayout();
            $this->_view->getLayout()->initMessages();
            $reviewBlock = $this->_view->getLayout()->getBlock('paypal.express.review');
            $reviewBlock->setQuote($this->_getQuote());
            $reviewBlock->getChildBlock('details')->setQuote($this->_getQuote());
            if ($reviewBlock->getChildBlock('shipping_method')) {
                $reviewBlock->getChildBlock('shipping_method')->setQuote($this->_getQuote());
            }
            $this->_view->renderLayout();
            return;
        } catch (\Magento\Core\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addError(
                __('We can\'t initialize Express Checkout review.')
            );
            $this->_objectManager->get('Magento\Logger')->logException($e);
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
        } catch (\Magento\Core\Exception $e) {
            $this->messageManager->addError($e->getMessage());
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
                $this->_view->loadLayout('paypal_express_review_details');
                $this->getResponse()->setBody($this->_view->getLayout()->getBlock('root')
                    ->setQuote($this->_getQuote())
                    ->toHtml());
                return;
            }
        } catch (\Magento\Core\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addError(__('We can\'t update shipping method.'));
            $this->_objectManager->get('Magento\Logger')->logException($e);
        }
        if ($isAjax) {
            $this->getResponse()->setBody('<script type="text/javascript">window.location.href = '
                . $this->_url->getUrl('*/*/review') . ';</script>');
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
            $this->_view->loadLayout('paypal_express_review');

            $this->getResponse()->setBody($this->_view->getLayout()->getBlock('express.review.shipping.method')
                ->setQuote($this->_getQuote())
                ->toHtml());
            return;
        } catch (\Magento\Core\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addError(__('We can\'t update Order data.'));
            $this->_objectManager->get('Magento\Logger')->logException($e);
        }
        $this->getResponse()->setBody('<script type="text/javascript">window.location.href = '
            . $this->_url->getUrl('*/*/review') . ';</script>');
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
                $this->_view->loadLayout('paypal_express_review_details');
                $this->getResponse()->setBody($this->_view->getLayout()->getBlock('root')
                    ->setQuote($this->_getQuote())
                    ->toHtml());
                return;
            }
        } catch (\Magento\Core\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addError(__('We can\'t update Order data.'));
            $this->_objectManager->get('Magento\Logger')->logException($e);
        }
        if ($isAjax) {
            $this->getResponse()->setBody('<script type="text/javascript">window.location.href = '
                . $this->_url->getUrl('*/*/review') . ';</script>');
        } else {
            $this->_redirect('*/*/review');
        }
    }

    /**
     * Submit the order
     *
     * @throws \Magento\Core\Exception
     */
    public function placeOrderAction()
    {
        try {
            $requiredAgreements = $this->_objectManager->get('Magento\Checkout\Helper\Data')->getRequiredAgreementIds();
            if ($requiredAgreements) {
                $postedAgreements = array_keys($this->getRequest()->getPost('agreement', array()));
                if (array_diff($requiredAgreements, $postedAgreements)) {
                    throw new \Magento\Core\Exception(
                        __('Please agree to all the terms and conditions before placing the order.')
                    );
                }
            }

            $this->_initCheckout();
            $this->_checkout->place($this->_initToken());

            // prepare session to success or cancellation page
            $this->_getCheckoutSession()->clearHelperData();

            // "last successful quote"
            $quoteId = $this->_getQuote()->getId();
            $this->_getCheckoutSession()->setLastQuoteId($quoteId)->setLastSuccessQuoteId($quoteId);

            // an order may be created
            $order = $this->_checkout->getOrder();
            if ($order) {
                $this->_getCheckoutSession()->setLastOrderId($order->getId())
                    ->setLastRealOrderId($order->getIncrementId());
            }

            // recurring profiles may be created along with the order or without it
            $profiles = $this->_checkout->getRecurringPaymentProfiles();
            if ($profiles) {
                $ids = array();
                foreach ($profiles as $profile) {
                    $ids[] = $profile->getId();
                }
                $this->_getCheckoutSession()->setLastRecurringProfileIds($ids);
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
        } catch (\Magento\Core\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addError(__('We can\'t place the order.'));
            $this->_objectManager->get('Magento\Logger')->logException($e);
        }
        $this->_redirect('*/*/review');
    }

    /**
     * Instantiate quote and checkout
     *
     * @throws \Magento\Core\Exception
     */
    private function _initCheckout()
    {
        $quote = $this->_getQuote();
        if (!$quote->hasItems() || $quote->getHasError()) {
            $this->getResponse()->setHeader('HTTP/1.1', '403 Forbidden');
            throw new \Magento\Core\Exception(__('We can\'t initialize Express Checkout.'));
        }
        if (!isset($this->_checkoutTypes[$this->_checkoutType])) {
            $parameters = array(
                'params' => array(
                    'quote' => $quote,
                    'config' => $this->_config,
                ),
            );
            $this->_checkoutTypes[$this->_checkoutType] = $this->_checkoutFactory
                ->create($this->_checkoutType, $parameters);
        }
        $this->_checkout = $this->_checkoutTypes[$this->_checkoutType];
    }

    /**
     * Search for proper checkout token in request or session or (un)set specified one
     * Combined getter/setter
     *
     * @param string $setToken
     * @return \Magento\Paypal\Controller\Express|string
     * @throws \Magento\Core\Exception
     */
    protected function _initToken($setToken = null)
    {
        if (null !== $setToken) {
            if (false === $setToken) {
                // security measure for avoid unsetting token twice
                if (!$this->_getSession()->getExpressCheckoutToken()) {
                    throw new \Magento\Core\Exception(__('PayPal Express Checkout Token does not exist.'));
                }
                $this->_getSession()->unsExpressCheckoutToken();
            } else {
                $this->_getSession()->setExpressCheckoutToken($setToken);
            }
            return $this;
        }
        $setToken = $this->getRequest()->getParam('token');
        if ($setToken) {
            if ($setToken !== $this->_getSession()->getExpressCheckoutToken()) {
                throw new \Magento\Core\Exception(__('A wrong PayPal Express Checkout Token is specified.'));
            }
        } else {
            $setToken = $this->_getSession()->getExpressCheckoutToken();
        }
        return $setToken;
    }

    /**
     * PayPal session instance getter
     *
     * @return \Magento\Session\Generic
     */
    private function _getSession()
    {
        return $this->_paypalSession;
    }

    /**
     * Return checkout session object
     *
     * @return \Magento\Checkout\Model\Session
     */
    private function _getCheckoutSession()
    {
        return $this->_checkoutSession;
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
        $this->_actionFlag->set('', 'no-dispatch', true);
        $this->getResponse()->setRedirect(
            $this->_objectManager->get('Magento\Core\Helper\Url')->addRequestParam(
                $this->_objectManager->get('Magento\Customer\Helper\Data')->getLoginUrl(),
                array('context' => 'checkout')
            )
        );
    }

    /**
     * Does method supports before auth url for redirect
     * @return bool
     */
    public function supportsCustomerBeforeAuthUrl()
    {
        return true;
    }

}
