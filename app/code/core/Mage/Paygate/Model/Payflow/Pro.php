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
 * @category   Mage
 * @package    Mage_Paygate
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Payflow Pro payment gateway model
 *
 * @category    Mage
 * @package     Mage_Paygate
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Paygate_Model_Payflow_Pro extends  Mage_Payment_Model_Method_Cc
{
    const TRXTYPE_AUTH_ONLY         = 'A';
    const TRXTYPE_SALE              = 'S';
    const TRXTYPE_CREDIT            = 'C';
    const TRXTYPE_DELAYED_CAPTURE   = 'D';
    const TRXTYPE_DELAYED_VOID      = 'V';
    const TRXTYPE_DELAYED_VOICE     = 'F';
    const TRXTYPE_DELAYED_INQUIRY   = 'I';

    const TENDER_AUTOMATED          = 'A';
    const TENDER_CC                 = 'C';
    const TENDER_PINLESS_DEBIT      = 'D';
    const TENDER_ECHEK              = 'E';
    const TENDER_TELECHECK          = 'K';
    const TENDER_PAYPAL             = 'P';

    const RESPONSE_DELIM_CHAR = ',';

    protected $_clientTimeout = 45;

    const RESPONSE_CODE_APPROVED = 0;
    const RESPONSE_CODE_DECLINED = 12;
    const RESPONSE_CODE_CAPTURE_ERROR = 111;

    protected $_code = 'verisign';

    /*
    * 3 = Authorisation approved
    * 6 = Settlement pending (transaction is scheduled to be settled)
    * 9 =  Authorisation captured
    */
    protected $_validVoidTransState = array(3,6,9);

    public function authorize(Mage_Payment_Model_Info $payment, $amount)
    {
        if($amount>0){
            $payment->setTrxtype(self::TRXTYPE_AUTH_ONLY);
            $payment->setAmount($amount);
            $request = $this->buildRequest($payment);
            $result = $this->postRequest($request);
            $payment->setCcTransId($result->getPnref());
            switch ($result->getResultCode()){
                case self::RESPONSE_CODE_APPROVED:
                     $payment->setStatus('APPROVED');
                     break;
                default:
                    Mage::throwException($result->getRespmsg()?$result->getRespmsg():Mage::helper('paygate')->__('Error in authorizing the payment'));
                break;
            }
        }else{
            Mage::throwException(Mage::helper('paygate')->__('Invalid amount for authorization'));
        }
        return $this;
    }

    public function capture(Mage_Payment_Model_Info $payment, $amount)
    {
        if($payment->getCcTransId()){
             $payment->setTrxtype(self::TRXTYPE_DELAYED_CAPTURE);
             $request = $this->buildBasicRequest($payment);
             if($amount>0){
                 $request->setAmt($amount);
             }
             $result = $this->postRequest($request);
             if($result->getResultCode()!=self::RESPONSE_CODE_APPROVED){
                 /*
                 *capture error: either transaction is not an authorization or
                 *              an attemp to capture an authorization transaction that has already been captures
                 * payflow: only one delayed capture transaction is allower per authorization.
                            so need to use sale transaction
                 */
                 if($result->getResultCode()==self::RESPONSE_CODE_CAPTURE_ERROR){
                    $this->sale($payment,$amount);
                 }else{
                    Mage::throwException($result->getRespmsg()?$result->getRespmsg():Mage::helper('paygate')->__('Error in capturing the payment'));
                 }
             }else{
                 $payment->setStatus('APPROVED');
                 $payment->setCcTransId($result->getPnref());
             }
        }else{
             Mage::throwException(Mage::helper('paygate')->__('Invalid transaction to capture'));
        }
        return $this;
    }

    public function sale(Mage_Payment_Model_Info $payment,$amount)
    {
        if($payment->getCcTransId()){
            $payment->setTrxtype(self::TRXTYPE_SALE);
            $request = $this->buildBasicRequest($payment);
            $request->setAmt($amount);
            $result = $this->postRequest($request);
            if($result->getResultCode()!=self::RESPONSE_CODE_APPROVED){
                Mage::throwException($result->getRespmsg()?$result->getRespmsg():Mage::helper('paygate')->__('Error in submitting the payment'));
            }else{
               $payment->setStatus('APPROVED');
               $payment->setCcTransId($result->getPnref());
            }
        }else{
            Mage::throwException(Mage::helper('paygate')->__('Invalid transaction to submit a sale transaction'));
        }

    }

/*    public function onOrderValidate(Mage_Sales_Model_Order_Payment $payment)
    {
        #$payment->setTrxtype(self::TRXTYPE_AUTH_ONLY);
        $payment->setTrxtype(Mage::getStoreConfig('payment/verisign/payment_action'));
        $payment->setDocument($payment->getOrder());

        $request = $this->buildRequest($payment);
        $result = $this->postRequest($request);

        $payment->setCcTransId($result->getPnref());

        if (Mage::getStoreConfig('payment/verisign/debug')) {
            $payment->setCcDebugRequestBody($result->getRequestBody())
                ->setCcDebugResponseSerialized(serialize($result));
        }

        switch ($result->getResultCode()) {
            case self::RESPONSE_CODE_APPROVED:
                $payment->setStatus('APPROVED');
                #$payment->getOrder()->addStatusToHistory(Mage::getStoreConfig('payment/verisign/order_status'));
                break;

            case self::RESPONSE_CODE_DECLINED:
                $payment->setStatus('DECLINED');
                $payment->setStatusDescription($result->getRespmsg());
                break;

            default:
                $payment->setStatus('UNKNOWN');
                $payment->setStatusDescription($result->getRespmsg());
                break;
        }

        return $this;
    }
*/
    public function postRequest(Varien_Object $request)
    {
        if ($this->getConfigData('debug')) {
            foreach( $request->getData() as $key => $value ) {
                $requestData[] = strtoupper($key) . '=' . $value;
            }

            $requestData = join('&', $requestData);

            $debug = Mage::getModel('paygate/authorizenet_debug')
                ->setRequestBody($requestData)
                ->setRequestSerialized(serialize($request->getData()))
                ->setRequestDump(print_r($request->getData(),1))
                ->save();
        }

        $client = new Varien_Http_Client();

        $uri = $this->getConfigData('url');
        $client->setUri($uri)
               ->setConfig(array(
                    'maxredirects'=>5,
                    'timeout'=>30,
                ))
            ->setMethod(Zend_Http_Client::POST)
            ->setParameterPost($request->getData())
            ->setHeaders('X-VPS-VIT-CLIENT-CERTIFICATION-ID: 33baf5893fc2123d8b191d2d011b7fdc')
            ->setHeaders('X-VPS-Request-ID: ' . $request->getRequestId())
            ->setHeaders('X-VPS-CLIENT-TIMEOUT: ' . $this->_clientTimeout)
        ;

        $response = $client->request();

        $result = Mage::getModel('paygate/payflow_pro_result');

        $response = strstr($response->getBody(), 'RESULT');
        $valArray = explode('&', $response);

        foreach($valArray as $val) {
                $valArray2 = explode('=', $val);
                $result->setData(strtolower($valArray2[0]), $valArray2[1]);
        }

        $result->setResultCode($result->getResult())
                ->setRespmsg($result->getRespmsg());

        if (!empty($debug)) {
            $debug
                ->setResponseBody($response)
                ->setResultSerialized(serialize($result->getData()))
                ->setResultDump(print_r($result->getData(),1))
                ->save();
        }

        return $result;
    }

    public function buildRequest(Varien_Object $payment)
    {
        $document = $payment->getDocument();

        if( !$payment->getTrxtype() ) {
            $payment->setTrxtype(self::TRXTYPE_AUTH_ONLY);
        }

        if( !$payment->getTender() ) {
            $payment->setTender(self::TENDER_CC);
        }

        $request = Mage::getModel('paygate/payflow_pro_request')
            ->setUser($this->getConfigData('user'))
            ->setVendor($this->getConfigData('vendor'))
            ->setPartner($this->getConfigData('partner'))
            ->setPwd($this->getConfigData('pwd'))
            ->setTender($payment->getTender())
            ->setTrxtype($payment->getTrxtype())
            ->setVerbosity($this->getConfigData('verbosity'))
            ->setRequestId($this->_generateRequestId())
            ;

        if($payment->getAmount()){
            $request->setAmt(round($payment->getAmount(),2));
        }

        switch ($request->getTender()) {
            case self::TENDER_CC:
                    if($payment->getCcNumber()){
                        $request->setAcct($payment->getCcNumber())
                            ->setExpdate(sprintf('%02d%04d', $payment->getCcExpMonth(), $payment->getCcExpYear()))
                            ->setCvv2($payment->getCcCid());
                    }
                break;
        }
        return $request;
    }

    protected function _generateRequestId()
    {
        return md5(microtime() . rand(0, time()));
    }

     /**
      * buildBasicRequest
      *
      * @author Lindy Kyaw <lindy@varien.com>
      * @access public
      * @return object which was set with all basic required information
      */
    public function buildBasicRequest(Varien_Object $payment)
    {
        if( !$payment->getTender() ) {
            $payment->setTender(self::TENDER_CC);
        }

        $request = Mage::getModel('paygate/payflow_pro_request')
                ->setUser($this->getConfigData('user'))
                ->setVendor($this->getConfigData('vendor'))
                ->setPartner($this->getConfigData('partner'))
                ->setPwd($this->getConfigData('pwd'))
                ->setTender($payment->getTender())
                ->setTrxtype($payment->getTrxtype())
                ->setVerbosity($this->getConfigData('verbosity'))
                ->setRequestId($this->_generateRequestId())
                ->setOrigid($payment->getCcTransId());
        return $request;
    }

    /**
      * canVoid
      *
      * @author Lindy Kyaw <lindy@varien.com>
      * @access public
      * @param string $payment Mage_Payment_Model_Info object
      * @return Mage_Payment_Model_Abstract
      * @desc checking the transaction id is valid or not and transction id was not settled
      */
    public function canVoid(Mage_Payment_Model_Info $payment)
    {
        if($payment->getCcTransId()){
            $payment->setTrxtype(self::TRXTYPE_DELAYED_INQUIRY);

            $request=$this->buildBasicRequest($payment);
            $result = $this->postRequest($request);
            if ($this->getConfigData('debug')) {
              $payment->setCcDebugRequestBody($result->getRequestBody())
                ->setCcDebugResponseSerialized(serialize($result));
            }

            if($result->getResultCode()==self::RESPONSE_CODE_APPROVED){
                if($result->getTransstate()>1000){
                    $payment->setStatus(self::STATUS_ERROR);
                    $payment->setStatusDescription(Mage::helper('paygate')->__('Voided transaction'));
                }elseif(in_array($result->getTransstate(),$this->_validVoidTransState)){
                     $payment->setStatus(self::STATUS_VOID);
                }
            }else{
                $payment->setStatus(self::STATUS_ERROR);
                $payment->setStatusDescription($result->getRespmsg()?
                    $result->getRespmsg():
                    Mage::helper('paygate')->__('Error in retreiving the transaction'));
            }
        }else{
            $payment->setStatus(self::STATUS_ERROR);
            $payment->setStatusDescription(Mage::helper('paygate')->__('Invalid transaction id'));
        }

        return $this;
    }

     /**
      * void
      *
      * @author Lindy Kyaw <lindy@varien.com>
      * @access public
      * @param string $payment Mage_Payment_Model_Info object
      * @return Mage_Payment_Model_Abstract
      */
    public function void(Mage_Payment_Model_Info $payment)
    {
         if($payment->getCcTransId()){
            $payment->setTrxtype(self::TRXTYPE_DELAYED_VOID);

            $request=$this->buildBasicRequest($payment);

            $result = $this->postRequest($request);

            if ($this->getConfigData('debug')) {
              $payment->setCcDebugRequestBody($result->getRequestBody())
                ->setCcDebugResponseSerialized(serialize($result));
            }
            if($result->getResultCode()==self::RESPONSE_CODE_APPROVED){
                 $payment->setStatus(self::STATUS_SUCCESS);
                 $payment->setCcTransId($result->getPnref());
            }else{
                $payment->setStatus(self::STATUS_ERROR);
                $payment->setStatusDescription($result->getRespmsg());
            }

         }else{
            $payment->setStatus(self::STATUS_ERROR);
            $payment->setStatusDescription(Mage::helper('paygate')->__('Invalid transaction id'));
        }

        return $this;

    }

    /**
     * Check refund availability
     * @desc overiding the parent abstract
     * @return bool
     */
    public function canRefund()
    {
        return true;
    }


     /**
      * refund the amount with transaction id
      *
      * @author Lindy Kyaw <lindy@varien.com>
      * @access public
      * @param string $payment Mage_Payment_Model_Info object
      * @return Mage_Payment_Model_Abstract
      */
    public function refund(Mage_Payment_Model_Info $payment)
    {
        if(($payment->getCcTransId() && $payment->getAmount()>0)){
            $payment->setTrxtype(self::TRXTYPE_CREDIT);

            $request=$this->buildBasicRequest($payment);

            $request->setAmt(round($payment->getAmount(),2));

            $result = $this->postRequest($request);

            if ($this->getConfigData('debug')) {
              $payment->setCcDebugRequestBody($result->getRequestBody())
                ->setCcDebugResponseSerialized(serialize($result));
            }
            if($result->getResultCode()==self::RESPONSE_CODE_APPROVED){
                 $payment->setStatus(self::STATUS_SUCCESS);
                 $payment->setCcTransId($result->getPnref());
            }else{
                $payment->setStatus(self::STATUS_ERROR);
                $payment->setStatusDescription($result->getRespmsg()?
                    $result->getRespmsg():
                    Mage::helper('paygate')->__('Error in refunding the payment.'));
            }
        }else{
            $payment->setStatus(self::STATUS_ERROR);
            $payment->setStatusDescription(Mage::helper('paygate')->__('Error in refunding the payment'));
        }

        return $this;

    }

    /**
     * Retrieve payment system relation flag
     *
     * @return bool
     */
    public function isSystem()
    {
        return true;
    }
}