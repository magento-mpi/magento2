<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Multishipping checkout controller
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Checkout\Controller;

use Magento\App\Action\NotFoundException;
use Magento\App\RequestInterface;

class Multishipping extends \Magento\Checkout\Controller\Action
{
    /**
     * Retrieve checkout model
     *
     * @return \Magento\Checkout\Model\Type\Multishipping
     */
    protected function _getCheckout()
    {
        return $this->_objectManager->get('Magento\Checkout\Model\Type\Multishipping');
    }

    /**
     * Retrieve checkout state model
     *
     * @return \Magento\Checkout\Model\Type\Multishipping\State
     */
    protected function _getState()
    {
        return $this->_objectManager->get('Magento\Checkout\Model\Type\Multishipping\State');
    }

    /**
     * Retrieve checkout url helper
     *
     * @return \Magento\Checkout\Helper\Url
     */
    protected function _getHelper()
    {
        return $this->_objectManager->get('Magento\Checkout\Helper\Url');
    }

    /**
     * Retrieve checkout session
     *
     * @return \Magento\Checkout\Model\Session
     */
    protected function _getCheckoutSession()
    {
        return $this->_objectManager->get('Magento\Checkout\Model\Session');
    }

    /**
     * @var \Magento\Core\Model\Url
     */
    protected $_urlBuilder;


    /**
     * @param \Magento\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Core\Model\Url $urlBuilder
     */
    public function __construct(
        \Magento\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Core\Model\Url $urlBuilder
    ) {
        parent::__construct($context, $customerSession);
        $this->_urlBuilder = $urlBuilder;
    }

    /**
     * Dispatch request
     *
     * @param RequestInterface $request
     * @return \Magento\App\ResponseInterface
     */
    public function dispatch(RequestInterface $request)
    {
        $this->_request = $request;
        if ($this->_actionFlag->get('', 'redirectLogin')) {
            return parent::dispatch($request);
        }

        $action = $request->getActionName();

        $checkoutSessionQuote = $this->_getCheckoutSession()->getQuote();
        /**
         * Catch index action call to set some flags before checkout/type_multishipping model initialization
         */
        if ($action == 'index') {
            $checkoutSessionQuote->setIsMultiShipping(true);
            $this->_getCheckoutSession()->setCheckoutState(
                \Magento\Checkout\Model\Session::CHECKOUT_STATE_BEGIN
            );
        } elseif (!$checkoutSessionQuote->getIsMultiShipping()
            && !in_array($action, array('login', 'register', 'success'))
        ) {
            $this->_redirect('*/*/index');
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
            return parent::dispatch($request);
        }

        if (!in_array($action, array('login', 'register'))) {
            $customerSession = $this->_objectManager->get('Magento\Customer\Model\Session');
            if (!$customerSession->authenticate($this, $this->_getHelper()->getMSLoginUrl())) {
                $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
            }

            if (!$this->_objectManager->get('Magento\Checkout\Helper\Data')->isMultishippingCheckoutAvailable()) {
                $error = $this->_getCheckout()->getMinimumAmountError();
                $this->messageManager->addError($error);
                $this->getResponse()->setRedirect($this->_getHelper()->getCartUrl());
                $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
                return parent::dispatch($request);
            }
        }

        if (!$this->_preDispatchValidateCustomer()) {
            return $this->getResponse();
        }

        if ($this->_getCheckoutSession()->getCartWasUpdated(true)
            && !in_array($action, array('index', 'login', 'register', 'addresses', 'success'))
        ) {
            $this->getResponse()->setRedirect($this->_getHelper()->getCartUrl());
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
            return parent::dispatch($request);
        }

        if ($action == 'success' && $this->_getCheckout()->getCheckoutSession()->getDisplaySuccess(true)) {
            return parent::dispatch($request);
        }

        $quote = $this->_getCheckout()->getQuote();
        if (!$quote->hasItems() || $quote->getHasError() || $quote->isVirtual()) {
            $this->getResponse()->setRedirect($this->_getHelper()->getCartUrl());
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
        }

        return parent::dispatch($request);
    }

    /**
     * Index action of Multishipping checkout
     */
    public function indexAction()
    {
        $this->_getCheckoutSession()->setCartWasUpdated(false);
        $this->_redirect('*/*/addresses');
    }

    /**
     * Multishipping checkout login page
     */
    public function loginAction()
    {
        if ($this->_objectManager->get('Magento\Customer\Model\Session')->isLoggedIn()) {
            $this->_redirect('*/*/');
            return;
        }

        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();

        // set account create url
        $loginForm = $this->_view->getLayout()->getBlock('customer.new');
        if ($loginForm) {
            $loginForm->setCreateAccountUrl($this->_getHelper()->getMSRegisterUrl());
        }
        $this->_view->renderLayout();
    }

    /**
     * Multishipping checkout login page
     */
    public function registerAction()
    {
        if ($this->_objectManager->get('Magento\Customer\Model\Session')->isLoggedIn()) {
            $this->getResponse()->setRedirect($this->_getHelper()->getMSCheckoutUrl());
            return;
        }

        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();

        $registerForm = $this->_view->getLayout()->getBlock('customer_form_register');
        if ($registerForm) {
            $registerForm->setShowAddressFields(true)
                ->setBackUrl($this->_getHelper()->getMSLoginUrl())
                ->setSuccessUrl($this->_getHelper()->getMSShippingAddressSavedUrl())
                ->setErrorUrl($this->_urlBuilder->getCurrentUrl());
        }

        $this->_view->renderLayout();
    }

    /**
     * Multishipping checkout select address page
     */
    public function addressesAction()
    {
        // If customer do not have addresses
        if (!$this->_getCheckout()->getCustomerDefaultShippingAddress()) {
            $this->_redirect('*/multishipping_address/newShipping');
            return;
        }

        $this->_getState()->unsCompleteStep(
            \Magento\Checkout\Model\Type\Multishipping\State::STEP_SHIPPING
        );

        $this->_getState()->setActiveStep(
            \Magento\Checkout\Model\Type\Multishipping\State::STEP_SELECT_ADDRESSES
        );
        if (!$this->_getCheckout()->validateMinimumAmount()) {
            $message = $this->_getCheckout()->getMinimumAmountDescription();
            $this->messageManager->addNotice($message);
        }
        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $this->_view->renderLayout();
    }

    /**
     * Multishipping checkout process posted addresses
     */
    public function addressesPostAction()
    {
        if (!$this->_getCheckout()->getCustomerDefaultShippingAddress()) {
            $this->_redirect('*/multishipping_address/newShipping');
            return;
        }
        try {
            if ($this->getRequest()->getParam('continue', false)) {
                $this->_getCheckout()->setCollectRatesFlag(true);
                $this->_getState()->setActiveStep(
                    \Magento\Checkout\Model\Type\Multishipping\State::STEP_SHIPPING
                );
                $this->_getState()->setCompleteStep(
                    \Magento\Checkout\Model\Type\Multishipping\State::STEP_SELECT_ADDRESSES
                );
                $this->_redirect('*/*/shipping');
            } elseif ($this->getRequest()->getParam('new_address')) {
                $this->_redirect('*/multishipping_address/newShipping');
            } else {
                $this->_redirect('*/*/addresses');
            }
            if ($shipToInfo = $this->getRequest()->getPost('ship')) {
                $this->_getCheckout()->setShippingItemsInformation($shipToInfo);
            }
        } catch (\Magento\Core\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            $this->_redirect('*/*/addresses');
        } catch (\Exception $e) {
            $this->messageManager->addException(
                $e,
                __('Data saving problem')
            );
            $this->_redirect('*/*/addresses');
        }
    }

    public function backToAddressesAction()
    {
        $this->_getState()->setActiveStep(
            \Magento\Checkout\Model\Type\Multishipping\State::STEP_SELECT_ADDRESSES
        );
        $this->_getState()->unsCompleteStep(
            \Magento\Checkout\Model\Type\Multishipping\State::STEP_SHIPPING
        );
        $this->_redirect('*/*/addresses');
    }

    /**
     * Multishipping checkout remove item action
     */
    public function removeItemAction()
    {
        $itemId     = $this->getRequest()->getParam('id');
        $addressId  = $this->getRequest()->getParam('address');
        if ($addressId && $itemId) {
            $this->_getCheckout()->setCollectRatesFlag(true);
            $this->_getCheckout()->removeAddressItem($addressId, $itemId);
        }
        $this->_redirect('*/*/addresses');
    }

    /**
     * Validate minimum amount
     *
     * @return bool
     */
    protected function _validateMinimumAmount()
    {
        if (!$this->_getCheckout()->validateMinimumAmount()) {
            $error = $this->_getCheckout()->getMinimumAmountError();
            $this->messageManager->addError($error);
            $this->_forward('backToAddresses');
            return false;
        }
        return true;
    }

    /**
     * Multishipping checkout shipping information page
     */
    public function shippingAction()
    {
        if (!$this->_validateMinimumAmount()) {
            return;
        }

        if (!$this->_getState()->getCompleteStep(\Magento\Checkout\Model\Type\Multishipping\State::STEP_SELECT_ADDRESSES)) {
            return $this->_redirect('*/*/addresses');
        }

        $this->_getState()->setActiveStep(
            \Magento\Checkout\Model\Type\Multishipping\State::STEP_SHIPPING
        );
        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $this->_view->renderLayout();
    }

    public function backToShippingAction()
    {
        $this->_getState()->setActiveStep(
            \Magento\Checkout\Model\Type\Multishipping\State::STEP_SHIPPING
        );
        $this->_getState()->unsCompleteStep(
            \Magento\Checkout\Model\Type\Multishipping\State::STEP_BILLING
        );
        $this->_redirect('*/*/shipping');
    }

    public function shippingPostAction()
    {
        $shippingMethods = $this->getRequest()->getPost('shipping_method');
        try {
            $this->_eventManager->dispatch(
                'checkout_controller_multishipping_shipping_post',
                array('request'=>$this->getRequest(), 'quote'=>$this->_getCheckout()->getQuote())
            );
            $this->_getCheckout()->setShippingMethods($shippingMethods);
            $this->_getState()->setActiveStep(
                \Magento\Checkout\Model\Type\Multishipping\State::STEP_BILLING
            );
            $this->_getState()->setCompleteStep(
                \Magento\Checkout\Model\Type\Multishipping\State::STEP_SHIPPING
            );
            $this->_redirect('*/*/billing');
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            $this->_redirect('*/*/shipping');
        }
    }

    /**
     * Multishipping checkout billing information page
     */
    public function billingAction()
    {
        if (!$this->_validateBilling()) {
            return;
        }

        if (!$this->_validateMinimumAmount()) {
            return;
        }

        if (!$this->_getState()->getCompleteStep(\Magento\Checkout\Model\Type\Multishipping\State::STEP_SHIPPING)) {
            return $this->_redirect('*/*/shipping');
        }

        $this->_getState()->setActiveStep(
            \Magento\Checkout\Model\Type\Multishipping\State::STEP_BILLING
        );

        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $this->_view->renderLayout();
    }

    /**
     * Validation of selecting of billing address
     *
     * @return boolean
     */
    protected function _validateBilling()
    {
        if (!$this->_getCheckout()->getQuote()->getBillingAddress()->getFirstname()) {
            $this->_redirect('*/multishipping_address/selectBilling');
            return false;
        }
        return true;
    }

    /**
     * Back to billing action
     */
    public function backToBillingAction()
    {
        $this->_getState()->setActiveStep(
            \Magento\Checkout\Model\Type\Multishipping\State::STEP_BILLING
        );
        $this->_getState()->unsCompleteStep(
            \Magento\Checkout\Model\Type\Multishipping\State::STEP_OVERVIEW
        );
        $this->_redirect('*/*/billing');
    }

    /**
     * Multishipping checkout place order page
     */
    public function overviewAction()
    {
        if (!$this->_validateMinimumAmount()) {
            return;
        }

        $this->_getState()->setActiveStep(\Magento\Checkout\Model\Type\Multishipping\State::STEP_OVERVIEW);

        try {
            $payment = $this->getRequest()->getPost('payment', array());
            $payment['checks'] = \Magento\Payment\Model\Method\AbstractMethod::CHECK_USE_FOR_MULTISHIPPING
                | \Magento\Payment\Model\Method\AbstractMethod::CHECK_USE_FOR_COUNTRY
                | \Magento\Payment\Model\Method\AbstractMethod::CHECK_USE_FOR_CURRENCY
                | \Magento\Payment\Model\Method\AbstractMethod::CHECK_ORDER_TOTAL_MIN_MAX
                | \Magento\Payment\Model\Method\AbstractMethod::CHECK_ZERO_TOTAL;
            $this->_getCheckout()->setPaymentMethod($payment);

            $this->_getState()->setCompleteStep(
                \Magento\Checkout\Model\Type\Multishipping\State::STEP_BILLING
            );

            $this->_view->loadLayout();
            $this->_view->getLayout()->initMessages();
            $this->_view->renderLayout();
        } catch (\Magento\Core\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            $this->_redirect('*/*/billing');
        } catch (\Exception $e) {
            $this->_objectManager->get('Magento\Logger')->logException($e);
            $this->messageManager->addException($e, __('We cannot open the overview page.'));
            $this->_redirect('*/*/billing');
        }
    }

    /**
     * Overview action
     */
    public function overviewPostAction()
    {
        if (!$this->_validateMinimumAmount()) {
            return;
        }

        try {
            $requiredAgreements = $this->_objectManager->get('Magento\Checkout\Helper\Data')->getRequiredAgreementIds();
            if ($requiredAgreements) {
                $postedAgreements = array_keys($this->getRequest()->getPost('agreement', array()));
                $diff = array_diff($requiredAgreements, $postedAgreements);
                if ($diff) {
                    $this->messageManager->addError(
                        __('Please agree to all Terms and Conditions before placing the order.')
                    );
                    $this->_redirect('*/*/billing');
                    return;
                }
            }

            $payment = $this->getRequest()->getPost('payment');
            $paymentInstance = $this->_getCheckout()->getQuote()->getPayment();
            if (isset($payment['cc_number'])) {
                $paymentInstance->setCcNumber($payment['cc_number']);
            }
            if (isset($payment['cc_cid'])) {
                $paymentInstance->setCcCid($payment['cc_cid']);
            }
            $this->_getCheckout()->createOrders();
            $this->_getState()->setActiveStep(
                \Magento\Checkout\Model\Type\Multishipping\State::STEP_SUCCESS
            );
            $this->_getState()->setCompleteStep(
                \Magento\Checkout\Model\Type\Multishipping\State::STEP_OVERVIEW
            );
            $this->_getCheckout()->getCheckoutSession()->clearQuote();
            $this->_getCheckout()->getCheckoutSession()->setDisplaySuccess(true);
            $this->_redirect('*/*/success');
        } catch (\Magento\Payment\Model\Info\Exception $e) {
            $message = $e->getMessage();
            if (!empty($message)) {
                $this->messageManager->addError($message);
            }
            $this->_redirect('*/*/billing');
        } catch (\Magento\Checkout\Exception $e) {
            $this->_objectManager->get('Magento\Checkout\Helper\Data')
                ->sendPaymentFailedEmail($this->_getCheckout()->getQuote(), $e->getMessage(), 'multi-shipping');
            $this->_getCheckout()->getCheckoutSession()->clearQuote();
            $this->messageManager->addError($e->getMessage());
            $this->_redirect('*/cart');
        } catch (\Magento\Core\Exception $e) {
            $this->_objectManager->get('Magento\Checkout\Helper\Data')
                ->sendPaymentFailedEmail($this->_getCheckout()->getQuote(), $e->getMessage(), 'multi-shipping');
            $this->messageManager->addError($e->getMessage());
            $this->_redirect('*/*/billing');
        } catch (\Exception $e) {
            $this->_objectManager->get('Magento\Logger')->logException($e);
            $this->_objectManager->get('Magento\Checkout\Helper\Data')
                ->sendPaymentFailedEmail($this->_getCheckout()->getQuote(), $e->getMessage(), 'multi-shipping');
            $this->messageManager->addError(__('Order place error'));
            $this->_redirect('*/*/billing');
        }
    }

    /**
     * Multishipping checkout success page
     */
    public function successAction()
    {
        if (!$this->_getState()->getCompleteStep(\Magento\Checkout\Model\Type\Multishipping\State::STEP_OVERVIEW)) {
            $this->_redirect('*/*/addresses');
            return;
        }

        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $ids = $this->_getCheckout()->getOrderIds();
        $this->_eventManager->dispatch('checkout_multishipping_controller_success_action', array('order_ids' => $ids));
        $this->_view->renderLayout();
    }

    /**
     * Redirect to login page
     *
     */
    public function redirectLogin()
    {
        $this->_actionFlag->set('', 'no-dispatch', true);
        $url = $this->_objectManager->create('Magento\UrlInterface')
            ->getUrl('*/*', array('_secure' => true));
        $this->_objectManager->get('Magento\Customer\Model\Session')->setBeforeAuthUrl($url);

        $this->getResponse()->setRedirect(
            $this->_objectManager->get('Magento\Core\Helper\Url')->addRequestParam(
                $this->_getHelper()->getMSLoginUrl(),
                array('context' => 'checkout')
            )
        );

        $this->_actionFlag->set('', 'redirectLogin', true);
    }
}
