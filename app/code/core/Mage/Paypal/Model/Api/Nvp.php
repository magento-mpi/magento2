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
     * Global public interface map
     * @var array
     */
    protected $_globalMap = array(
        // each call
        'VERSION'      => 'version',
        'USER'         => 'api_username',
        'PWD'          => 'api_password',
        'SIGNATURE'    => 'api_signature',
        'BUTTONSOURCE' => 'build_notation_code',
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
        'IPADDRESS'         => 'ip_address',
        'NOTIFYURL'         => 'notify_url',
        'RETURNFMFDETAILS'  => 'fraud_management_filters_enabled',
        // style settings
        'PAGESTYLE'      => 'page_style',
        'HDRIMG'         => 'hdrimg',
        'HDRBORDERCOLOR' => 'hdrbordercolor',
        'HDRBACKCOLOR'   => 'hdrbackcolor',
        'PAYFLOWCOLOR'   => 'payflowcolor',
        'LOCALECODE'     => 'locale_code',

        // transaction info
        'TRANSACTIONID'   => 'transaction_id',
        'AUTHORIZATIONID' => 'authorization_id',
        'AMT' => 'amount',

        // payment/billing info
        'CURRENCYCODE'  => 'currency_code',
        'PAYMENTSTATUS' => 'payment_status',
        'PENDINGREASON' => 'pending_reason',
        'PROTECTIONELIGIBILITY' => 'protection_eligibility',
        'PAYERID' => 'payer_id',
        'PAYERSTATUS' => 'payer_status',
        'ADDRESSID' => 'address_id',
        'ADDRESSSTATUS' => 'address_status',
        'EMAIL'         => 'email',
        // paypal direct credit card information
        'CREDITCARDTYPE' => 'credit_card_type',
        'ACCT'           => 'credit_card_number',
        'EXPDATE'        => 'credit_card_expiration_date',
        'CVV2'           => 'credit_card_cvv2',
        'STARTDATE'      => 'maestro_solo_issue_date', // MMYYYY, always six chars, including leading zero
        'ISSUENUMBER'    => 'maestro_solo_issue_number',
        'CVV2MATCH'      => 'cvv2_check_result',
        'AVSCODE'        => 'avs_result',
        // cardinal centinel
        'AUTHSTATUS3D' => 'centinel_authstatus',
        'MPIVENDOR3DS' => 'centinel_mpivendor',
        'CAVV'         => 'centinel_cavv',
        'ECI3DS'       => 'centinel_eci',
        'XID'          => 'centinel_xid',
        'VPAS'         => 'centinel_vpas_result',
        'ECISUBMITTED3DS' => 'centinel_eci_result',
    );

    /**
     * Filter callbacks for preparing internal amounts to NVP request
     *
     * @var array
     */
    protected $_exportToRequestFilters = array(
        'AMT' => '_filterAmount',
        'CREDITCARDTYPE' => '_filterCcType',
    );

    /**
     * Request map for each API call
     * @var array
     */
    protected $_eachCallRequest = array('VERSION', 'USER', 'PWD', 'SIGNATURE', 'BUTTONSOURCE',);

    /**
     * SetExpressCheckout request/response map
     * @var array
     */
    protected $_setExpressCheckoutRequest = array(
        'PAYMENTACTION', 'AMT', 'CURRENCYCODE', 'RETURNURL', 'CANCELURL', 'INVNUM', 'SOLUTIONTYPE',
        'GIROPAYCANCELURL', 'GIROPAYSUCCESSURL', 'BANKTXNPENDINGURL',
        'PAGESTYLE', 'HDRIMG', 'HDRBORDERCOLOR', 'HDRBACKCOLOR', 'PAYFLOWCOLOR', 'LOCALECODE',
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
     * DoDirectPayment request/response map
     * @var array
     */
    protected $_doDirectPaymentRequest = array(
        'PAYMENTACTION', 'IPADDRESS', 'RETURNFMFDETAILS',
        'AMT', 'CURRENCYCODE', 'INVNUM', 'NOTIFYURL', 'EMAIL', //, 'ITEMAMT', 'SHIPPINGAMT', 'TAXAMT',
        'CREDITCARDTYPE', 'ACCT', 'EXPDATE', 'CVV2', 'STARTDATE', 'ISSUENUMBER',
        'AUTHSTATUS3D', 'MPIVENDOR3DS', 'CAVV', 'ECI3DS', 'XID',
    );
    protected $_doDirectPaymentResponse = array(
        'TRANSACTIONID', 'AMT', 'AVSCODE', 'CVV2MATCH', 'VPAS', 'ECISUBMITTED3DS'
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
        'BUSINESS' => 'company',
        'NOTETEXT' => 'customer_notes',
        'EMAIL' => 'email',
        'FIRSTNAME' => 'firstname',
        'LASTNAME' => 'lastname',
        'MIDDLENAME' => 'middlename',
        'SALUTATION' => 'prefix',
        'SUFFIX' => 'suffix',

        'COUNTRYCODE' => 'country_id', // iso-3166 two-character code
        'STATE'    => 'region',
        'CITY'     => 'city',
        'STREET'   => 'street',
        'STREET2'  => 'street2',
        'ZIP'      => 'postcode',
        'PHONENUM' => 'telephone',
    );

    /**
     * Map for shipping address import/export (extends billing address mapper)
     * @var array
     */
    protected $_shippingAddressMap = array(
        'SHIPTOCOUNTRYCODE' => 'country_id',
        'SHIPTOSTATE' => 'region',
        'SHIPTOCITY'    => 'city',
        'SHIPTOSTREET'  => 'street',
        'SHIPTOSTREET2' => 'street2',
        'SHIPTOZIP' => 'postcode',
        'SHIPTOPHONENUM' => 'telephone',
        // 'SHIPTONAME' will be treated manually in address import/export methods
    );

    /**
     * Payment information response specifically to be collected after some requests
     * @var array
     */
    protected $_paymentInformationResponse = array(
        'PAYERID', 'PAYERSTATUS', 'CORRELATIONID', 'ADDRESSID', 'ADDRESSSTATUS',
        'PAYMENTSTATUS', 'PENDINGREASON', 'PROTECTIONELIGIBILITY',
    );

    /**
     * Line items export mapping settings
     * @var array
     */
    protected $_lineItemExportTotals = array(
        'subtotal' => 'ITEMAMT',
        'shipping' => 'SHIPPINGAMT',
        'tax'      => 'TAXAMT',
        // 'shipping_discount' => 'SHIPPINGDISCOUNT', // currently ignored by API for some reason
    );
    protected $_lineItemExportItemsFormat = array(
        'id'     => 'L_NUMBER%d',
        'name'   => 'L_NAME%d',
        'qty'    => 'L_QTY%d',
        'amount' => 'L_AMT%d',
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
     * Map of credit card types supported by this API
     * @var array
     */
    protected $_supportedCcTypes = array('VI' => 'Visa', 'MC' => 'MasterCard', 'DI' => 'Discover', 'AE' => 'Amex');

    /**
     * Warning codes recollected after each API call
     *
     * @var array
     */
    protected $_callWarnings = array();

    /**
     * API endpoint getter
     *
     * @return string
     */
    public function getApiEndpoint()
    {
        return sprintf('https://api-3t%s.paypal.com/nvp', $this->_config->sandboxFlag ? '.sandbox' : '');
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
        $this->_exportLineItems($request);

        // import/suppress shipping address, if any
        if ($address = $this->getAddress()) {
            $request = $this->_importAddress($address, $request);
            $request['ADDROVERRIDE'] = 1;
        }
        if ($this->getSuppressShipping()) {
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
        $this->_exportLineItems($request);
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
     * Process a credit card payment
     */
    public function callDoDirectPayment()
    {
        $request = $this->_exportToRequest($this->_doDirectPaymentRequest);
        $this->_exportLineItems($request);
        if ($address = $this->getAddress()) {
            $request = $this->_importAddress($address, $request);
        }
        $response = $this->call('DoDirectPayment', $request);
        $this->_importFromResponse($this->_doDirectPaymentResponse, $response);
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
        $request['method'] = $methodName;
        $request = $this->_exportToRequest($this->_eachCallRequest, $request);

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
        $this->_callWarnings = array();
        if ($ack == 'SUCCESS' || $ack == 'SUCCESSWITHWARNING') {
            // collect warnings
            if ($ack == 'SUCCESSWITHWARNING') {
                for ($i = 0; isset($response["L_ERRORCODE{$i}"]); $i++) {
                    $this->_callWarnings[] = $response["L_ERRORCODE{$i}"];
                }
            }
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
        for ($i = 0; isset($response["L_ERRORCODE{$i}"]); $i++) {
            $errors[] = sprintf('%s (#%s: %s).',
                preg_replace('/\.$/', '', $response["L_LONGMESSAGE{$i}"]),
                $response["L_ERRORCODE{$i}"], preg_replace('/\.$/', '', $response["L_SHORTMESSAGE{$i}"])
            );
        }
        if ($errors) {
            $errors = implode(' ', $errors);
            $e = new Exception(sprintf('PayPal NVP gateway errors: %s Corellation ID: %s. Version: %s.', $errors,
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
     * Create billing and shipping addresses basing on response data
     * @param array $data
     */
    protected function _exportAddressses($data)
    {
        $address = new Varien_Object();
        Varien_Object_Mapper::accumulateByMap($data, $address, $this->_billingAddressMap);
        $this->_applyStreetAndRegionWorkarounds($address);
        $this->setExportedBillingAddress($address);

        // assume there is shipping address if there is at least one field specific to shipping
        if (isset($data['SHIPTONAME'])) {
            $shippingAddress = clone $address;
            Varien_Object_Mapper::accumulateByMap($data, $shippingAddress, $this->_shippingAddressMap);
            $this->_applyStreetAndRegionWorkarounds($shippingAddress);
            // PayPal doesn't provide detailed shipping name fields, so the name will be overwritten
            $shippingAddress->addData(array(
                'prefix'     => null,
                'firstname'  => $data['SHIPTONAME'],
                'middlename' => null,
                'lastname'   => null,
                'suffix'     => null,
            ));
            $this->setExportedShippingAddress($shippingAddress);
        }
    }

    /**
     * Adopt specified address object to be compatible with Magento
     *
     * @param Varien_Object $address
     */
    protected function _applyStreetAndRegionWorkarounds(Varien_Object $address)
    {
        // merge street addresses into 1
        if ($address->hasStreet2()) {
             $address->setStreet(implode("\n", array($address->getStreet(), $address->getStreet2())));
             $address->unsStreet2();
        }
        // attempt to fetch region_id from directory
        if ($address->getCountryId() && $address->getRegion()) {
            $regions = Mage::getModel('directory/country')->loadByCode($address->getCountryId())->getRegionCollection()
                ->addRegionCodeFilter($address->getRegion())
                ->setPageSize(1)
            ;
            foreach ($regions as $region) {
                $address->setRegionId($region->getId());
                break;
            }
        }
    }

    /**
     * Prepare request data basing on provided address
     *
     * @param Varien_Object $address
     * @param array $to
     * @return array
     */
    protected function _importAddress(Varien_Object $address, array $to)
    {
        $to = Varien_Object_Mapper::accumulateByMap($address, $to, array_flip($this->_billingAddressMap));
        if ($regionCode = $this->_lookupRegionCodeFromAddress($address)) {
            $to['STATE'] = $regionCode;
        }
        if (!$this->getSuppressShipping()) {
            $to = Varien_Object_Mapper::accumulateByMap($address, $to, array_flip($this->_shippingAddressMap));
            if ($regionCode = $this->_lookupRegionCodeFromAddress($address)) {
                $to['SHIPTOSTATE'] = $regionCode;
            }
            $this->_importStreetFromAddress($address, $to, 'SHIPTOSTREET', 'SHIPTOSTREET2');
            $this->_importStreetFromAddress($address, $to, 'STREET', 'STREET2');
            $to['SHIPTONAME'] = $address->getName();
        }
        return $to;
    }

    /**
     * Filter for credit card type
     *
     * @param string $value
     * @return string
     */
    protected function _filterCcType($value)
    {
        if (isset($this->_supportedCcTypes[$value])) {
            return $this->_supportedCcTypes[$value];
        }
        return '';
    }
}
