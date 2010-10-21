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
 * @package     Mage_DirectPayment
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_DirectPayment_Model_Authorizenet extends Mage_Paygate_Model_Authorizenet
{
    protected $_code  = 'directpayment';
    protected $_formBlockType = 'directpayment/form';
    protected $_infoBlockType = 'directpayment/info';
    
    /**
     * Availability options
     */
    protected $_canUseInternal          = true;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = false;
    protected $_canSaveCc = false;
    protected $_isInitializeNeeded      = true;
    
    // no need to debug
    protected $_debugReplacePrivateDataKeys = array();
    
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
     * @return Mage_DirectPayment_Model_Authorizenet_Request
     */
    public function getRequestModel()
    {
        return Mage::getModel('directpayment/authorizenet_request');
    }
    
    /**
     * Return response.
     *
     * @return Mage_DirectPayment_Model_Authorizenet_Response
     */
    public function getResponse()
    {
        if (!$this->_response){
            $this->_response = Mage::getModel('directpayment/authorizenet_response');
        }
        return $this->_response;
        
    }
    
    /**
     *  Return Order Place Redirect URL.
     *  Need to prevent emails sending for new orders to store's directors.
     *
     *  @return      string 1
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
     * @return Mage_DirectPayment_Model_Authorizenet_Request
     */
    public function generateRequestFromOrder(Mage_Sales_Model_Order $order)
    {
        $request = $this->getRequestModel();
        $request->setConstantData($this)
            ->setDataFromOrder($order)
            ->signRequestData();
        return $request;
    }
    
    /**
     * Fill response with data.
     *
     * @param array $postData
     * @return Mage_DirectPayment_Model_Authorizenet
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
            Mage::throwException(Mage::helper('directpayment')->__('Response hash validation failed. Transaction declined.'));
        }
        
        if (!$response->getXTransId()){
            Mage::throwException(Mage::helper('paygate')->__('Payment authorization error.'));
        }
        
        return true;
    }
    
    public function process(array $responseData)
    {
        $this->setResponseData($responseData);
        
        Mage::log($responseData);
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
                try {
                    $this->_authOrder($order);
                }
                catch (Exception $e){
                    if ($authResponse->getXTransId() && $authResponse->getXType() == 'AUTH_ONLY'){
                        $order->getPayment()->void();
                        $order->registerCancellation()
                            ->save();
                    }
                    throw $e;
                }
            }
            else {
                Mage::throwException(($responseText) ? $responseText : Mage::helper('directpayment')->__('Payment error. Order was not found.'));
            }
        }
        else {
            Mage::throwException(($responseText) ? $responseText : Mage::helper('directpayment')->__('Payment error. Order was not found.'));
        }
    }
    
    public function checkResponseCode()
    {
        switch ($this->getResponse()->getXResponseCode()) {
            case self::RESPONSE_CODE_APPROVED:
                return true;
            case self::RESPONSE_CODE_DECLINED:
            case self::RESPONSE_CODE_ERROR:
                Mage::throwException($this->_wrapGatewayError($this->getResponse()->getXResponseReasonText()));
            default:
                Mage::throwException(Mage::helper('paygate')->__('Payment authorization error.'));
        }
    }
    
    protected function _authOrder(Mage_Sales_Model_Order $order)
    {
        $this->checkResponseCode();
        
        $response = $this->getResponse();
        
        $payment = $order->getPayment();
        //match amounts. should be equals for authorization.
        if (sprintf('%.2F', $payment->getBaseAmountAuthorized()) != sprintf('%.2F', $response->getXAmount())){
            Mage::throwException(Mage::helper('directpayment')->__('Payment error. Paid amount doesn\'t match the order amount.'));
        }
        
        $payment->setTransactionId($response->getXTransId())
            ->setIsTransactionClosed(0)
            ->setTransactionAdditionalInfo($this->_realTransactionIdKay, $response->getXTransId());
            
        
        $payment->addTransaction(Mage_Sales_Model_Order_Payment_Transaction::TYPE_AUTH);
        
        // Set transaction apporval message
        $message = Mage::helper('directpayment')->__(
            'Amount of %s approved by payment gateway. Transaction ID: "%s".',
            $order->getBaseCurrency()->formatTxt($payment->getBaseAmountAuthorized()),
            $response->getXTransId()
        );
        
        $order->setState(Mage_Sales_Model_Order::STATE_NEW, true, $message, true)
            ->save();

        if ($payment->getAdditionalInformation('payment_type') == self::ACTION_AUTHORIZE_CAPTURE) {
            $payment->setParentTransactionId($response->getXTransId())
                ->capture(null);
            $order->save();
        }

        try {
            $order->sendNewOrderEmail();
            
            Mage::getModel('sales/quote')
                ->load($order->getQuoteId())
                ->setIsActive(false)
                ->save();
        }
        // do not cancel order if we couldn't send email
        catch (Exception $e) {}
    }
}