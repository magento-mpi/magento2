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
        
    protected $_response;
    
    /**
     * (non-PHPdoc)
     * @see app/code/core/Mage/Payment/Model/Method/Mage_Payment_Model_Method_Cc#validate()
     */
    public function validate()
    {
        return true;
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
        if (!$this->_response){
            $this->_response = Mage::getModel('authorizenet/directpost_response');
        }
        return $this->_response;
        
    }
    
    /**
     *  Return Order Place Redirect URL.
     *  Need to prevent emails sending for incomplete orders to store's directors.
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
                $order->setState(Mage_Sales_Model_Order::STATE_PENDING_PAYMENT, 'pending_payment', '', false);
                
                $stateObject->setState(Mage_Sales_Model_Order::STATE_PENDING_PAYMENT);
                $stateObject->setStatus('pending_payment');
                $stateObject->setIsNotified(false);
                break;
            default:
                break;
        }
    }
    
    /**
     * Generate request object and fill its fields from Quote object
     *
     * @param Mage_Sales_Model_Order $order
     * @return Mage_Authorizenet_Model_Directpost_Request
     */
    public function generateRequestFromOrder(Mage_Sales_Model_Order $order)
    {
        $request = $this->getRequestModel();
        $request->setConstantData($this)
            ->setDataFromOrder($order, $this)
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
            Mage::throwException(Mage::helper('directpost')->__('Response hash validation failed. Transaction declined.'));
        }
        
        if (!$response->getXTransId()){
            Mage::throwException(Mage::helper('directpost')->__('Payment authorization error.'));
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
       
        $authResponse = $this->getResponse();
        //operate with order
        $orderIncrementId = $authResponse->getXInvoiceNum();
        $responseText = $this->_wrapGatewayError($authResponse->getXResponseReasonText());
        if ($orderIncrementId){
            /* @var $order Mage_Sales_Model_Order */
            $order = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);
            if ($order->getId()){
                //operate with order
                //check amount
                $this->_authOrder($order);
            }
            else {
                Mage::throwException(($responseText) ? $responseText : Mage::helper('authorizenet')->__('Payment error. Order was not found.'));
            }
        }
        else {
            Mage::throwException(($responseText) ? $responseText : Mage::helper('authorizenet')->__('Payment error. Order was not found.'));
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
            $this->_decline($order, $message, false);
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
            $this->_decline($order, $message, true);
            Mage::throwException($message);
        }

        //capture order using AIM if needed
        if ($payment->getAdditionalInformation('payment_type') == self::ACTION_AUTHORIZE_CAPTURE) {
            $payment->setTransactionId(null)
                ->setParentTransactionId($response->getXTransId())
                ->capture(null);
            $order->save();
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
     * Register order cancellation. Return money to customer if needed.
     *
     * @param Mage_Sales_Model_Order $order
     * @param string $message
     * @param bool $voidPayment
     */
    protected function _decline(Mage_Sales_Model_Order $order, $message = '', $voidPayment = true)
    {
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
}
