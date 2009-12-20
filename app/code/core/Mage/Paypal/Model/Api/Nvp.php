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
     * Filter callbacks for preparing internal amounts to NVP request
     * @var array
     */
    protected $_exportToRequestFilters = array(
        'AMT' => '_filterAmount',
    );

    /**
     * Global public interface map
     * @var array
     */
    protected $_globalMap = array(
        // commands
        'PAYMENTACTION' => 'payment_type',
        'RETURNURL'     => 'return_url',
        'CANCELURL'     => 'cancel_url',
        'INVNUM'        => 'inv_num',
        'TOKEN'         => 'token',
        'CORRELATIONID' => 'correlation_id',
        'SOLUTIONTYPE'  => 'solution_type',
        'GIROPAYCANCELURL'  => 'giropay_cancel_url',
        'GIROPAYSUCCESSURL' => 'giropay_success_url',
        'BANKTXNPENDINGURL' => 'giropay_bank_txn_pending_url',
        'IPADDRESS'         => 'server_name',
        'NOTIFYURL'         => 'notify_url',
        // style settings
        'PAGESTYLE'      => 'page_style',
        'HDRIMG'         => 'hdrimg',
        'HDRBORDERCOLOR' => 'hdrbordercolor',
        'HDRBACKCOLOR'   => 'hdrbackcolor',
        'PAYFLOWCOLOR'   => 'payflowcolor',
        'BUTTONSOURCE'   => 'button_source_ec',

        // transaction info
        'AUTHORIZATIONID' => 'authorization_id',
        'AMT' => 'amount',

        // payment info
        'CURRENCYCODE'  => 'currency_code',
        'PAYMENTSTATUS' => 'payment_status',
        'PENDINGREASON' => 'pending_reason',
        'PROTECTIONELIGIBILITY' => 'protection_eligibility',
        'PAYERID' => 'payer_id',
        'PAYERSTATUS' => 'payer_status',
        'ADDRESSID' => 'address_id',
        'ADDRESSSTATUS' => 'address_status',

        // cardinal centinel
//        'AUTHSTATUS3D',
//        'MPIVENDOR3DS',
//        'CAVV'
        'XID' => 'centinel_verification_id',
    );

    /**
     * SetExpressCheckout request/response map
     * @var array
     */
    protected $_setExpressCheckoutRequest = array(
        'PAYMENTACTION', 'AMT', 'CURRENCYCODE', 'RETURNURL', 'CANCELURL', 'INVNUM', 'SOLUTIONTYPE',
        'GIROPAYCANCELURL', 'GIROPAYSUCCESSURL', 'BANKTXNPENDINGURL',
        'PAGESTYLE', 'HDRIMG', 'HDRBORDERCOLOR', 'HDRBACKCOLOR', 'PAYFLOWCOLOR',
    );
    protected $_setExpressCheckoutResponse = array('TOKEN');

    /**
     * GetExpressCheckoutDetails request/response map
     * @var array
     */
    protected $_getExpressCheckoutDetailsRequest = array('TOKEN');

    /**
     * DoExpressCheckoutPayment request/response map
     * @var array
     */
    protected $_doExpressCheckoutPaymentRequest = array(
        'TOKEN', 'PAYERID', 'PAYMENTACTION', 'AMT', 'CURRENCYCODE', 'IPADDRESS', 'BUTTONSOURCE', 'NOTIFYURL',
    );

    /**
     * DoReauthorization request/response map
     * @var array
     */
    protected $_doReauthorizationRequest = array('AUTHORIZATIONID', 'AMT', 'CURRENCYCODE');
    protected $_doReauthorizationResponse = array(
        'AUTHORIZATIONID', 'PAYMENTSTATUS', 'PENDINGREASON', 'PROTECTIONELIGIBILITY'
    );

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
     * Payment information response specifically to be collected after some requests
     * @var array
     */
    protected $_paymentInformationResponse = array(
        'PAYERID', 'PAYERSTATUS', 'CORRELATIONID', 'ADDRESSID', 'ADDRESSSTATUS',
        'PAYMENTSTATUS', 'PENDINGREASON', 'PROTECTIONELIGIBILITY',
        // 'AUTHSTATUS3D','MPIVENDOR3DS','CAVV','XID'
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
        $request = $this->_exportToRequest($this->_setExpressCheckoutRequest);
        $request['LOCALECODE'] = Mage::app()->getLocale()->getLocaleCode();

        // prepare line items
        if ($lineItems = $this->getLineItems()) {
            $lineItemArray = $this->_prepareLineItem($lineItems, $this->getItemAmount(), $this->getItemTaxAmount(), $this->getShippingAmount(), $this->getDiscountAmount());
            $request = array_merge($request, $lineItemArray);
        }

        // import/suppress shipping address, if any
        if ($address = $this->getShippingAddress()) {
            $request = $this->_importShippingAddress($address, $request);
            $request['ADDROVERRIDE'] = 1;
        } elseif ($this->getSuppressShipping()) {
            $request['NOSHIPPING'] = 1;
        }

        $response = $this->call('SetExpressCheckout', $request);
        $this->_importFromResponse($this->_setExpressCheckoutResponse, $response);
    }

    /**
     * GetExpressCheckoutDetails call
     * @see https://cms.paypal.com/us/cgi-bin/?&cmd=_render-content&content_ID=developer/e_howto_api_nvp_r_GetExpressCheckoutDetails
     */
    function callGetExpressCheckoutDetails()
    {
        $request = $this->_exportToRequest($this->_getExpressCheckoutDetailsRequest);
        $response = $this->call('GetExpressCheckoutDetails', $request);
        $this->_importFromResponse($this->_paymentInformationResponse, $response);
        $this->_exportAddressses($response);
//        $this->setIsRedirectRequired(!empty($resArr['REDIRECTREQUIRED']) && (bool)$resArr['REDIRECTREQUIRED']);
    }

    /**
     * DoExpressCheckout call
     * @see https://cms.paypal.com/us/cgi-bin/?&cmd=_render-content&content_ID=developer/e_howto_api_nvp_r_DoExpressCheckoutPayment
     */
    public function callDoExpressCheckoutPayment()
    {
        $request = $this->_exportToRequest($this->_doExpressCheckoutPaymentRequest);

        $request['TAXAMT'] = sprintf('%.2F', $this->getTaxAmount());
        $request['SHIPPINGAMT'] = sprintf('%.2F',$this->getShippingAmount());

        $this->_prepareLineItems($request);
        if ($this->getReturnFmfDetailes()) {
            $request['RETURNFMFDETAILS '] = 1;
        }

        $response = $this->call('DoExpressCheckoutPayment', $request);
        $this->_importFromResponse($this->_paymentInformationResponse, $response);

        $this->setTransactionId($response['TRANSACTIONID']);
        $this->setAmount($response['AMT']);
//        $this->setIsRedirectRequired(!empty($response['REDIRECTREQUIRED']) && (bool)$response['REDIRECTREQUIRED']);
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

        $request = array(
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
            $request['AUTHSTATUS3D'] = $this->getAuthStatus();
            $request['MPIVENDOR3DS'] = $this->getMpiVendor();
            $request['CAVV']         = $this->getCavv();
            $request['ECI3DS']       = $this->getEci3d();
            $request['XID']          = $this->getXid();
        }
        if ($this->getReturnFmfDetails()) {
            $request['RETURNFMFDETAILS '] = 1;
        }

        if ($lineItems = $this->getLineItems()) {
            $lineItemArray = $this->_prepareLineItem($lineItems, $this->getItemAmount(), $this->getItemTaxAmount(), $this->getShippingAmount(), $this->getDiscountAmount());
            $request = array_merge($request, $lineItemArray);
        }

        $response = $this->call('DoDirectPayment', $request);

        $this->setTransactionId($response['TRANSACTIONID']);
        $this->setAmount($response['AMT']);
        $this->setAvsCode($response['AVSCODE']);
        $this->setCvv2Match($response['CVV2MATCH']);
    }

    /**
     * Made additional request to paypal to get autharization id
     */
    public function callDoReauthorization()
    {
        $request = $this->_export($this->_doReauthorizationRequest);
        $response = $this->call('DoReauthorization', $request);
        $this->_import($response, $this->_doReauthorizationResponse);
    }

    /**
     * DoCapture call
     * @see https://cms.paypal.com/us/cgi-bin/?&cmd=_render-content&content_ID=developer/e_howto_api_nvp_r_DoCapture
     */
    public function callDoCapture()
    {
        $request = array(
            'AUTHORIZATIONID' => $this->getAuthorizationId(),
            'COMPLETETYPE'    => $this->getCompleteType(),
            'AMT'             => $this->getAmount(),
            'CURRENCYCODE'    => $this->getCurrencyCode(),
            'NOTE'            => $this->getNote(),
            'INVNUM'          => $this->getInvNum()
        );

        $response = $this->call('DoCapture', $request);

//        if (!empty($response['PAYERSTATUS'])) {
//            $this->setAccountStatus($response['PAYERSTATUS']);
//        }
//        if (!empty($response['PROTECTIONELIGIBILITY'])) {
//            $this->setProtectionEligibility($response['PROTECTIONELIGIBILITY']);
//        }
//        $this->setAuthorizationId($response['AUTHORIZATIONID']);
        $this->setTransactionId($response['TRANSACTIONID']);
//        $this->setPaymentStatus($response['PAYMENTSTATUS']);
        $this->setCurrencyCode($response['CURRENCYCODE']);
        $this->setAmount($response['AMT']);
    }

    /**
     * DoVoid call
     * @see https://cms.paypal.com/us/cgi-bin/?&cmd=_render-content&content_ID=developer/e_howto_api_nvp_r_DoVoid
     */
    public function callDoVoid()
    {
        $request = array(
            'AUTHORIZATIONID' => $this->getAuthorizationId(),
            'NOTE'            => $this->getNote(),
        );
        $this->call('DoVoid', $request);
    }

    /**
     * GetTransactionDetails
     * @see https://cms.paypal.com/us/cgi-bin/?&cmd=_render-content&content_ID=developer/e_howto_api_nvp_r_GetTransactionDetails
     */
    public function callGetTransactionDetails()
    {
        $request = array(
            'TRANSACTIONID' => $this->getTransactionId(),
        );

        $response = $this->call('GetTransactionDetails', $request);

//        $this->setIsRedirectRequired(!empty($resArr['REDIRECTREQUIRED']) && (bool)$resArr['REDIRECTREQUIRED']);
//        $this->setPayerEmail($resArr['RECEIVEREMAIL']); // this is incorrect!
        $this->setPayerId($response['PAYERID']);
        $this->setFirstname($response['FIRSTNAME']);
        $this->setLastname($response['LASTNAME']);
        $this->setTransactionId($response['TRANSACTIONID']);
        $this->setParentTransactionId($response['PARENTTRANSACTIONID']);
        $this->setCurrencyCode($response['CURRENCYCODE']);
        $this->setAmount($response['AMT']);
//        if (!empty($resArr['PAYERSTATUS'])) {
//            $this->setPaymentStatus($resArr['PAYERSTATUS']);
//            $this->setAccountStatus($resArr['PAYERSTATUS']);
//        }
//        if (!empty($resArr['PROTECTIONELIGIBILITY'])) {
//            $this->setProtectionEligibility($resArr['PROTECTIONELIGIBILITY']);
//        }
    }

    /**
     * RefundTransaction call
     * @see https://cms.paypal.com/us/cgi-bin/?&cmd=_render-content&content_ID=developer/e_howto_api_nvp_r_RefundTransaction
     */
    public function callRefundTransaction()
    {
        $request = array(
            'TRANSACTIONID' => $this->getTransactionId(),
            'REFUNDTYPE'    => $this->getRefundType(),
            'CURRENCYCODE'  => $this->getCurrencyCode(),
            'NOTE'          => $this->getNote(),
        );
        if ($this->getRefundType() === Mage_Paypal_Model_Config::REFUND_TYPE_PARTIAL) {
            $request['AMT'] = $this->getAmount();
        }

        $result = $this->call('RefundTransaction', $request);
        $this->setTransactionId($result['REFUNDTRANSACTIONID']);
        $this->setAmount($result['GROSSREFUNDAMT']);
    }

    /**
     * ManagePendingTransactionStatus
     * @see https://cms.paypal.com/us/cgi-bin/?&cmd=_render-content&content_ID=developer/e_howto_api_nvp_r_ManagePendingTransactionStatus
     */
    public function callManagePendingTransactionStatus()
    {
        $response = $this->call('ManagePendingTransactionStatus', array(
            'TRANSACTIONID' => $this->getTransactionId(),
            'ACTION'        => $this->getAction(),
        ));
        $this->setTransactionId($response['TRANSACTIONID']);
    }

    /**
     * getPalDetails call
     * @see https://www.x.com/docs/DOC-1300
     * @see https://cms.paypal.com/us/cgi-bin/?cmd=_render-content&content_ID=developer/e_howto_api_ECButtonIntegration
     */
    public function callGetPalDetails()
    {
        $result = $this->call('getPalDetails', array());
        $this->setPal($result['PAL']);
    }

    /**
     * Do the API call
     *
     * @param string $methodName
     * @param array $request
     * @return array
     * @throws Mage_Core_Exception
     */
    public function call($methodName, array $request)
    {
        $request = array_merge(array(
            'METHOD'    => $methodName,
            'VERSION'   => $this->getVersion(),
            'USER'      => $this->getApiUserName(),
            'PWD'       => $this->getApiPassword(),
            'SIGNATURE' => $this->getApiSignature(),
        ), $request);

        if ($this->getDebug()) {
            $requestDebug = $request;
            foreach ($this->_debugReplacePrivateDataKeys as $key) {
                if (isset($request[$key])) {
                    $requestDebug[$key] = '***';
                }
            }
            $debug = Mage::getModel('paypal/api_debug')
                ->setApiEndpoint($this->getApiEndpoint())
                ->setRequestBody(var_export($requestDebug, 1))
                ->save();
        }

        $http = new Varien_Http_Adapter_Curl();
        $config = array('timeout' => 30);
        if ($this->getUseProxy()) {
            $config['proxy'] = $this->getProxyHost(). ':' . $this->getProxyPort();
        }
        $http->setConfig($config);
        $http->write(Zend_Http_Client::POST, $this->getApiEndpoint(), '1.1', array(), http_build_query($request));
        $response = $http->read();
        $http->close();
        $response = preg_split('/^\r?$/m', $response, 2);
        $response = trim($response[1]);
        $response = $this->_deformatNVP($response);

        if ($this->getDebug()) {
            $debug->setResponseBody(var_export($response, 1))->save();
        }

        // handle transport error
        if ($http->getErrno()) {
            Mage::logException(new Exception(
                sprintf('PayPal NVP CURL connection error #%s: %s', $http->getErrno(), $http->getError())
            ));
//            $this->setRedirectUrl($this->getApiErrorUrl());
            Mage::throwException(Mage::helper('paypal')->__('Unable to communicate with PayPal gateway.'));
        }

        $ack = strtoupper($response['ACK']);
        if ($ack == 'SUCCESS' || $ack == 'SUCCESSWITHWARNING') {
//            $this->unsError();
// TODO: move to appropriate place
//            if ($ack=='SUCCESSWITHWARNING') {
//                //fraud checking
//                for ($i=0; isset($response['L_SHORTMESSAGE'.$i]); $i++) {
//                    if ($response['L_ERRORCODE'.$i] == self::FRAUD_ERROR_CODE) {
//                        $this->setIsFraud(true);
//                    }
//                }
//            }
            return $response;
        }

        // handle logical errors
        $errors = array();
        for ($i=0; isset($response["L_SHORTMESSAGE{$i}"]); $i++) {
            $errors[] = sprintf('%s (#%s: %s).',
                preg_replace('/\.$/', '', $response["L_LONGMESSAGE{$i}"]),
                $response["L_ERRORCODE{$i}"], preg_replace('/\.$/', '', $response["L_SHORTMESSAGE{$i}"])
            );
        }
        if ($errors) {
            $errors = implode(' ', $errors);
            $e = new Exception(sprintf('PayPal NVP gateway errors %s Corellation ID: %s. Version: %s.', $errors,
                isset($response['CORRELATIONID']) ? $response['CORRELATIONID'] : '',
                isset($response['VERSION']) ? $response['VERSION'] : ''
            ));
            Mage::logException($e);
            Mage::throwException(Mage::helper('paypal')->__('PayPal geteway rejected request. %s', $errors));
        }
        return $response;
    }

    /**
     * Parse an NVP response string into an associative array
     * @param string $nvpstr
     * @return array
     */
    protected function _deformatNVP($nvpstr)
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
