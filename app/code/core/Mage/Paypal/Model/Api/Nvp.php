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
     * Paypal methods definition
     */
    const DO_DIRECT_PAYMENT = 'DoDirectPayment';
    const DO_CAPTURE = 'DoCapture';
    const DO_VOID = 'DoVoid';
    const REFUND_TRANSACTION = 'RefundTransaction';
    const SET_EXPRESS_CHECKOUT = 'SetExpressCheckout';
    const GET_EXPRESS_CHECKOUT_DETAILS = 'GetExpressCheckoutDetails';
    const DO_EXPRESS_CHECKOUT_PAYMENT = 'DoExpressCheckoutPayment';
    const CALLBACK_RESPONSE = 'CallbackResponse';

    /**
     * Paypal ManagePendingTransactionStatus actions
     */
    const PENDING_TRANSACTION_ACCEPT = 'Accept';
    const PENDING_TRANSACTION_DENY = 'Deny';

    /**
     * Paypal status values
     */
    const STATUS_PENDING = 'Pending';
    const STATUS_PROCESSING = 'Processing';
    const STATUS_COMPLETED = 'Completed';
    const STATUS_DENIED = 'Denied';
    const STATUS_REVERSED = 'Reversed';
    const STATUS_DISPLAY_ONLY = 'Display Only';
    const STATUS_PARTIALLY_REFUNDED = 'Partially Refunded';
    const STATUS_CREATED_REFUNDED = 'Created Refunded';

    /**
     * Capture types (make authorization close or remain open)
     * @var string
     */
    protected $_captureTypeComplete = 'Complete';
    protected $_captureTypeNotcomplete = 'NotComplete';

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

        // for Unilateral payments
        'SUBJECT'      => 'business_account',

        // commands
        'PAYMENTACTION' => 'payment_action',
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
        'NOTE'              => 'note',
        'REFUNDTYPE'        => 'refund_type',
        'ACTION'            => 'action',
        'REDIRECTREQUIRED'  => 'redirect_required',
        'SUCCESSPAGEREDIRECTREQUESTED'  => 'redirect_requested',
        // style settings
        'PAGESTYLE'      => 'page_style',
        'HDRIMG'         => 'hdrimg',
        'HDRBORDERCOLOR' => 'hdrbordercolor',
        'HDRBACKCOLOR'   => 'hdrbackcolor',
        'PAYFLOWCOLOR'   => 'payflowcolor',
        'LOCALECODE'     => 'locale_code',
        'PAL'            => 'pal',

        // transaction info
        'TRANSACTIONID'   => 'transaction_id',
        'AUTHORIZATIONID' => 'authorization_id',
        'REFUNDTRANSACTIONID' => 'refund_transaction_id',
        'COMPLETETYPE'    => 'complete_type',
        'AMT' => 'amount',
        'GROSSREFUNDAMT' => 'refunded_amount', // possible mistake, check with API reference

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
            // backwards compatibility
            'FIRSTNAME'     => 'firstname',
            'LASTNAME'      => 'lastname',

        // shipping rate
        'SHIPPINGOPTIONNAME' => 'shipping_rate_code',

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
        'AUTHSTATUS3DS' => 'centinel_authstatus',
        'MPIVENDOR3DS'  => 'centinel_mpivendor',
        'CAVV'         => 'centinel_cavv',
        'ECI3DS'       => 'centinel_eci',
        'XID'          => 'centinel_xid',
        'VPAS'         => 'centinel_vpas_result',
        'ECISUBMITTED3DS' => 'centinel_eci_result',

        // recurring payment profiles
//'TOKEN' => 'token',
        'SUBSCRIBERNAME'    =>'subscriber_name',
        'PROFILESTARTDATE'  => 'start_datetime',
        'PROFILEREFERENCE'  => 'internal_reference_id',
        'DESC'              => 'schedule_description',
        'MAXFAILEDPAYMENTS' => 'suspension_threshold',
        'AUTOBILLAMT'       => 'bill_failed_later',
        'BILLINGPERIOD'     => 'period_unit',
        'BILLINGFREQUENCY'    => 'period_frequency',
        'TOTALBILLINGCYCLES'  => 'period_max_cycles',
//'AMT' => 'billing_amount', // have to use 'amount', see above
        'TRIALBILLINGPERIOD'      => 'trial_period_unit',
        'TRIALBILLINGFREQUENCY'   => 'trial_period_frequency',
        'TRIALTOTALBILLINGCYCLES' => 'trial_period_max_cycles',
        'TRIALAMT'            => 'trial_billing_amount',
// 'CURRENCYCODE' => 'currency_code',
        'SHIPPINGAMT'         => 'shipping_amount',
        'TAXAMT'              => 'tax_amount',
        'INITAMT'             => 'init_amount',
        'FAILEDINITAMTACTION' => 'init_may_fail',
        'PROFILEID'           => 'recurring_profile_id',
        'PROFILESTATUS'       => 'recurring_profile_status',

        'BILLINGAGREEMENTID' => 'billing_agreement_id',
        'REFERENCEID' => 'reference_id',
        'BILLINGAGREEMENTSTATUS' => 'billing_agreement_status',
        'BILLINGTYPE' => 'billing_type',
        'SREET' => 'street',
        'CITY' => 'city',
        'STATE' => 'state',
        'COUNTRYCODE' => 'countrycode',
        'ZIP' => 'zip',
        'PAYERBUSINESS' => 'payer_business'
    );

    /**
     * Filter callbacks for preparing internal amounts to NVP request
     *
     * @var array
     */
    protected $_exportToRequestFilters = array(
        'AMT'         => '_filterAmount',
        'TRIALAMT'    => '_filterAmount',
        'SHIPPINGAMT' => '_filterAmount',
        'TAXAMT'      => '_filterAmount',
        'INITAMT'     => '_filterAmount',
        'CREDITCARDTYPE' => '_filterCcType',
//        'PROFILESTARTDATE' => '_filterToPaypalDate',
        'AUTOBILLAMT' => '_filterBillFailedLater',
        'BILLINGPERIOD' => '_filterPeriodUnit',
        'TRIALBILLINGPERIOD' => '_filterPeriodUnit',
        'FAILEDINITAMTACTION' => '_filterInitialAmountMayFail',
        'BILLINGAGREEMENTSTATUS' => '_filterBillingAgreementStatus'
    );

    protected $_importFromRequestFilters = array(
        'REDIRECTREQUIRED'  => '_filterToBool',
        'SUCCESSPAGEREDIRECTREQUESTED'  => '_filterToBool',
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
    protected $_doExpressCheckoutPaymentResponse = array(
        'TRANSACTIONID', 'AMT', 'PAYMENTSTATUS', 'REDIRECTREQUIRED', 'SUCCESSPAGEREDIRECTREQUESTED',
    );

    /**
     * DoDirectPayment request/response map
     * @var array
     */
    protected $_doDirectPaymentRequest = array(
        'PAYMENTACTION', 'IPADDRESS', 'RETURNFMFDETAILS',
        'AMT', 'CURRENCYCODE', 'INVNUM', 'NOTIFYURL', 'EMAIL', //, 'ITEMAMT', 'SHIPPINGAMT', 'TAXAMT',
        'CREDITCARDTYPE', 'ACCT', 'EXPDATE', 'CVV2', 'STARTDATE', 'ISSUENUMBER',
        'AUTHSTATUS3DS', 'MPIVENDOR3DS', 'CAVV', 'ECI3DS', 'XID',
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
     * DoCapture request/response map
     * @var array
     */
    protected $_doCaptureRequest = array('AUTHORIZATIONID', 'COMPLETETYPE', 'AMT', 'CURRENCYCODE', 'NOTE', 'INVNUM',);
    protected $_doCaptureResponse = array('TRANSACTIONID', 'CURRENCYCODE', 'AMT',);

    /**
     * DoVoid request map
     * @var array
     */
    protected $_doVoidRequest = array('AUTHORIZATIONID', 'NOTE',);

    /**
     * GetTransactionDetailsRequest
     * @var array
     */
    protected $_getTransactionDetailsRequest = array('TRANSACTIONID');
    protected $_getTransactionDetailsResponse = array(
        'PAYERID', 'FIRSTNAME', 'LASTNAME', 'TRANSACTIONID', 'PARENTTRANSACTIONID', 'CURRENCYCODE', 'AMT', 'PAYMENTSTATUS'
    );

    /**
     * RefundTransaction request/response map
     * @var array
     */
    protected $_refundTransactionRequest = array('TRANSACTIONID', 'REFUNDTYPE', 'CURRENCYCODE', 'NOTE',);
    protected $_refundTransactionResponse = array('REFUNDTRANSACTIONID', 'GROSSREFUNDAMT',);

    /**
     * ManagePendingTransactionStatus request/response map
     */
    protected $_managePendingTransactionStatusRequest = array('TRANSACTIONID', 'ACTION');
    protected $_managePendingTransactionStatusResponse = array('TRANSACTIONID', 'STATUS');

    /**
     * GetPalDetails response map
     * @var array
     */
    protected $_getPalDetailsResponse = array('PAL');

    /**
     * CreateRecurringPaymentsProfile request/response map
     *
     * @var array
     */
    protected $_createRecurringPaymentsProfileRequest = array(
        'TOKEN', 'SUBSCRIBERNAME', 'PROFILESTARTDATE', 'PROFILEREFERENCE', 'DESC', 'MAXFAILEDPAYMENTS', 'AUTOBILLAMT',
        'BILLINGPERIOD', 'BILLINGFREQUENCY', 'TOTALBILLINGCYCLES', 'AMT', 'TRIALBILLINGPERIOD', 'TRIALBILLINGFREQUENCY',
        'TRIALTOTALBILLINGCYCLES', 'TRIALAMT', 'CURRENCYCODE', 'SHIPPINGAMT', 'TAXAMT', 'INITAMT', 'FAILEDINITAMTACTION'
    );
    protected $_createRecurringPaymentsProfileResponse = array(
        'PROFILEID', 'PROFILESTATUS'
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
     * Map for callback request
     * @var array
     */
    protected $_callbackRequestMap = array(
        'SHIPTOCOUNTRY' => 'country_id',
        'SHIPTOSTATE' => 'region',
        'SHIPTOCITY'    => 'city',
        'SHIPTOSTREET'  => 'street',
        'SHIPTOSTREET2' => 'street2',
        'SHIPTOZIP' => 'postcode'
    );

    /**
     * Payment information response specifically to be collected after some requests
     * @var array
     */
    protected $_paymentInformationResponse = array(
        'PAYERID', 'PAYERSTATUS', 'CORRELATIONID', 'ADDRESSID', 'ADDRESSSTATUS',
        'PAYMENTSTATUS', 'PENDINGREASON', 'PROTECTIONELIGIBILITY', 'EMAIL', 'SHIPPINGOPTIONNAME'
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
     * Shipping options export to request mapping settings
     * @var array
     */
    protected $_shippingOptionsExportItemsFormat = array(
        'is_default' => 'L_SHIPPINGOPTIONISDEFAULT%d',
        'name'       => 'L_SHIPPINGOPTIONNAME%d',
        'amount'     => 'L_SHIPPINGOPTIONAMOUNT%d',
    );

    /**
     * init Billing Agreement request/response map
     * @var array
     */
    protected $_customerBillingAgreementRequest = array('RETURNURL', 'CANCELURL', 'BILLINGTYPE');
    protected $_customerBillingAgreementResponse = array('TOKEN');

    /**
     * Billing Agreement details request/response map
     * @var array
     */
    protected $_billingAgreementCustomerDetailsRequest = array('TOKEN');
    protected $_billingAgreementCustomerDetailsResponse = array('EMAIL', 'PAYERID', 'PAYERSTATUS', 'SHIPTOCOUNTRYCODE', 'PAYERBUSINESS');

    /**
     * Create Billing Agreement request/response map
     * @var array
     */
    protected $_createBillingAgreementRequest = array('TOKEN');
    protected $_createBillingAgreementResponse = array('BILLINGAGREEMENTID');

    /**
     * Update Billing Agreement request/response map
     * @var array
     */
    protected $_updateBillingAgreementRequest = array(
        'REFERENCEID', 'BILLINGAGREEMENTDESCRIPTION', 'BILLINGAGREEMENTSTATUS', 'BILLINGAGREEMENTCUSTOM'
    );
    protected $_updateBillingAgreementResponse = array(
        'REFERENCEID', 'BILLINGAGREEMENTDESCRIPTION', 'BILLINGAGREEMENTSTATUS', 'BILLINGAGREEMENTCUSTOM'
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
    protected $_supportedCcTypes = array(
        'VI' => 'Visa', 'MC' => 'MasterCard', 'DI' => 'Discover', 'AE' => 'Amex', 'SM' => 'Maestro', 'SO' => 'Solo');

    /**
     * Warning codes recollected after each API call
     *
     * @var array
     */
    protected $_callWarnings = array();

    /**
     * Whether to return raw response information after each call
     *
     * @var bool
     */
    protected $_rawResponseNeeded = false;

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
     * Return Paypal callback request timeout
     *
     * @return int
     */
    public function getCallbackTimeout()
    {
        return 6;
    }

    /**
     * Retrieve billing agreement type
     *
     * @return string
     */
    public function getBillingAgreementType()
    {
        return 'MerchantInitiatedBilling';
    }

    /**
     * SetExpressCheckout call
     * @see https://cms.paypal.com/us/cgi-bin/?&cmd=_render-content&content_ID=developer/e_howto_api_nvp_r_SetExpressCheckout
     * TODO: put together style and giropay settings
     */
    public function callSetExpressCheckout()
    {
        $request = $this->_prepareExpressCheckoutCallRequest($this->_setExpressCheckoutRequest);
        $request = $this->_exportToRequest($request);
        $this->_exportLineItems($request);

        // import/suppress shipping address, if any
        if ($address = $this->getAddress()) {
            $request = $this->_importAddress($address, $request);
            if (!$this->getShippingOptions()) {
                $request['ADDROVERRIDE'] = 1;
            }
        }
        if ($this->getSuppressShipping()) {
            $request['NOSHIPPING'] = 1;
        }

        if ($options = $this->getShippingOptions()) {
            $request['CALLBACK'] = $this->getCallbackUrl();
            $request['CALLBACKTIMEOUT'] = $this->getCallbackTimeout();

            $maxAmount = 0;
            foreach ($options as $option) {
                if ($option['amount'] > $maxAmount) {
                    $maxAmount = $option['amount'];
                }
            }
            $request['MAXAMT'] = $request['AMT'] + $maxAmount;
            $this->_exportShippingOptions($request);
        }

        // add recurring profiles information
        $i = 0;
        foreach ($this->_recurringPaymentProfiles as $profile) {
            $request["L_BILLINGTYPE{$i}"] = 'RecurringPayments';
            $request["L_BILLINGAGREEMENTDESCRIPTION{$i}"] = $profile->getScheduleDescription();
            $i++;
        }

        $response = $this->call(self::SET_EXPRESS_CHECKOUT, $request);
        $this->_importFromResponse($this->_setExpressCheckoutResponse, $response);
    }

    /**
     * GetExpressCheckoutDetails call
     * @see https://cms.paypal.com/us/cgi-bin/?&cmd=_render-content&content_ID=developer/e_howto_api_nvp_r_GetExpressCheckoutDetails
     */
    function callGetExpressCheckoutDetails()
    {
        $request = $this->_prepareExpressCheckoutCallRequest($this->_getExpressCheckoutDetailsRequest);
        $request = $this->_exportToRequest($request);
        $response = $this->call(self::GET_EXPRESS_CHECKOUT_DETAILS, $request);
        $this->_importFromResponse($this->_paymentInformationResponse, $response);
        $this->_exportAddressses($response);
    }

    /**
     * DoExpressCheckout call
     * @see https://cms.paypal.com/us/cgi-bin/?&cmd=_render-content&content_ID=developer/e_howto_api_nvp_r_DoExpressCheckoutPayment
     */
    public function callDoExpressCheckoutPayment()
    {
        $request = $this->_prepareExpressCheckoutCallRequest($this->_doExpressCheckoutPaymentRequest);
        $request = $this->_exportToRequest($request);
        $this->_exportLineItems($request);

        $response = $this->call(self::DO_EXPRESS_CHECKOUT_PAYMENT, $request);
        $this->_importFromResponse($this->_paymentInformationResponse, $response);
        $this->_importFromResponse($this->_doExpressCheckoutPaymentResponse, $response);
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
        $response = $this->call(self::DO_DIRECT_PAYMENT, $request);
        $this->_importFromResponse($this->_doDirectPaymentResponse, $response);
    }

    /**
     * Analise response and return true if payment has state Pending
     *
     * @return bool
     */
    public function getIsPaymentPending()
    {
        return $this->getPaymentStatus() == self::STATUS_PENDING;
    }

    /**
     * Analise response and return true if payment has fraud error code
     *
     * @return bool
     */
    public function getIsPaymentFraud()
    {
        return in_array(11610, $this->_callWarnings);
    }

    /**
     * Analise response and return true if payment has state Completed
     *
     * @return bool
     */
    public function getIsPaymentCompleted()
    {
        return $this->getPaymentStatus() == self::STATUS_COMPLETED;
    }

    /**
     * Analise response and return true if payment has state Denied
     *
     * @return bool
     */
    public function getIsPaymentDenied()
    {
        return $this->getPaymentStatus() == self::STATUS_DENIED;
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
        $this->setCompleteType($this->_getCaptureCompleteType());
        $request = $this->_exportToRequest($this->_doCaptureRequest);
        $response = $this->call(self::DO_CAPTURE, $request);
        $this->_importFromResponse($this->_paymentInformationResponse, $response);
        $this->_importFromResponse($this->_doCaptureResponse, $response);
    }

    /**
     * DoVoid call
     * @see https://cms.paypal.com/us/cgi-bin/?&cmd=_render-content&content_ID=developer/e_howto_api_nvp_r_DoVoid
     */
    public function callDoVoid()
    {
        $request = $this->_exportToRequest($this->_doVoidRequest);
        $this->call(self::DO_VOID, $request);
    }

    /**
     * GetTransactionDetails
     * @see https://cms.paypal.com/us/cgi-bin/?&cmd=_render-content&content_ID=developer/e_howto_api_nvp_r_GetTransactionDetails
     */
    public function callGetTransactionDetails()
    {
        $request = $this->_exportToRequest($this->_getTransactionDetailsRequest);
        $response = $this->call('GetTransactionDetails', $request);
        $this->_importFromResponse($this->_getTransactionDetailsResponse, $response);
    }

    /**
     * RefundTransaction call
     * @see https://cms.paypal.com/us/cgi-bin/?&cmd=_render-content&content_ID=developer/e_howto_api_nvp_r_RefundTransaction
     */
    public function callRefundTransaction()
    {
        $request = $this->_exportToRequest($this->_refundTransactionRequest);
        if ($this->getRefundType() === Mage_Paypal_Model_Config::REFUND_TYPE_PARTIAL) {
            $request['AMT'] = $this->getAmount();
        }
        $response = $this->call(self::REFUND_TRANSACTION, $request);
        $this->_importFromResponse($this->_refundTransactionResponse, $response);
    }

    /**
     * ManagePendingTransactionStatus
     * @see https://cms.paypal.com/us/cgi-bin/?&cmd=_render-content&content_ID=developer/e_howto_api_nvp_r_ManagePendingTransactionStatus
     */
    public function callManagePendingTransactionStatus()
    {
        $request = $this->_exportToRequest($this->_managePendingTransactionStatusRequest);
        $response = $this->call('ManagePendingTransactionStatus', $request);
        $this->_importFromResponse($this->_managePendingTransactionStatusResponse, $response);
    }

    /**
     * getPalDetails call
     * @see https://www.x.com/docs/DOC-1300
     * @see https://cms.paypal.com/us/cgi-bin/?cmd=_render-content&content_ID=developer/e_howto_api_ECButtonIntegration
     */
    public function callGetPalDetails()
    {
        $response = $this->call('getPalDetails', array());
        $this->_importFromResponse($this->_getPalDetailsResponse, $response);
    }

    /**
     * Set Customer BillingA greement call
     *
     * @see https://cms.paypal.com/us/cgi-bin/?&cmd=_render-content&content_ID=developer/e_howto_api_nvp_r_SetCustomerBillingAgreement
     */
    public function callSetCustomerBillingAgreement()
    {
        $request = $this->_exportToRequest($this->_customerBillingAgreementRequest);
        $response = $this->call('SetCustomerBillingAgreement', $request);
        $this->_importFromResponse($this->_customerBillingAgreementResponse, $response);
    }

    /**
     * Get Billing Agreement Customer Details call
     *
     * @see https://cms.paypal.com/us/cgi-bin/?&cmd=_render-content&content_ID=developer/e_howto_api_nvp_r_GetBillingAgreementCustomerDetails
     */
    public function callGetBillingAgreementCustomerDetails()
    {
        $request = $this->_exportToRequest($this->_billingAgreementCustomerDetailsRequest);
        $response = $this->call('GetBillingAgreementCustomerDetails', $request);
        $this->_importFromResponse($this->_billingAgreementCustomerDetailsResponse, $response);
    }

    /**
     * Create Billing Agreement call
     *
     */
    public function callCreateBillingAgreement()
    {
        $request = $this->_exportToRequest($this->_createBillingAgreementRequest);
        $response = $this->call('CreateBillingAgreement', $request);
        $this->_importFromResponse($this->_createBillingAgreementResponse, $response);
    }

    /**
     * Billing Agreement Update call
     *
     */
    public function callUpdateBillingAgreement()
    {
        $request = $this->_exportToRequest($this->_updateBillingAgreementRequest);
        $response = $this->call('BillAgreementUpdate', $request);
        $this->_importFromResponse($this->_updateBillingAgreementResponse, $response);
    }

    /**
     * CreateRecurringPaymentsProfile call
     *
     * @param Mage_Payment_Model_Recurring_Profile $profile
     */
    public function callCreateRecurringPaymentsProfile(Mage_Payment_Model_Recurring_Profile $profile)
    {
        Varien_Object_Mapper::accumulateByMap($profile, $this, array(
            'token', // EC fields
            // TODO: DP fields
            // profile fields
            'subscriber_name', 'start_datetime', 'internal_reference_id', 'schedule_description',
            'suspension_threshold', 'bill_failed_later', 'period_unit', 'period_frequency', 'period_max_cycles',
            'billing_amount' => 'amount', 'trial_period_unit', 'trial_period_frequency', 'trial_period_max_cycles',
            'trial_billing_amount', 'currency_code', 'shipping_amount', 'tax_amount', 'init_amount', 'init_may_fail',
        ));
        $request = $this->_exportToRequest($this->_createRecurringPaymentsProfileRequest);
        $response = $this->call('CreateRecurringPaymentsProfile', $request);
        $this->_importFromResponse($this->_createRecurringPaymentsProfileResponse, $response);

        $this->setIsProfileActive('ActiveProfile' === $this->getRecurringProfileStatus());
    }

    /**
     * Import callback request array into $this public data
     *
     * @param array $request
     * @return Mage_Paypal_Model_Api_Nvp
     */
    public function importCallbackRequest($request)
    {
        $shippingAddress = new Varien_Object();
        Varien_Object_Mapper::accumulateByMap($request, $shippingAddress, $this->_callbackRequestMap);
        $shippingAddress->setExportedKeys(array_values($this->_callbackRequestMap));
        $this->_applyStreetAndRegionWorkarounds($shippingAddress);
        $this->setCallbackRequestShippingAddress($shippingAddress);
        return $this;
    }

    /**
     * Return callback response
     *
     * @return string
     */
    public function getCallbackResponse()
    {
        $response = array();
        $this->_exportShippingOptions($response);
        if (empty($response)) {
            $response['NO_SHIPPING_OPTION_DETAILS'] = '1';
        }
        $response = $this->_addMethodToRequest(self::CALLBACK_RESPONSE, $response);
        return $this->_buildQuery($response);
    }

    /**
     * Add method to request array
     *
     * @param string $methodName
     * @param array $request
     * @return array
     */
    protected function _addMethodToRequest($methodName, $request)
    {
        $request['method'] = $methodName;
        return $request;
    }

    /**
     * Add method to response array
     *
     * @param string $methodName
     * @param array $response
     * @return array
     */
    protected function _addMethodToResponse($methodName, $response)
    {
        $response['method'] = $methodName;
        return $response;
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
        $request = $this->_addMethodToRequest($methodName, $request);
        $eachCallRequest = $this->_prepareEachCallRequest($methodName);
        $request = $this->_exportToRequest($eachCallRequest, $request);

        $debugData = array('request' => $request);

        try {
            $http = new Varien_Http_Adapter_Curl();
            $config = array('timeout' => 30);
            if ($this->getUseProxy()) {
                $config['proxy'] = $this->getProxyHost(). ':' . $this->getProxyPort();
            }
            $http->setConfig($config);
            $http->write(Zend_Http_Client::POST, $this->getApiEndpoint(), '1.1', array(), $this->_buildQuery($request));
            $response = $http->read();
        }
        catch (Exception $e) {
            $debugData['result'] = array('error' => $e->getMessage(), 'code' => $e->getCode());
            $this->_debug($debugData);
            throw $e;
        }

        $http->close();

        $response = preg_split('/^\r?$/m', $response, 2);
        $response = trim($response[1]);
        $response = $this->_deformatNVP($response);

        $debugData['result'] = $response;
        $this->_debug($debugData);

        // handle transport error
        if ($http->getErrno()) {
            Mage::logException(new Exception(
                sprintf('PayPal NVP CURL connection error #%s: %s', $http->getErrno(), $http->getError())
            ));
            Mage::throwException(Mage::helper('paypal')->__('Unable to communicate with the PayPal gateway.'));
        }

        if ($this->_isCallSuccessful($response)) {
            if ($this->_rawResponseNeeded) {
                $this->setRawSuccessResponseData($response);
            }
            return $response;
        }
        $this->_handleCallErrors($response);
        return $response;
    }

    /**
     * Setter for 'raw response needed' flag
     *
     * @param bool $flag
     * @return Mage_Paypal_Model_Api_Nvp
     */
    public function setRawResponseNeeded($flag)
    {
        $this->_rawResponseNeeded = $flag;
        return $this;
    }

    /**
     * Handle logical errors
     *
     * @param array
     */
    protected function _handleCallErrors($response)
    {
        $errors = array();
        for ($i = 0; isset($response["L_ERRORCODE{$i}"]); $i++) {
            $longMessage = isset($response["L_LONGMESSAGE{$i}"])
                ? preg_replace('/\.$/', '', $response["L_LONGMESSAGE{$i}"]) : '';
            $shortMessage = preg_replace('/\.$/', '', $response["L_SHORTMESSAGE{$i}"]);
            $errors[] = $longMessage
                ? sprintf('%s (#%s: %s).', $longMessage, $response["L_ERRORCODE{$i}"], $shortMessage)
                : sprintf('#%s: %s.', $response["L_ERRORCODE{$i}"], $shortMessage);
        }
        if ($errors) {
            $errors = implode(' ', $errors);
            $e = new Exception(sprintf('PayPal NVP gateway errors: %s Corellation ID: %s. Version: %s.', $errors,
                isset($response['CORRELATIONID']) ? $response['CORRELATIONID'] : '',
                isset($response['VERSION']) ? $response['VERSION'] : ''
            ));
            Mage::logException($e);
            Mage::throwException(Mage::helper('paypal')->__('PayPal gateway has rejected request. %s', $errors));
        }
    }

    /**
     * Catch success calls and collect warnings
     *
     * @param array
     * @return bool| success flag
     */
    protected function _isCallSuccessful($response)
    {
        $ack = strtoupper($response['ACK']);
        $this->_callWarnings = array();
        if ($ack == 'SUCCESS' || $ack == 'SUCCESSWITHWARNING') {
            // collect warnings
            if ($ack == 'SUCCESSWITHWARNING') {
                for ($i = 0; isset($response["L_ERRORCODE{$i}"]); $i++) {
                    $this->_callWarnings[] = $response["L_ERRORCODE{$i}"];
                }
            }
            return true;
        }
        return false;
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
        $address->setExportedKeys(array_values($this->_billingAddressMap));
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
                $address->setExportedKeys(array_merge($address->getExportedKeys(), array('region_id')));
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

    /**
     * Filter for true/false values (converts to boolean)
     *
     * @param mixed $value
     * @return mixed
     */
    protected function _filterToBool($value)
    {
        if ('false' === $value || '0' === $value) {
            return false;
        } elseif ('true' === $value || '1' === $value) {
            return true;
        }
        return $value;
    }

    /**
     * Filter for 'AUTOBILLAMT'
     *
     * @param string $value
     * @return string
     */
    protected function _filterBillFailedLater($value)
    {
        return $value ? 'AddToNextBilling' : 'NoAutoBill';
    }

    /**
     * Filter for 'BILLINGPERIOD' and 'TRIALBILLINGPERIOD'
     *
     * @param string $value
     * @return string
     */
    protected function _filterPeriodUnit($value)
    {
        switch ($value) {
            case 'day':        return 'Day';
            case 'week':       return 'Week';
            case 'semi_month': return 'SemiMonth';
            case 'month':      return 'Month';
            case 'year':       return 'Year';
        }
    }

    /**
     * Filter for 'FAILEDINITAMTACTION'
     *
     * @param string $value
     * @return string
     */
    protected function _filterInitialAmountMayFail($value)
    {
        return $value ? 'ContinueOnFailure' : 'CancelOnFailure';
    }

    /**
     * Filter for billing agreement status
     *
     * @param string $value
     * @return string
     */
    protected function _filterBillingAgreementStatus($value)
    {
        switch ($value) {
            case 'canceled':    return 'Canceled';
            case 'active':      return 'Active';
        }
    }

    /**
     * Return capture type
     *
     * @param Varien_Object $payment
     * @return string
     */
    protected function _getCaptureCompleteType()
    {
        return ($this->getIsCaptureComplete())
                ? $this->_captureTypeComplete
                : $this->_captureTypeNotcomplete;
    }

    /**
     * Return each call request without unused fields in case of Express Checkout Unilateral payments
     *
     * @param string $methodName Current method name
     * @return array
     */
    protected function _prepareEachCallRequest($methodName)
    {
        $expressCheckooutMetods = array(
            self::SET_EXPRESS_CHECKOUT, self::GET_EXPRESS_CHECKOUT_DETAILS, self::DO_EXPRESS_CHECKOUT_PAYMENT
        );
        if (!in_array($methodName, $expressCheckooutMetods) || !$this->_config->shouldUseUnilateralPayments()) {
            return $this->_eachCallRequest;
        }
        return array_diff($this->_eachCallRequest, array('USER', 'PWD', 'SIGNATURE'));
    }

    /**
     * Supplement EC call request fields with additional values if needed
     *
     * @param array $requestFields Standard set of values
     * @return array New set of fields with additional values
     */
    protected function _prepareExpressCheckoutCallRequest($requestFields)
    {
        if ($this->_config->shouldUseUnilateralPayments()) {
            array_push($requestFields, 'SUBJECT');
            return $requestFields;
        }
        return $requestFields;
    }
}
