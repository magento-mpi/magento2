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
 * @package     Mage_Paypal
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Payflow Pro payment gateway model
 *
 * @category    Mage
 * @package     Mage_Paypal
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Paypal_Model_Payflowpro extends  Mage_Payment_Model_Method_Cc
{
    /**
     * Transaction action codes
     */
    const TRXTYPE_AUTH_ONLY         = 'A';
    const TRXTYPE_SALE              = 'S';
    const TRXTYPE_CREDIT            = 'C';
    const TRXTYPE_DELAYED_CAPTURE   = 'D';
    const TRXTYPE_DELAYED_VOID      = 'V';
    const TRXTYPE_DELAYED_VOICE     = 'F';
    const TRXTYPE_DELAYED_INQUIRY   = 'I';

    /**
     * Tender type codes
     */
    const TENDER_CC = 'C';

    /**
     * Gateway request URLs
     */
    const TRANSACTION_URL           = 'https://payflowpro.paypal.com/transaction';
    const TRANSACTION_URL_TEST_MODE = 'https://pilot-payflowpro.paypal.com/transaction';

    /**
     * Response codes
     */
    const RESPONSE_CODE_APPROVED            = 0;
    const RESPONSE_CODE_FRAUDSERVICE_FILTER = 126;
    const RESPONSE_CODE_DECLINED            = 12;
    const RESPONSE_CODE_CAPTURE_ERROR       = 111;

    /**
     * Payment method code
     */
    protected $_code = Mage_Paypal_Model_Config::METHOD_PAYFLOWPRO;

    /**
     * Availability options
     */
    protected $_isGateway               = true;
    protected $_canAuthorize            = true;
    protected $_canCapture              = true;
    protected $_canCapturePartial       = false;
    protected $_canRefund               = true;
    protected $_canVoid                 = true;
    protected $_canUseInternal          = true;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = true;
    protected $_canSaveCc = false;
    protected $_isProxy = false;

    /**
     * Gateway request timeout
     */
    protected $_clientTimeout = 45;

    /**
     * Fields that should be replaced in debug with '***'
     *
     * @var array
     */
    protected $_debugReplacePrivateDataKeys = array('user', 'pwd', 'acct', 'expdate', 'cvv2');

    /**
     * Centinel cardinal fields map
     *
     * @var string
     */
    protected $_centinelFieldMap = array(
        'centinel_mpivendor'    => 'MPIVENDOR3DS',
        'centinel_authstatus'   => 'AUTHSTATUS3DS',
        'centinel_cavv'         => 'CAVV',
        'centinel_eci'          => 'ECI',
        'centinel_xid'          => 'XID',
    );

    /**
     * Check whether payment method can be used
     *
     * @param Mage_Sales_Model_Quote
     * @return bool
     */
    public function isAvailable($quote = null)
    {
        $storeId = Mage::app()->getStore($this->getStore())->getId();
        $config = Mage::getModel('paypal/config')->setStoreId($storeId);
        if ($config->isMethodAvailable($this->getCode()) && parent::isAvailable($quote)) {
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
        $value = null;
        switch ($field)
        {
            case 'url':
                $value = $this->_getTransactionUrl();
                break;
            default:
                $value = parent::getConfigData($field, $storeId);
        }
        return $value;
    }

    /**
     * Payment action getter compatible with payment model
     *
     * @see Mage_Sales_Model_Payment::place()
     * @return string
     */
    public function getConfigPaymentAction()
    {
        switch ($this->getConfigData('payment_action')) {
            case Mage_Paypal_Model_Config::PAYMENT_ACTION_AUTH:
                return Mage_Payment_Model_Method_Abstract::ACTION_AUTHORIZE;
            case Mage_Paypal_Model_Config::PAYMENT_ACTION_SALE:
                return Mage_Payment_Model_Method_Abstract::ACTION_AUTHORIZE_CAPTURE;
        }
    }

    /**
     * Authorize payment
     *
     * @param Mage_Sales_Model_Order_Payment $payment
     * @return Mage_Paypal_Model_Payflowpro
     */
    public function authorize(Varien_Object $payment, $amount)
    {
        $request = $this->_buildPlaceRequest($payment, $amount);
        $request->setTrxtype(self::TRXTYPE_AUTH_ONLY);
        $response = $this->_postRequest($request);
        $this->_processErrors($response);

        switch ($response->getResultCode()){
            case self::RESPONSE_CODE_APPROVED:
                $payment->setTransactionId($response->getPnref())->setIsTransactionClosed(0);
                break;
            case self::RESPONSE_CODE_FRAUDSERVICE_FILTER:
                $payment->setTransactionId($response->getPnref())->setIsTransactionClosed(0);
                $payment->setIsTransactionPending(true);
                $payment->setIsFraudDetected(true);
                break;
        }
        return $this;
    }

    /**
     * Capture payment
     *
     * @param Mage_Sales_Model_Order_Payment $payment
     * @return Mage_Paypal_Model_Payflowpro
     */
    public function capture(Varien_Object $payment, $amount)
    {
        if ($payment->getParentTransactionId()) {
            $request = $this->_buildManageRequest();
            $request->setTrxtype(self::TRXTYPE_DELAYED_CAPTURE);
            $request->setOrigid($payment->getParentTransactionId());
        } else {
            $request = $this->_buildPlaceRequest($payment, $amount);
            $request->setTrxtype(self::TRXTYPE_SALE);
        }

        $response = $this->_postRequest($request);
        $this->_processErrors($response);

        switch ($response->getResultCode()){
            case self::RESPONSE_CODE_APPROVED:
                $payment->setTransactionId($response->getPnref())->setIsTransactionClosed(0);
                break;
            case self::RESPONSE_CODE_FRAUDSERVICE_FILTER:
                $payment->setTransactionId($response->getPnref())->setIsTransactionClosed(0);
                $payment->setIsTransactionPending(true);
                $payment->setIsFraudDetected(true);
                break;
        }
        return $this;
    }

    /**
     * Void payment
     *
     * @param Mage_Sales_Model_Order_Payment $payment
     * @return Mage_Paypal_Model_Payflowpro
     */
    public function void(Varien_Object $payment)
    {
        $request = $this->_buildManageRequest();
        $request->setTrxtype(self::TRXTYPE_DELAYED_VOID);
        $request->setOrigid($payment->getParentTransactionId());
        $response = $this->_postRequest($request);
        $this->_processErrors($response);

        if ($response->getResultCode() == self::RESPONSE_CODE_APPROVED){
            $payment->setTransactionId($response->getPnref())
                ->setIsTransactionClosed(1)
                ->setShouldCloseParentTransaction(1);
        }

        return $this;
    }

    /**
     * Refund capture
     *
     * @param Mage_Sales_Model_Order_Payment $payment
     * @return Mage_Paypal_Model_Payflowpro
     */
    public function refund(Varien_Object $payment, $amount)
    {
        $request = $this->_buildManageRequest();
        $request->setTrxtype(self::TRXTYPE_CREDIT);
        $request->setOrigid($payment->getParentTransactionId());
        $response = $this->_postRequest($request);
        $this->_processErrors($response);

        if ($response->getResultCode() == self::RESPONSE_CODE_APPROVED){
            $payment->setTransactionId($response->getPnref())
                ->setIsTransactionClosed(1);
        }
        return $this;
    }

    /**
     * Getter for URL to perform Payflow requests, based on test mode by default
     *
     * @param bool $testMode Ability to specify test mode using
     * @return string
     */
    protected function _getTransactionUrl($testMode = null)
    {
        $testMode = is_null($testMode) ? $this->getConfigData('sandbox_flag') : (bool)$testMode;
        if ($testMode) {
            return self::TRANSACTION_URL_TEST_MODE;
        }
        return self::TRANSACTION_URL;
    }

    /**
     * Post request to gateway and return response
     *
     * @param Varien_Object $request
     * @return Varien_Object
     */
    protected function _postRequest(Varien_Object $request)
    {
        $debugData = array('request' => $request->getData());

        $client = new Varien_Http_Client();
        $result = $this->_getResultObject();

        $_config = array(
                        'maxredirects'=>5,
                        'timeout'=>30,
                    );

        $_isProxy = $this->getConfigData('use_proxy', false);
        if($_isProxy){
            $_config['proxy'] = $this->getConfigData('proxy_host') . ':' . $this->getConfigData('proxy_port');//http://proxy.shr.secureserver.net:3128',
            $_config['httpproxytunnel'] = true;
            $_config['proxytype'] = CURLPROXY_HTTP;
        }

        $uri = $this->getConfigData('url');
        $client->setUri($uri)
            ->setConfig($_config)
            ->setMethod(Zend_Http_Client::POST)
            ->setParameterPost($request->getData())
            ->setHeaders('X-VPS-VIT-CLIENT-CERTIFICATION-ID: 33baf5893fc2123d8b191d2d011b7fdc')
            ->setHeaders('X-VPS-Request-ID: ' . $request->getRequestId())
            ->setHeaders('X-VPS-CLIENT-TIMEOUT: ' . $this->_clientTimeout);

        try {
           /**
            * we are sending request to payflow pro without url encoding
            * so we set up _urlEncodeBody flag to false
            */
            $response = $client->setUrlEncodeBody(false)->request();
        }
        catch (Exception $e) {
            $result->setResponseCode(-1)
                ->setResponseReasonCode($e->getCode())
                ->setResponseReasonText($e->getMessage());

            $debugData['result'] = $result->getData();
            $this->_debug($debugData);
            throw $e;
        }



        $response = strstr($response->getBody(), 'RESULT');
        $valArray = explode('&', $response);

        foreach($valArray as $val) {
                $valArray2 = explode('=', $val);
                $result->setData(strtolower($valArray2[0]), $valArray2[1]);
        }

        $result->setResultCode($result->getResult())
                ->setRespmsg($result->getRespmsg());

        $debugData['result'] = $result->getData();
        $this->_debug($debugData);

        return $result;
    }

     /**
      * Return request object with information for manage transaction request
      *
      * @return Varien_Object
      */
    protected function _buildManageRequest()
    {
        return $this->_buildBasicRequest($payment);
    }

     /**
      * Return request object with information for 'authorization' or 'sale' action
      *
      * @param Mage_Sales_Model_Order_Payment $payment
      * @param float $amount
      * @return Varien_Object
      */
    protected function _buildPlaceRequest(Varien_Object $payment, $amount)
    {
        $request = $this->_buildBasicRequest($payment);
        $request->setAmt(round($amount,2));
        $request->setCurrency($payment->getOrder()->getBaseCurrencyCode());
        $request->setAcct($payment->getCcNumber());
        $request->setExpdate(sprintf('%02d',$payment->getCcExpMonth()) . substr($payment->getCcExpYear(),-2,2));
        $request->setCvv2($payment->getCcCid());

        if ($this->getIsCentinelValidationEnabled()){
            $params = array();
            $params = $this->getCentinelValidator()->exportCmpiData($params);
            $request = Varien_Object_Mapper::accumulateByMap($params, $request, $this->_centinelFieldMap);
        }

        $order = $payment->getOrder();
        if(!empty($order)){
            $billing = $order->getBillingAddress();
            if (!empty($billing)) {
                $request->setFirstname($billing->getFirstname())
                    ->setLastname($billing->getLastname())
                    ->setStreet($billing->getStreet(1))
                    ->setCity($billing->getCity())
                    ->setState($billing->getRegionCode())
                    ->setZip($billing->getPostcode())
                    ->setCountry($billing->getCountry())
                    ->setEmail($payment->getOrder()->getCustomerEmail());
            }
            $shipping = $order->getShippingAddress();
            if (!empty($shipping)) {
                $request->setShiptofirstname($shipping->getFirstname())
                    ->setShiptolastname($shipping->getLastname())
                    ->setShiptostreet($shipping->getStreet(1))
                    ->setShiptocity($shipping->getCity())
                    ->setShiptostate($billing->getRegionCode())
                    ->setShiptozip($shipping->getPostcode())
                    ->setShiptocountry($shipping->getCountry());
            }
        }
        return $request;
    }

     /**
      * Return request object with basic information for gateway request
      *
      * @param Mage_Sales_Model_Order_Payment $payment
      * @return Varien_Object
      */
    protected function _buildBasicRequest(Varien_Object $payment)
    {
        $request = $this->_getRequestObject()
            ->setUser($this->getConfigData('user'))
            ->setVendor($this->getConfigData('vendor'))
            ->setPartner($this->getConfigData('partner'))
            ->setPwd($this->getConfigData('pwd'))
            ->setVerbosity($this->getConfigData('verbosity'))
            ->setTender(self::TENDER_CC)
            ->setRequestId($this->_generateRequestId());
        return $request;
    }

     /**
      * Return unique value for request
      *
      * @return string
      */
    protected function _generateRequestId()
    {
        return Mage::helper('core')->uniqHash();
    }

     /**
      * Return generic object instance for API requests
      *
      * @return Varien_Object
      */
    protected function _getRequestObject()
    {
        $request = new Varien_Object();
        return $request;
    }

     /**
      * Return wrapper object instance for API response results
      *
      * @return Varien_Object
      */
    protected function _getResultObject()
    {
        $result = new Varien_Object();
        return $result;
    }

     /**
      * If response is failed throw exception
      *
      * @return Mage_Paypal_Model_Payflowpro
      * @throws Mage_Core_Exception
      */
    protected function _processErrors(Varien_Object $response)
    {
        if ($response->getResultCode() != self::RESPONSE_CODE_APPROVED
            && $response->getResultCode() != self::RESPONSE_CODE_FRAUDSERVICE_FILTER) {
            Mage::throwException($response->getRespmsg());
        }
        return $this;
    }
}
