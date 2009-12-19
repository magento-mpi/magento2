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
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * NVP API wrappers model
 * @TODO: move some parts to abstract, don't hesitate to throw exceptions on api calls
 */
class Mage_Paypal_Model_Api_Nvp extends Mage_Paypal_Model_Api_Abstract
{
    /**
     * Map for billing address import/export
     * @var array
     */
    protected $_billingAddressMap = array (
        'SHIPTOCITY' => 'city',
        'BUSINESS' => 'company',
        'COUNTRYCODE' => 'country_id', // iso-3166 two-character code
        'NOTETEXT' => 'customer_notes',
        'EMAIL' => 'email',
        'FIRSTNAME' => 'firstname',
        'LASTNAME' => 'lastname',
        'MIDDLENAME' => 'middlename',
        'SHIPTOZIP' => 'postcode',
        'SALUTATION' => 'prefix',
        'SHIPTOSTATE' => 'region',
        'SUFFIX' => 'suffix',
        'PHONENUM' => 'telephone',
        'SHIPTOSTREET' => 'street',
        'SHIPTOSTREET2' => 'street2',
    );

    /**
     * Map for shipping address import/export (extends billing address mapper)
     * @var array
     */
    protected $_shippingAddressMap = array(
        'SHIPTONAME' => 'firstname', // workaround to put shipping name non-corrupted into one field
        'SHIPTOCOUNTRYCODE' => 'country_id' // iso-3166 two-character code
    );

    /**
     * Map for various PayPal-specific payment info
     * @see Mage_Paypal_Model_Info
     */
    protected $_paymentInformationMap = array(
        // common
        'PAYERID' => 'paypal_payer_id',
        'PAYERSTATUS' => 'paypal_payer_status',
        'CORRELATIONID' => 'paypal_correlation_id',
        'ADDRESSID' => 'paypal_address_id',
        'ADDRESSSTATUS' => 'paypal_address_status',
        'PROTECTIONELIGIBILITY' => 'paypal_protection_eligibility',

        // cardinal centinel
//        'AUTHSTATUS3D',
//        'MPIVENDOR3DS',
//        'CAVV'
        'XID' => 'paypal_centinel_verified',
    );

    /**
     * Fields that should be replaced in debug with '***'
     *
     * @var array
     */
    protected $_debugReplacePrivateDataKeys = array(
        'ACCT', 'EXPDATE', 'CVV2', 'CARDISSUE', 'CARDSTART', 'CREDITCARDTYPE', 'USER', 'PWD', 'SIGNATURE'
    );

    /**
     * Return page style for given paymethod
     *
     * @return string
     */
    public function getPageStyle()
    {
        return $this->getStyleConfigData('page_style');
    }

    /**
     * Return Api endpoint url. used for direct paypal requests
     *
     * @return string
     */
    public function getApiEndpoint()
    {
        if (!$this->getData('api_endpoint')) {
            if ($this->getSandboxFlag()) {
                $default = 'https://api-3t.sandbox.paypal.com/nvp';
            } else {
                $default = 'https://api-3t.paypal.com/nvp';
            }
            return $this->getConfigData('api_endpoint', $default);
        }
        return $this->getData('api_endpoint');
    }

    /**
     * return paypal sandbox url, depending of sendbox flag.
     * used for redirect to paypal, express method
     *
     * TODO: dispose of the paypal_url crap in sys config
     * @return string
     */
    public function getPaypalUrl()
    {
        if (!$this->hasPaypalUrl()) {
            if ($this->getSandboxFlag()) {
                $default = 'https://www.sandbox.paypal.com/';
            } else {
                $default = 'https://www.paypal.com/cgi-bin/';
            }
            $default .= 'webscr?cmd=_express-checkout&useraction='.$this->getUserAction().'&token=';

            $url = $this->getConfigData('paypal_url', $default);
        } else {
            $url = $this->getData('paypal_url');
        }
        return $url . $this->getToken();
    }

    /**
     * Return Paypal Api version
     *
     * @return string
     */
    public function getVersion()
    {
        return '60.0';
    }

    /**
     * SetExpressCheckout call
     * @see https://cms.paypal.com/us/cgi-bin/?&cmd=_render-content&content_ID=developer/e_howto_api_nvp_r_SetExpressCheckout
     * TODO: put together style and giropay settings
     */
    public function callSetExpressCheckout()
    {
        $nvpArr = array(
            'PAYMENTACTION' => $this->getPaymentType(),
            'AMT'           => (float)$this->getAmount(),
            'CURRENCYCODE'  => $this->getCurrencyCode(),
            'RETURNURL'     => $this->getReturnUrl(),
            'CANCELURL'     => $this->getCancelUrl(),
            'INVNUM'        => $this->getInvNum(),
            'HDRIMG'        => $this->getStyleConfigData('paypal_hdrimg'),
            'HDRBORDERCOLOR' => $this->getStyleConfigData('paypal_hdrbordercolor'),
            'HDRBACKCOLOR'   => $this->getStyleConfigData('paypal_hdrbackcolor'),
            'PAYFLOWCOLOR'   => $this->getStyleConfigData('paypal_payflowcolor'),
            'LOCALECODE'     => Mage::app()->getLocale()->getLocaleCode()
        );

        $nvpArr = Varien_Object_Mapper::accumulateByMap(array($this, 'getDataUsingMethod'), $nvpArr, array(
            'page_style' => 'PAGESTYLE',
            'giropay_cancel_url' => 'GIROPAYCANCELURL',
            'giropay_success_url' => 'GIROPAYSUCCESSURL',
            'giropay_bank_txn_pending_url' => 'BANKTXNPENDINGURL',
            'solution_type' => 'SOLUTIONTYPE'));

        // prepare line items
        if ($lineItems = $this->getLineItems()) {
            $lineItemArray = $this->_prepareLineItem($lineItems, $this->getItemAmount(), $this->getItemTaxAmount(), $this->getShippingAmount(), $this->getDiscountAmount());
            $nvpArr = array_merge($nvpArr, $lineItemArray);
        }

        // import/suppress shipping address, if any
        if ($address = $this->getShippingAddress()) {
            $nvpArr = $this->_importShippingAddress($address, $nvpArr);
            $nvpArr['ADDROVERRIDE'] = 1;
        } elseif ($this->getSuppressShipping()) {
            $nvpArr['NOSHIPPING'] = 1;
        }

        $resArr = $this->call('SetExpressCheckout', $nvpArr);
        if (false === $resArr) {
            return false;
        }

        $this->setToken($resArr['TOKEN']);
//        $this->setRedirectUrl($this->getPaypalUrl());
        return $resArr;
    }

    /**
     * GetExpressCheckoutDetails call
     * @see https://cms.paypal.com/us/cgi-bin/?&cmd=_render-content&content_ID=developer/e_howto_api_nvp_r_GetExpressCheckoutDetails
     * @param string $token
     * @return array
     */
    function callGetExpressCheckoutDetails($token = null)
    {
        if (null === $token) {
            $token = $this->getToken();
        }
        $nvpArr = array(
            'TOKEN' => $token,
        );

        $resArr = $this->call('GetExpressCheckoutDetails', $nvpArr);
        if (false === $resArr) {
            return false;
        }

        // export payment data and addresses
        Varien_Object_Mapper::accumulateByMap($resArr, $this, $this->_paymentInformationMap);
        $this->_exportAddressses($resArr);

//        $this->setIsRedirectRequired(!empty($resArr['REDIRECTREQUIRED']) && (bool)$resArr['REDIRECTREQUIRED']);
        return $resArr;
    }

    /**
     * DoExpressCheckout call
     * @see https://cms.paypal.com/us/cgi-bin/?&cmd=_render-content&content_ID=developer/e_howto_api_nvp_r_DoExpressCheckoutPayment
     * @return array
     */
    public function callDoExpressCheckoutPayment()
    {
        $nvpArr = array(
            'TOKEN'         => $this->getToken(),
            'PAYERID'       => $this->getPayerId(),
            'PAYMENTACTION' => $this->getPaymentType(),
            'AMT'           => $this->getAmount(),
            'CURRENCYCODE'  => $this->getCurrencyCode(),
            'IPADDRESS'     => $this->getServerName(),
            'BUTTONSOURCE'  => $this->getButtonSourceEc(),
            'NOTIFYURL'     => $this->getNotifyUrl(),
        );

        $nvpArr['TAXAMT'] = sprintf('%.2F', $this->getTaxAmount());
        $nvpArr['SHIPPINGAMT'] = sprintf('%.2F',$this->getShippingAmount());

        $this->_prepareLineItems($nvpArr);
        if ($this->getReturnFmfDetailes()) {
            $nvpArr['RETURNFMFDETAILS '] = 1;
        }

        $resArr = $this->call('DoExpressCheckoutPayment', $nvpArr);
        if (false === $resArr) {
            return false;
        }

        Varien_Object_Mapper::accumulateByMap($resArr, $this, $this->_paymentInformationMap);
        $this->setTransactionId($resArr['TRANSACTIONID']);
        $this->setAmount($resArr['AMT']);
        $this->setIsRedirectRequired(!empty($resArr['REDIRECTREQUIRED']) && (bool)$resArr['REDIRECTREQUIRED']);
        return $resArr;
    }

    /**
     * Process a credit card payment.
     * TODO: fix this
     */
    public function callDoDirectPayment()
    {
        $p = $this->getPayment();
        $a = $this->getBillingAddress();
        if ($this->getShippingAddress()) {
            $s = $this->getShippingAddress();
        } else {
            $s = $a;
        }

        $nvpArr = array(
            'PAYMENTACTION'  => $this->getPaymentType(),
            'AMT'            => $this->getAmount(),
            'CURRENCYCODE'   => $this->getCurrencyCode(),
            'BUTTONSOURCE'   => $this->getButtonSourceDp(),
            'INVNUM'         => $this->getInvNum(),
            'CREDITCARDTYPE' => $this->getCcTypeName($p->getCcType()),
            'ACCT'           => $p->getCcNumber(),
            'EXPDATE'        => sprintf('%02d%02d', $p->getCcExpMonth(), $p->getCcExpYear()),
            'CVV2'           => $p->getCcCid(),

            'FIRSTNAME'      => $a->getFirstname(),
            'LASTNAME'       => $a->getLastname(),
            'STREET'         => $a->getStreet(1),
            'CITY'           => $a->getCity(),
            'STATE'          => ($a->getRegionCode() ? $a->getRegionCode() : $a->getRegion()),
            'ZIP'            => $a->getPostcode(),
            'COUNTRYCODE'    => 'US', // only US supported for direct payment
            'EMAIL'          => $this->getEmail(),

            'SHIPTONAME'     => $s->getName(),
            'SHIPTOSTREET'   => $s->getStreet(1),
            'SHIPTOSTREET2'   => $s->getStreet(2),
            'SHIPTOCITY'     => $s->getCity(),
            'SHIPTOSTATE'    => ($s->getRegionCode() ? $s->getRegionCode() : $s->getRegion()),
            'SHIPTOZIP'      => $s->getPostcode(),
            'SHIPTOCOUNTRYCODE' => $s->getCountry(),
            'NOTIFYURL'      => $this->getNotifyUrl(), // $this->getNotifyUrl($this->getInvNum(), 'direct'),
        );

        if ($this->getMpiVendor()) {
            $nvpArr['AUTHSTATUS3D'] = $this->getAuthStatus();
            $nvpArr['MPIVENDOR3DS'] = $this->getMpiVendor();
            $nvpArr['CAVV']         = $this->getCavv();
            $nvpArr['ECI3DS']       = $this->getEci3d();
            $nvpArr['XID']          = $this->getXid();
        }
        if ($this->getReturnFmfDetails()) {
            $nvpArr['RETURNFMFDETAILS '] = 1;
        }

        if ($lineItems = $this->getLineItems()) {
            $lineItemArray = $this->_prepareLineItem($lineItems, $this->getItemAmount(), $this->getItemTaxAmount(), $this->getShippingAmount(), $this->getDiscountAmount());
            $nvpArr = array_merge($nvpArr, $lineItemArray);
        }

        $resArr = $this->call('DoDirectPayment', $nvpArr);

        if (false===$resArr) {
            return false;
        }

        $this->setTransactionId($resArr['TRANSACTIONID']);
        $this->setAmount($resArr['AMT']);
        $this->setAvsCode($resArr['AVSCODE']);
        $this->setCvv2Match($resArr['CVV2MATCH']);

        return $resArr;
    }

    /**
     * Made additional request to paypal to get autharization id
     * TODO: fix this
     */
    public function callDoReauthorization()
    {
        $nvpArr = array(
            'AUTHORIZATIONID' => $this->getAuthorizationId(),
            'AMT'             => $this->getAmount(),
            'CURRENCYCODE'    => $this->getCurrencyCode(),
        );

        $resArr = $this->call('DoReauthorization', $nvpArr);

        if (false===$resArr) {
            return false;
        }

        if (!empty($resArr['PROTECTIONELIGIBILITY'])) {
            $this->setProtectionEligibility($resArr['PROTECTIONELIGIBILITY']);
        }
        $this->setAuthorizationId($resArr['AUTHORIZATIONID']);

        return $resArr;
    }

    /**
     * DoCapture call
     * @see https://cms.paypal.com/us/cgi-bin/?&cmd=_render-content&content_ID=developer/e_howto_api_nvp_r_DoCapture
     * @return array
     * TODO: fix this
     */
    public function callDoCapture()
    {
        $nvpArr = array(
            'AUTHORIZATIONID' => $this->getAuthorizationId(),
            'COMPLETETYPE'    => $this->getCompleteType(),
            'AMT'             => $this->getAmount(),
            'CURRENCYCODE'    => $this->getCurrencyCode(),
            'NOTE'            => $this->getNote(),
            'INVNUM'          => $this->getInvNum()
        );

        $resArr = $this->call('DoCapture', $nvpArr);
        if (false === $resArr) {
            return false;
        }

        if (!empty($resArr['PAYERSTATUS'])) {
            $this->setAccountStatus($resArr['PAYERSTATUS']);
        }
        if (!empty($resArr['PROTECTIONELIGIBILITY'])) {
            $this->setProtectionEligibility($resArr['PROTECTIONELIGIBILITY']);
        }
        $this->setAuthorizationId($resArr['AUTHORIZATIONID']);
        $this->setTransactionId($resArr['TRANSACTIONID']);
        $this->setPaymentStatus($resArr['PAYMENTSTATUS']);
        $this->setCurrencyCode($resArr['CURRENCYCODE']);
        $this->setAmount($resArr['AMT']);

        return $resArr;
    }

    /**
     * DoVoid call
     * @see https://cms.paypal.com/us/cgi-bin/?&cmd=_render-content&content_ID=developer/e_howto_api_nvp_r_DoVoid
     * @return array
     * TODO: fix this
     */
    public function callDoVoid()
    {
        $nvpArr = array(
            'AUTHORIZATIONID' => $this->getAuthorizationId(),
            'NOTE'            => $this->getNote(),
        );

        $resArr = $this->call('DoVoid', $nvpArr);
        if (false === $resArr) {
            return false;
        }

        $this->setAuthorizationId($resArr['AUTHORIZATIONID']);
        return $resArr;
    }

    /**
     * GetTransactionDetails
     * @see https://cms.paypal.com/us/cgi-bin/?&cmd=_render-content&content_ID=developer/e_howto_api_nvp_r_GetTransactionDetails
     * TODO: fix this
     * @return array
     */
    public function callGetTransactionDetails()
    {
        $nvpArr = array(
            'TRANSACTIONID' => $this->getTransactionId(),
        );

        $resArr = $this->call('GetTransactionDetails', $nvpArr);

        if (false===$resArr) {
            return false;
        }

//        $this->setIsRedirectRequired(!empty($resArr['REDIRECTREQUIRED']) && (bool)$resArr['REDIRECTREQUIRED']);
        $this->setPayerEmail($resArr['RECEIVEREMAIL']);
        $this->setPayerId($resArr['PAYERID']);
        $this->setFirstname($resArr['FIRSTNAME']);
        $this->setLastname($resArr['LASTNAME']);
        $this->setTransactionId($resArr['TRANSACTIONID']);
        $this->setParentTransactionId($resArr['PARENTTRANSACTIONID']);
        $this->setCurrencyCode($resArr['CURRENCYCODE']);
        $this->setAmount($resArr['AMT']);
        if (!empty($resArr['PAYERSTATUS'])) {
            $this->setPaymentStatus($resArr['PAYERSTATUS']);
            $this->setAccountStatus($resArr['PAYERSTATUS']);
        }
        if (!empty($resArr['PROTECTIONELIGIBILITY'])) {
            $this->setProtectionEligibility($resArr['PROTECTIONELIGIBILITY']);
        }

        return $resArr;
    }

    /**
     * RefundTransaction call
     * @see https://cms.paypal.com/us/cgi-bin/?&cmd=_render-content&content_ID=developer/e_howto_api_nvp_r_RefundTransaction
     */
    public function callRefundTransaction()
    {
        $nvpArr = array(
            'TRANSACTIONID' => $this->getTransactionId(),
            'REFUNDTYPE'    => $this->getRefundType(),
            'CURRENCYCODE'  => $this->getCurrencyCode(),
            'NOTE'          => $this->getNote(),
        );
        if ($this->getRefundType()===self::REFUND_TYPE_PARTIAL) {
            $nvpArr['AMT'] = $this->getAmount();
        }

        $resArr = $this->call('RefundTransaction', $nvpArr);
        if (false === $resArr) {
            return false;
        }

        $this->setTransactionId($resArr['REFUNDTRANSACTIONID']);
        $this->setAmount($resArr['GROSSREFUNDAMT']);

        return $resArr;
    }

    /**
     * ManagePendingTransactionStatus
     * @see https://cms.paypal.com/us/cgi-bin/?&cmd=_render-content&content_ID=developer/e_howto_api_nvp_r_ManagePendingTransactionStatus
     * @return array
     */
    public function callManagePendingTransactionStatus()
    {
        $nvpArr = array(
            'TRANSACTIONID' => $this->getTransactionId(),
            'ACTION'        => $this->getAction(),
        );

        $resArr = $this->call('ManagePendingTransactionStatus', $nvpArr);

        if (false===$resArr) {
            return false;
        }

        $this->setTransactionId($resArr['TRANSACTIONID']);
        return $resArr;
    }

    /**
     * getPalDetails call
     * @see https://www.x.com/docs/DOC-1300
     * @return Mage_Paypal_Model_Api_Nvp
     * TODO: fix this
     */
    public function callPalDetails()
    {
        $nvpArr = array();

        $resArr = $this->call('getPalDetails', $nvpArr);
        if (false === $resArr) {
            return false;
        }
        $this->setPal($resArr['PAL']);
        return $this;
    }

    /**
     * Function to perform the API call to PayPal using API signature
     *
     * @param $methodName string is name of API  method.
     * @param $nvpArr array NVP params array
     * @return array|boolean an associtive array containing the response from the server or false in case of error.
     */
    public function call($methodName, array $nvpArr)
    {
        $nvpArr = array_merge(array(
            'METHOD'    => $methodName,
            'VERSION'   => $this->getVersion(),
            'USER'      => $this->getApiUserName(),
            'PWD'       => $this->getApiPassword(),
            'SIGNATURE' => $this->getApiSignature(),
        ), $nvpArr);

        $nvpReq = '';
        $nvpReqDebug = '';

        foreach ($nvpArr as $k=>$v) {
            $nvpReq .= '&'.$k.'='.urlencode($v);
            $nvpReqDebug .= '&'.$k.'=';
            if (in_array($k, $this->_debugReplacePrivateDataKeys)) {
                $nvpReqDebug .= '***';
            } else {
                $nvpReqDebug .= urlencode($v);
            }
        }

        $nvpReq = substr($nvpReq, 1);
        if ($this->getDebug()) {
            $debug = Mage::getModel('paypal/api_debug')
                ->setApiEndpoint($this->getApiEndpoint())
                ->setRequestBody($nvpReqDebug)
                ->save();
        }
        $http = new Varien_Http_Adapter_Curl();
        $config = array('timeout' => 30);
        if ($this->getUseProxy()) {
            $config['proxy'] = $this->getProxyHost(). ':' . $this->getProxyPort();
        }
        $http->setConfig($config);
        $http->write(Zend_Http_Client::POST, $this->getApiEndpoint(), '1.1', array(), $nvpReq);
        $response = $http->read();
        $response = preg_split('/^\r?$/m', $response, 2);
        $response = trim($response[1]);

        if ($this->getDebug()) {
            $debug->setResponseBody($response)->save();
        }

        $nvpReqArray = $this->deformatNVP($nvpReq);
        $this->getSession()->setNvpReqArray($nvpReqArray);

        if ($http->getErrno()) {
            $http->close();
            $this->setError(array(
                'type'=>'CURL',
                'code'=>$http->getErrno(),
                'message'=>$http->getError()
            ));
//            $this->setRedirectUrl($this->getApiErrorUrl());
            return false;
        }
        $http->close();

        //converting NVPResponse to an Associative Array
        $nvpResArray = $this->deformatNVP($response);
        $this->getSession()
            ->setLastCallMethod($methodName)
            ->setResHash($nvpResArray);
        $ack = strtoupper($nvpResArray['ACK']);
        if ($ack == 'SUCCESS' || $ack=='SUCCESSWITHWARNING') {
            $this->unsError();
            if ($ack=='SUCCESSWITHWARNING') {
                //fraud checking
                for ($i=0; isset($nvpResArray['L_SHORTMESSAGE'.$i]); $i++) {
                    if ($nvpResArray['L_ERRORCODE'.$i] == self::FRAUD_ERROR_CODE) {
                        $this->setIsFraud(true);
                    }
                }
            }
            return $nvpResArray;
        }

        $errorArr = array(
            'type' => 'API',
            'ack' => $ack,
        );
        if (isset($nvpResArray['VERSION'])) {
            $errorArr['version'] = $nvpResArray['VERSION'];
        }
        if (isset($nvpResArray['CORRELATIONID'])) {
            $errorArr['correlation_id'] = $nvpResArray['CORRELATIONID'];
        }
        for ($i=0; isset($nvpResArray['L_SHORTMESSAGE'.$i]); $i++) {
            $errorArr['code'] = $nvpResArray['L_ERRORCODE'.$i];
            $errorArr['short_message'] = $nvpResArray['L_SHORTMESSAGE'.$i];
            $errorArr['long_message'] = $nvpResArray['L_LONGMESSAGE'.$i];
        }
        $this->setError($errorArr);
//        $this->setRedirectUrl($this->getApiErrorUrl());
        return false;
    }

    /*'----------------------------------------------------------------------------------
     * This function will take NVPString and convert it to an Associative Array and it will decode the response.
      * It is usefull to search for a particular key and displaying arrays.
      * @nvpstr is NVPString.
      * @nvpArray is Associative Array.
       ----------------------------------------------------------------------------------
      */
    public function deformatNVP($nvpstr)
    {
        $intial=0;
        $nvpArray = array();

        $nvpstr = strpos($nvpstr, "\r\n\r\n")!==false ? substr($nvpstr, strpos($nvpstr, "\r\n\r\n")+4) : $nvpstr;

        while(strlen($nvpstr)) {
            //postion of Key
            $keypos= strpos($nvpstr,'=');
            //position of value
            $valuepos = strpos($nvpstr,'&') ? strpos($nvpstr,'&'): strlen($nvpstr);

            /*getting the Key and Value values and storing in a Associative Array*/
            $keyval=substr($nvpstr,$intial,$keypos);
            $valval=substr($nvpstr,$keypos+1,$valuepos-$keypos-1);
            //decoding the respose
            $nvpArray[urldecode($keyval)] =urldecode( $valval);
            $nvpstr=substr($nvpstr,$valuepos+1,strlen($nvpstr));
         }
        return $nvpArray;
    }

    /**
     * Prepare line items request
     *
     * @param array &$request
     */
    protected function _prepareLineItems(array &$request)
    {
        $items = $this->getLineItems();
        $subtotal = sprintf('%.2F', $this->getSubtotalAmount());
        $request['ITEMAMT'] = $subtotal;
        if (!$items || !$subtotal) {
            return;
        }

        $i = 0;
        foreach($items as $item) {
            if ($item->getName() && $item->getBaseRowTotal()) {
                $request["L_NAME{$i}"]   = $item->getName();
                $request["L_NUMBER{$i}"] = $item->getProductId();
                if ($item->getBaseCalculationPrice()) {
                    $request["L_AMT{$i}"] = (float)$item->getBaseCalculationPrice();
                } else {
                    $request["L_AMT{$i}"] = (float)$item->getBasePrice();
                }
                if ($item->getTotalQty()) {
                    $request["L_QTY{$i}"]    = $item->getTotalQty();
                    $request["L_TAXAMT{$i}"] = (float)($item->getBaseTaxAmount() / $item->getTotalQty());
                } else {
                    $request["L_QTY{$i}"]    = (int) $item->getQtyOrdered();
                    $request["L_TAXAMT{$i}"] = (float)($item->getBaseTaxAmount() / $item->getQtyOrdered());
                }
            }
            $i++;
        }

        $discount = abs(1 * $this->getDiscountAmount());
        if ($discount > 0) {
            $i++;
            $request["L_NAME{$i}"]   = Mage::helper('paypal')->__('Discount');
            $request["L_NUMBER{$i}"] = 0;
            $request["L_AMT{$i}"]    = -1 * $discount;
            $request["L_QTY{$i}"]    = 1;
//            $request["L_DESC{$i}"]   = Mage::helper('paypal')->__('Discount');
            $request["L_TAXAMT{$i}"] = 0;
        }
    }

    /**
     * Error message NVP getter
     * @return string
     */
    public function getErrorMessage()
    {
        $e = $this->getError();
        if (!$e) {
            return '';
        }
        $shortMessage = '';
        if (!isset($e['short_message'])) {
            if (isset($e['code'])) {
                $shortMessage = Mage::helper('paypal')->__('Unknown API error #%s', $e['code']);
            }
        } else {
            $shortMessage = $e['short_message'];
        }
        $message = (isset($e['long_message']) ? sprintf('%s: %s', $shortMessage, $e['long_message']) : $shortMessage);
        return ($e['code'] ? sprintf('(#%s) ', $e['code']) : '' ) . $message;
    }

    /**
     * Create billing and shipping addresses basing on response data
     * @param array $data
     */
    protected function _exportAddressses($data)
    {
        $address = new Varien_Object();
        Varien_Object_Mapper::accumulateByMap($data, $address, $this->_billingAddressMap, array(
            // workaround for hardcoded fields non-compatible with PayPal (some of them may be empty in result data)
            'city' => '.',
            'company' => '.',
            'postcode' => '.',
            'region' => '.',
            'telephone' => '.',
            'middlename' => '.',
            'lastname' => '.',
            'prefix' => '.',
            'suffix' => '.',
            'street' => '.',
        ));
        // street address lines workaround
        if ($address->hasStreet2()) {
             $address->setStreet(implode("\n", array($address->getStreet(), $address->getStreet2())));
             $address->unsStreet2();
        }
        // region_id workaround: there is no need in 'region_id', because PayPal provides 'region', but Magento requires it
        $regions = Mage::getModel('directory/country')->loadByCode($address->getCountryId())->getRegionCollection()
            ->setPageSize(1)
        ;
        if ('.' !== $address->getRegion()) {
            $regions->addRegionCodeFilter($address->getRegion());
        }
        foreach ($regions as $region) {
            $address->setRegionId($region->getId());
            break;
        }
        $this->setExportedBillingAddress($address);

        // assume there is shipping address if street is found (have to replicate billing address partially, as workaround)
        if (trim($address->getStreet())) {
            $shippingAddress = clone $address;
            Varien_Object_Mapper::accumulateByMap($data, $address, $this->_shippingAddressMap);
            $this->setExportedShippingAddress($shippingAddress);
        }
    }

    /**
     * Prepare request data basing on provided shipping address
     * @param Varien_Object $address
     * @param array $to
     * @return array
     */
    protected function _importShippingAddress(Varien_Object $address, array $to)
    {
        $to = Varien_Object_Mapper::accumulateByMap($address, $to, array_flip($this->_billingAddressMap));
        $to = Varien_Object_Mapper::accumulateByMap($address, $to, array_flip($this->_shippingAddressMap));

        // region_id workaround: PayPal requires state code, try to find one in the address
        if ($regionId = $address->getData('region_id')) {
            $region = Mage::getModel('directory/region')->load($regionId);
            if ($region->getId()) {
                $to['SHIPTOSTATE'] = $region->getCode();
            }
        }
        // street address workaround
        $street = $address->getStreet();
        if ($street && is_array($street)) {
            foreach (array('SHIPTOSTREET', 'SHIPTOSTREET2') as $key) {
                if ($value = array_pop($street)) {
                    $to[$key] = $value;
                }
            }
        }
        return $to;
    }
}
