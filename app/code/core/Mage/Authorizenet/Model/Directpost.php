<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Authorizenet
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Authorize.net DirectPost payment method model.
 *
 * @category   Mage
 * @package    Mage_Authorizenet
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Authorizenet_Model_Directpost extends Mage_Paygate_Model_Authorizenet
{
    protected $_code  = 'authorizenet_directpost';
    protected $_formBlockType = 'directpost/form';
    protected $_infoBlockType = 'directpost/info';

    protected static $_createOrderBefore;

    /**
     * Availability options
     */
    protected $_canAuthorize            = true;
    protected $_canCapture              = true;
    protected $_canCapturePartial       = false;
    protected $_canRefund               = false;
    protected $_canRefundInvoicePartial = false;
    protected $_canVoid                 = true;
    protected $_canUseInternal          = true;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = false;
    protected $_canSaveCc               = false;
    protected $_isInitializeNeeded      = true;

    protected static $_response;

    /**
     * (non-PHPdoc)
     * @see app/code/core/Mage/Payment/Model/Method/Mage_Payment_Model_Method_Cc#validate()
     */
    public function validate()
    {
        return true;
    }

    /**
     * Return the value of setting 'create_order_before'
     *
     * @return int
     */
    protected function _getCreateOrderBefore()
    {
        if (!isset(self::$_createOrderBefore)){
            self::$_createOrderBefore = $this->getConfigData('create_order_before');
        }
        return self::$_createOrderBefore;
    }

    /**
     * Set the value of setting 'create_order_before' to static variable (need to operate with quote)
     *
     * @param int $createOrderBefore
     * @return Mage_Authorizenet_Model_Directpost
     */
    protected function _setCreateOrderBefore($createOrderBefore)
    {
        self::$_createOrderBefore = $createOrderBefore;
        return $this;
    }

    /**
     * Send authorize request to gateway
     *
     * @param  Varien_Object $payment
     * @param  decimal $amount
     * @return Mage_Paygate_Model_Authorizenet
     * @throws Mage_Core_Exception
     */
    public function authorize(Varien_Object $payment, $amount)
    {
        $payment->setAdditionalInformation('payment_type', $this->getConfigData('payment_action'));
        if (!$this->_getCreateOrderBefore()){
            $response = $this->getResponse();
            if ($response->getXTransId() && $response->isApproved()){
                $payment->setTransactionId($response->getXTransId())
                    ->setParentTransactionId(null)
                    ->setIsTransactionClosed(0)
                    ->setTransactionAdditionalInfo($this->_realTransactionIdKay, $response->getXTransId());
            }
        }
    }

    /**
     * Get CGI url
     *
     * @return string
     */
    public function getCgiUrl()
    {
        $uri = $this->getConfigData('cgi_url');
        return $uri ? $uri : self::CGI_URL;
    }

    /**
     * Return request model for form data building
     *
     * @return Mage_Authorizenet_Model_Directpost_Request
     */
    public function getRequestModel()
    {
        return Mage::getModel('authorizenet/directpost_request');
    }

    /**
     * Return response.
     *
     * @return Mage_Authorizenet_Model_Directpost_Response
     */
    public function getResponse()
    {
        if (!self::$_response){
            self::$_response = Mage::getModel('authorizenet/directpost_response');
        }
        return self::$_response;

    }

    /**
     *  Return Order Place Redirect URL.
     *  Need to prevent emails sending for incomplete orders.
     *
     *  @return string 1
     */
    public function getOrderPlaceRedirectUrl()
    {
        return 1;
    }

    /**
     * Instantiate state and set it to state object
     *
     * @param string $paymentAction
     * @param Varien_Object
     */
    public function initialize($paymentAction, $stateObject)
    {
        switch ($paymentAction){
            case self::ACTION_AUTHORIZE:
            case self::ACTION_AUTHORIZE_CAPTURE:
                $payment = $this->getInfoInstance();
                $order = $payment->getOrder();
                $payment->authorize(true, $order->getBaseTotalDue()); // base amount will be set inside
                $payment->setAmountAuthorized($order->getTotalDue());
                if ($this->_getCreateOrderBefore()){
                    $order->setState(Mage_Sales_Model_Order::STATE_PENDING_PAYMENT, 'pending_payment', '', false);

                    $stateObject->setState(Mage_Sales_Model_Order::STATE_PENDING_PAYMENT);
                    $stateObject->setStatus('pending_payment');
                    $stateObject->setIsNotified(false);
                }
                break;
            default:
                break;
        }
    }

    /**
     * Generate request object and fill its fields from Order object
     *
     * @param Mage_Sales_Model_Order $order
     * @return Mage_Authorizenet_Model_Directpost_Request
     */
    public function generateRequestFromOrder(Mage_Sales_Model_Order $order)
    {
        $request = $this->getRequestModel();
        $request->setConstantData($this)
            ->setDataFromEntity($order, $this)
            ->signRequestData();
        $this->_debug(array('request' => $request->getData()));

        return $request;
    }

    /**
     * Generate request object and fill its fields from Quote object
     *
     * @param Mage_Sales_Model_Quote $quote
     * @return Mage_Authorizenet_Model_Directpost_Request
     */
    public function generateRequestFromQuote(Mage_Sales_Model_Quote $quote)
    {
        $request = $this->getRequestModel();
        $request->setConstantData($this)
            ->setDataFromEntity($quote, $this)
            ->signRequestData();
        $this->_debug(array('request' => $request->getData()));

        return $request;
    }

    /**
     * Fill response with data.
     *
     * @param array $postData
     * @return Mage_Authorizenet_Model_Directpost
     */
    public function setResponseData(array $postData)
    {
        $this->getResponse()->setData($postData);
        return $this;
    }

    /**
     * Validate response data. Needed in controllers.
     *
     * @return bool true in case of validation success.
     * @throws Mage_Core_Exception in case of validation error
     */
    public function validateResponse()
    {
        $response = $this->getResponse();
        //md5 check
        if (!$response->isValidHash($this->getConfigData('trans_md5'), $this->getConfigData('login'))){
            Mage::throwException(Mage::helper('authorizenet')->__('Response hash validation failed. Transaction declined.'));
        }

        if (!$response->getXTransId()){
            Mage::throwException(Mage::helper('authorizenet')->__('Payment authorization error.'));
        }

        return true;
    }

    /**
     * Operate with order using data from $_POST which came from authorize.net by Relay URL.
     *
     * @param array $responseData data from Authorize.net from $_POST
     * @throws Mage_Core_Exception in case of validation error or order creation error
     */
    public function process(array $responseData)
    {
        $debugData = array(
            'response' => $responseData
        );
        $this->_debug($debugData);

        $this->setResponseData($responseData);

        //check MD5 error or others response errors
        //throws exception on false.
        $this->validateResponse();

        $response = $this->getResponse();
        //operate with order
        $orderIncrementId = $response->getXInvoiceNum();
        $responseText = $this->_wrapGatewayError($response->getXResponseReasonText());
        if ($orderIncrementId){
            if ($response->getCreateOrderBefore()){
                /* @var $order Mage_Sales_Model_Order */
                $order = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);
                if ($order->getId()){
                    //operate with order
                    $this->_authOrder($order);
                }
                else {
                    Mage::throwException(
                        ($responseText && !$response->isApproved()) ?
                        $responseText :
                        Mage::helper('authorizenet')->__('Payment error. Order was not found.')
                    );
                }
            }
            else {
                $quote = Mage::getModel('sales/quote')->load($orderIncrementId, 'reserved_order_id');
                if ($quote->getId()){
                    //operate with quote
                    $this->_authQuote($quote);
                }
                else {
                    Mage::throwException(
                        ($responseText && !$response->isApproved()) ?
                        $responseText :
                        Mage::helper('authorizenet')->__('Payment error. Quote was not found.')
                    );
                }
            }
        }
        else {
            Mage::throwException(
                ($responseText && !$response->isApproved()) ?
                $responseText :
                Mage::helper('authorizenet')->__('Payment error. Order was not found.')
            );
        }
    }

    /**
     * Check response code came from authorize.net.
     *
     * @return true in case of Approved response
     * @throws Mage_Core_Exception in case of Declined or Error response from Authorize.net
     */
    public function checkResponseCode()
    {
        switch ($this->getResponse()->getXResponseCode()) {
            case self::RESPONSE_CODE_APPROVED:
                return true;
            case self::RESPONSE_CODE_DECLINED:
            case self::RESPONSE_CODE_ERROR:
                Mage::throwException($this->_wrapGatewayError($this->getResponse()->getXResponseReasonText()));
            default:
                Mage::throwException(Mage::helper('authorizenet')->__('Payment authorization error.'));
        }
    }

    /**
     * Operate with order using information from Authorize.net.
     * Authorize order or authorize and capture it.
     *
     * @param Mage_Sales_Model_Order $order
     */
    protected function _authOrder(Mage_Sales_Model_Order $order)
    {
        try {
            $this->checkResponseCode();
        }
        catch (Exception $e){
            //decline the order (in case of wrong response code) but don't return money to customer.
            $message = $e->getMessage();
            $this->_declineOrder($order, $message, false);
            throw $e;
        }

        $response = $this->getResponse();

        //create transaction. need for void if amount will not match.
        $payment = $order->getPayment();
        $payment->setTransactionId($response->getXTransId())
            ->setIsTransactionClosed(0)
            ->setTransactionAdditionalInfo($this->_realTransactionIdKay, $response->getXTransId());


        $payment->addTransaction(Mage_Sales_Model_Order_Payment_Transaction::TYPE_AUTH);

        // Set transaction apporval message
        $message = Mage::helper('authorizenet')->__(
            'Amount of %s approved by payment gateway. Transaction ID: "%s".',
            $order->getBaseCurrency()->formatTxt($payment->getBaseAmountAuthorized()),
            $response->getXTransId()
        );

        $order->setState(Mage_Sales_Model_Order::STATE_NEW, true, $message, false)
            ->save();

        //match amounts. should be equals for authorization.
        //decline the order if amount does not match.
        if (sprintf('%.2F', $payment->getBaseAmountAuthorized()) != sprintf('%.2F', $response->getXAmount())){
            $message = Mage::helper('authorizenet')->__('Payment error. Paid amount doesn\'t match the order amount.');
            $this->_declineOrder($order, $message, true);
            Mage::throwException($message);
        }


        //capture order using AIM if needed
        if ($payment->getAdditionalInformation('payment_type') == self::ACTION_AUTHORIZE_CAPTURE) {
            try {
                $payment->setTransactionId(null)
                    ->setParentTransactionId($response->getXTransId())
                    ->capture(null);
                $order->save();
            }
            catch (Exception $e){
                Mage::logException($e);
                //if we couldn't capture order, just leave it as NEW order.
            }
        }

        try {
            if (!$response->hasOrderSendConfirmation() || $response->getOrderSendConfirmation()){
                $order->sendNewOrderEmail();
            }

            Mage::getModel('sales/quote')
                ->load($order->getQuoteId())
                ->setIsActive(false)
                ->save();
        }
        // do not cancel order if we couldn't send email
        catch (Exception $e) {}
    }

    /**
     * Operate with quote to create new order.
     *
     * @param Mage_Sales_Model_Quote $quote
     */
    protected function _authQuote(Mage_Sales_Model_Quote $quote)
    {
        $this->checkResponseCode();

        $response = $this->getResponse();

        try {
            $quote->collectTotals()->save();
        }
        catch (Exception $e){
            $this->_declineQuote($quote);
            throw $e;
        }


        //match amounts. should be equals for authorization.
        //decline the quote if amount does not match.
        if (sprintf('%.2F', $quote->getBaseGrandTotal()) != sprintf('%.2F', $response->getXAmount())){
            $message = Mage::helper('authorizenet')->__('Payment error. Paid amount doesn\'t match the order amount.');
            $this->_declineQuote($quote);
            Mage::throwException($message);
        }

        //auth order
        /* @var $service Mage_Sales_Model_Service_Quote */
        $service = Mage::getModel('sales/service_quote', $quote);
        $createOrderBefore = $this->_getCreateOrderBefore();
        $this->_setCreateOrderBefore($response->getCreateOrderBefore());
        try {
            $service->submitAll();
            $this->_setCreateOrderBefore($createOrderBefore);
            $order = $service->getOrder();
            $payment = $order->getPayment();

            //set additional result if needed
            $result['last_order_id'] = $order->getId();
            $result['last_real_order_id'] = $order->getIncrementId();
            $result['last_success_quote_id'] = $quote->getId();
            $result['last_quote_id'] = $quote->getId();

            // as well a billing agreement can be created
            $agreement = $payment->getBillingAgreement();
            if ($agreement) {
                $result['last_billing_agreement_id'] = $agreement->getId();
            }

            // add recurring profiles information to the session
            $profiles = $service->getRecurringPaymentProfiles();
            if ($profiles) {
                $ids = array();
                foreach ($profiles as $profile) {
                    $ids[] = $profile->getId();
                }
                $result['last_recurring_profile_ids'] = $ids;
                // TODO: send recurring profile emails
            }

            $quotePayment = $quote->getPayment();
            $quotePayment->setAdditionalInformation('session_data', $result);
            $quote->save();
        }
        catch (Exception $e){
            $this->_setCreateOrderBefore($createOrderBefore);
            $this->_declineQuote($quote);
            throw $e;
        }

        //capture order using AIM if needed
        if ($payment->getAdditionalInformation('payment_type') == self::ACTION_AUTHORIZE_CAPTURE) {
            try {
                $payment->setTransactionId(null)
                    ->setParentTransactionId($response->getXTransId())
                    ->capture(null);
                $order->save();
            }
            catch (Exception $e){
                Mage::logException($e);
                //if we couldn't capture order, just leave it as NEW order.
            }
        }

        try {
            if (!$response->hasOrderSendConfirmation() || $response->getOrderSendConfirmation()){
                $order->sendNewOrderEmail();
            }
        }
        // do not cancel order if we couldn't send email
        catch (Exception $e) {}
    }

    /**
     * Register order cancellation. Return money to customer if needed.
     *
     * @param Mage_Sales_Model_Order $order
     * @param string $message
     * @param bool $voidPayment
     */
    protected function _declineOrder(Mage_Sales_Model_Order $order, $message = '', $voidPayment = true)
    {
        try {
            $response = $this->getResponse();
            if ($voidPayment && $response->getXTransId() && strtoupper($response->getXType()) == 'AUTH_ONLY'){
                $order->getPayment()
                    ->setTransactionId(null)
                    ->setParentTransactionId($response->getXTransId())
                    ->void();
            }
            $order->registerCancellation($message)
                ->save();
        }
        catch (Exception $e){
            //quiet decline
            Mage::logException($e);
        }
    }

    /**
     * Return money to customer by quote.
     *
     * @param Mage_Sales_Model_Quote $quote
     */
    protected function _declineQuote(Mage_Sales_Model_Quote $quote)
    {
        try {
            $response = $this->getResponse();
            if ($response->getXTransId() && strtoupper($response->getXType()) == 'AUTH_ONLY'){
                $payment = new Varien_Object();
                $payment->setAnetTransType(self::REQUEST_TYPE_VOID);
                $payment->setXTransId($response->getXTransId());
                $pseudoOrder = new Varien_Object();
                $pseudoOrder->setStoreId($quote->getStoreId());
                $payment->setOrder($pseudoOrder);

                $request = $this->_buildRequest($payment);
                $result = $this->_postRequest($request);

                switch ($result->getResponseCode()) {
                    case self::RESPONSE_CODE_APPROVED:
                        return;
                    case self::RESPONSE_CODE_DECLINED:
                    case self::RESPONSE_CODE_ERROR:
                        Mage::throwException($this->_wrapGatewayError($result->getResponseReasonText()));
                    default:
                        Mage::throwException(Mage::helper('paygate')->__('Payment voiding error.'));
                }
            }
        }
        catch (Exception $e){
            //quiet decline
            Mage::logException($e);
        }
    }
}
