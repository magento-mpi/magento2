<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Paypal\Model\Api;

use Magento\Payment\Model\Cart;

/**
 * NVP API wrappers model
 * @TODO: move some parts to abstract, don't hesitate to throw exceptions on api calls
 *
 * @method string getToken()
 */
class Nvp extends \Magento\Paypal\Model\Api\AbstractApi
{
    /**
     * Paypal methods definition
     */
    const DO_DIRECT_PAYMENT = 'DoDirectPayment';

    const DO_CAPTURE = 'DoCapture';

    const DO_AUTHORIZATION = 'DoAuthorization';

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
     * Capture type (make authorization close or remain open)
     *
     * @var string
     */
    protected $_captureTypeComplete = 'Complete';

    /**
     * Capture type (make authorization close or remain open)
     *
     * @var string
     */
    protected $_captureTypeNotcomplete = 'NotComplete';

    /**
     * Global public interface map
     *
     * @var array
     */
    protected $_globalMap = array(
        // each call
        'VERSION' => 'version',
        'USER' => 'api_username',
        'PWD' => 'api_password',
        'SIGNATURE' => 'api_signature',
        'BUTTONSOURCE' => 'build_notation_code',

        // for Unilateral payments
        'SUBJECT' => 'business_account',

        // commands
        'PAYMENTACTION' => 'payment_action',
        'RETURNURL' => 'return_url',
        'CANCELURL' => 'cancel_url',
        'INVNUM' => 'inv_num',
        'TOKEN' => 'token',
        'CORRELATIONID' => 'correlation_id',
        'SOLUTIONTYPE' => 'solution_type',
        'GIROPAYCANCELURL' => 'giropay_cancel_url',
        'GIROPAYSUCCESSURL' => 'giropay_success_url',
        'BANKTXNPENDINGURL' => 'giropay_bank_txn_pending_url',
        'IPADDRESS' => 'ip_address',
        'NOTIFYURL' => 'notify_url',
        'RETURNFMFDETAILS' => 'fraud_management_filters_enabled',
        'NOTE' => 'note',
        'REFUNDTYPE' => 'refund_type',
        'ACTION' => 'action',
        'REDIRECTREQUIRED' => 'redirect_required',
        'SUCCESSPAGEREDIRECTREQUESTED' => 'redirect_requested',
        'REQBILLINGADDRESS' => 'require_billing_address',
        // style settings
        'PAGESTYLE' => 'page_style',
        'HDRIMG' => 'hdrimg',
        'HDRBORDERCOLOR' => 'hdrbordercolor',
        'HDRBACKCOLOR' => 'hdrbackcolor',
        'PAYFLOWCOLOR' => 'payflowcolor',
        'LOCALECODE' => 'locale_code',
        'PAL' => 'pal',

        // transaction info
        'TRANSACTIONID' => 'transaction_id',
        'AUTHORIZATIONID' => 'authorization_id',
        'REFUNDTRANSACTIONID' => 'refund_transaction_id',
        'COMPLETETYPE' => 'complete_type',
        'AMT' => 'amount',
        'ITEMAMT' => 'subtotal_amount',
        'GROSSREFUNDAMT' => 'refunded_amount', // possible mistake, check with API reference

        // payment/billing info
        'CURRENCYCODE' => 'currency_code',
        'PAYMENTSTATUS' => 'payment_status',
        'PENDINGREASON' => 'pending_reason',
        'PROTECTIONELIGIBILITY' => 'protection_eligibility',
        'PAYERID' => 'payer_id',
        'PAYERSTATUS' => 'payer_status',
        'ADDRESSID' => 'address_id',
        'ADDRESSSTATUS' => 'address_status',
        'EMAIL' => 'email',

        // backwards compatibility
        'FIRSTNAME' => 'firstname',
        'LASTNAME' => 'lastname',

        // shipping rate
        'SHIPPINGOPTIONNAME' => 'shipping_rate_code',
        'NOSHIPPING' => 'suppress_shipping',

        // paypal direct credit card information
        'CREDITCARDTYPE' => 'credit_card_type',
        'ACCT' => 'credit_card_number',
        'EXPDATE' => 'credit_card_expiration_date',
        'CVV2' => 'credit_card_cvv2',
        'STARTDATE' => 'maestro_solo_issue_date',
        'ISSUENUMBER' => 'maestro_solo_issue_number',
        'CVV2MATCH' => 'cvv2_check_result',
        'AVSCODE' => 'avs_result',
        // cardinal centinel
        'AUTHSTATUS3DS' => 'centinel_authstatus',
        'MPIVENDOR3DS' => 'centinel_mpivendor',
        'CAVV' => 'centinel_cavv',
        'ECI3DS' => 'centinel_eci',
        'XID' => 'centinel_xid',
        'VPAS' => 'centinel_vpas_result',
        'ECISUBMITTED3DS' => 'centinel_eci_result',

        // recurring payments
        'SUBSCRIBERNAME' => 'subscriber_name',
        'PROFILESTARTDATE' => 'start_datetime',
        'PROFILEREFERENCE' => 'internal_reference_id',
        'DESC' => 'schedule_description',
        'MAXFAILEDPAYMENTS' => 'suspension_threshold',
        'AUTOBILLAMT' => 'bill_failed_later',
        'BILLINGPERIOD' => 'period_unit',
        'BILLINGFREQUENCY' => 'period_frequency',
        'TOTALBILLINGCYCLES' => 'period_max_cycles',
        //'AMT' => 'billing_amount', // have to use 'amount', see above
        'TRIALBILLINGPERIOD' => 'trial_period_unit',
        'TRIALBILLINGFREQUENCY' => 'trial_period_frequency',
        'TRIALTOTALBILLINGCYCLES' => 'trial_period_max_cycles',
        'TRIALAMT' => 'trial_billing_amount',
        // 'CURRENCYCODE' => 'currency_code',
        'SHIPPINGAMT' => 'shipping_amount',
        'TAXAMT' => 'tax_amount',
        'INITAMT' => 'init_amount',
        'FAILEDINITAMTACTION' => 'init_may_fail',
        'PROFILEID' => 'recurring_payment_id',
        'PROFILESTATUS' => 'recurring_payment_status',
        'STATUS' => 'status',

        //Next two fields are used for Brazil only
        'TAXID' => 'buyer_tax_id',
        'TAXIDTYPE' => 'buyer_tax_id_type',

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
     * Filter callback for preparing internal amounts to NVP request
     *
     * @var array
     */
    protected $_exportToRequestFilters = array(
        'AMT' => '_filterAmount',
        'ITEMAMT' => '_filterAmount',
        'TRIALAMT' => '_filterAmount',
        'SHIPPINGAMT' => '_filterAmount',
        'TAXAMT' => '_filterAmount',
        'INITAMT' => '_filterAmount',
        'CREDITCARDTYPE' => '_filterCcType',
        //        'PROFILESTARTDATE' => '_filterToPaypalDate',
        'AUTOBILLAMT' => '_filterBillFailedLater',
        'BILLINGPERIOD' => '_filterPeriodUnit',
        'TRIALBILLINGPERIOD' => '_filterPeriodUnit',
        'FAILEDINITAMTACTION' => '_filterInitialAmountMayFail',
        'BILLINGAGREEMENTSTATUS' => '_filterBillingAgreementStatus',
        'NOSHIPPING' => '_filterInt'
    );

    /**
     * Filter callback for preparing internal amounts to NVP request
     *
     * @var array
     */
    protected $_importFromRequestFilters = array(
        'REDIRECTREQUIRED' => '_filterToBool',
        'SUCCESSPAGEREDIRECTREQUESTED' => '_filterToBool',
        'PAYMENTSTATUS' => '_filterPaymentStatusFromNvpToInfo'
    );

    /**
     * Request map for each API call
     *
     * @var string[]
     */
    protected $_eachCallRequest = array('VERSION', 'USER', 'PWD', 'SIGNATURE', 'BUTTONSOURCE');

    /**
     * SetExpressCheckout request map
     *
     * @var string[]
     */
    protected $_setExpressCheckoutRequest = array(
        'PAYMENTACTION',
        'AMT',
        'CURRENCYCODE',
        'RETURNURL',
        'CANCELURL',
        'INVNUM',
        'SOLUTIONTYPE',
        'NOSHIPPING',
        'GIROPAYCANCELURL',
        'GIROPAYSUCCESSURL',
        'BANKTXNPENDINGURL',
        'PAGESTYLE',
        'HDRIMG',
        'HDRBORDERCOLOR',
        'HDRBACKCOLOR',
        'PAYFLOWCOLOR',
        'LOCALECODE',
        'BILLINGTYPE',
        'SUBJECT',
        'ITEMAMT',
        'SHIPPINGAMT',
        'TAXAMT',
        'REQBILLINGADDRESS'
    );

    /**
     * SetExpressCheckout response map
     *
     * @var string[]
     */
    protected $_setExpressCheckoutResponse = array('TOKEN');

    /**
     * GetExpressCheckoutDetails request map
     *
     * @var string[]
     */
    protected $_getExpressCheckoutDetailsRequest = array('TOKEN', 'SUBJECT');

    /**
     * DoExpressCheckoutPayment request map
     *
     * @var string[]
     */
    protected $_doExpressCheckoutPaymentRequest = array(
        'TOKEN',
        'PAYERID',
        'PAYMENTACTION',
        'AMT',
        'CURRENCYCODE',
        'IPADDRESS',
        'BUTTONSOURCE',
        'NOTIFYURL',
        'RETURNFMFDETAILS',
        'SUBJECT',
        'ITEMAMT',
        'SHIPPINGAMT',
        'TAXAMT'
    );

    /**
     * DoExpressCheckoutPayment response map
     *
     * @var string[]
     */
    protected $_doExpressCheckoutPaymentResponse = array(
        'TRANSACTIONID',
        'AMT',
        'PAYMENTSTATUS',
        'PENDINGREASON',
        'REDIRECTREQUIRED'
    );

    /**
     * DoDirectPayment request map
     *
     * @var string[]
     */
    protected $_doDirectPaymentRequest = array(
        'PAYMENTACTION',
        'IPADDRESS',
        'RETURNFMFDETAILS',
        'AMT',
        'CURRENCYCODE',
        'INVNUM',
        'NOTIFYURL',
        'EMAIL',
        'ITEMAMT',
        'SHIPPINGAMT',
        'TAXAMT',
        'CREDITCARDTYPE',
        'ACCT',
        'EXPDATE',
        'CVV2',
        'STARTDATE',
        'ISSUENUMBER',
        'AUTHSTATUS3DS',
        'MPIVENDOR3DS',
        'CAVV',
        'ECI3DS',
        'XID'
    );

    /**
     * DoDirectPayment response map
     *
     * @var string[]
     */
    protected $_doDirectPaymentResponse = array(
        'TRANSACTIONID',
        'AMT',
        'AVSCODE',
        'CVV2MATCH',
        'VPAS',
        'ECISUBMITTED3DS'
    );

    /**
     * DoReauthorization request map
     *
     * @var string[]
     */
    protected $_doReauthorizationRequest = array('AUTHORIZATIONID', 'AMT', 'CURRENCYCODE');

    /**
     * DoReauthorization response map
     *
     * @var string[]
     */
    protected $_doReauthorizationResponse = array(
        'AUTHORIZATIONID',
        'PAYMENTSTATUS',
        'PENDINGREASON',
        'PROTECTIONELIGIBILITY'
    );

    /**
     * DoCapture request map
     *
     * @var string[]
     */
    protected $_doCaptureRequest = array('AUTHORIZATIONID', 'COMPLETETYPE', 'AMT', 'CURRENCYCODE', 'NOTE', 'INVNUM');

    /**
     * DoCapture response map
     *
     * @var string[]
     */
    protected $_doCaptureResponse = array('TRANSACTIONID', 'CURRENCYCODE', 'AMT', 'PAYMENTSTATUS', 'PENDINGREASON');

    /**
     * DoAuthorization request map
     *
     * @var string[]
     */
    protected $_doAuthorizationRequest = array('TRANSACTIONID', 'AMT', 'CURRENCYCODE');

    /**
     * DoAuthorization response map
     *
     * @var string[]
     */
    protected $_doAuthorizationResponse = array('TRANSACTIONID', 'AMT');

    /**
     * DoVoid request map
     *
     * @var string[]
     */
    protected $_doVoidRequest = array('AUTHORIZATIONID', 'NOTE');

    /**
     * GetTransactionDetailsRequest
     *
     * @var string[]
     */
    protected $_getTransactionDetailsRequest = array('TRANSACTIONID');

    /**
     * GetTransactionDetailsResponse
     *
     * @var string[]
     */
    protected $_getTransactionDetailsResponse = array(
        'PAYERID',
        'FIRSTNAME',
        'LASTNAME',
        'TRANSACTIONID',
        'PARENTTRANSACTIONID',
        'CURRENCYCODE',
        'AMT',
        'PAYMENTSTATUS',
        'PENDINGREASON'
    );

    /**
     * RefundTransaction request map
     *
     * @var string[]
     */
    protected $_refundTransactionRequest = array('TRANSACTIONID', 'REFUNDTYPE', 'CURRENCYCODE', 'NOTE');

    /**
     * RefundTransaction response map
     *
     * @var string[]
     */
    protected $_refundTransactionResponse = array('REFUNDTRANSACTIONID', 'GROSSREFUNDAMT');

    /**
     * ManagePendingTransactionStatus request map
     *
     * @var string[]
     */
    protected $_managePendingTransactionStatusRequest = array('TRANSACTIONID', 'ACTION');

    /**
     * ManagePendingTransactionStatus response map
     *
     * @var string[]
     */
    protected $_managePendingTransactionStatusResponse = array('TRANSACTIONID', 'STATUS');

    /**
     * GetPalDetails response map
     *
     * @var string[]
     */
    protected $_getPalDetailsResponse = array('PAL');

    /**
     * Map for billing address import/export
     *
     * @var array
     */
    protected $_billingAddressMap = array(
        'BUSINESS' => 'company',
        'NOTETEXT' => 'customer_notes',
        'EMAIL' => 'email',
        'FIRSTNAME' => 'firstname',
        'LASTNAME' => 'lastname',
        'MIDDLENAME' => 'middlename',
        'SALUTATION' => 'prefix',
        'SUFFIX' => 'suffix',
        'COUNTRYCODE' => 'country_id', // iso-3166 two-character code
        'STATE' => 'region',
        'CITY' => 'city',
        'STREET' => 'street',
        'STREET2' => 'street2',
        'ZIP' => 'postcode',
        'PHONENUM' => 'telephone'
    );

    /**
     * Map for billing address to do request (not response)
     * Merging with $_billingAddressMap
     *
     * @var array
     */
    protected $_billingAddressMapRequest = array();

    /**
     * Map for shipping address import/export (extends billing address mapper)
     * @var array
     */
    protected $_shippingAddressMap = array(
        'SHIPTOCOUNTRYCODE' => 'country_id',
        'SHIPTOSTATE' => 'region',
        'SHIPTOCITY' => 'city',
        'SHIPTOSTREET' => 'street',
        'SHIPTOSTREET2' => 'street2',
        'SHIPTOZIP' => 'postcode',
        'SHIPTOPHONENUM' => 'telephone'
        // 'SHIPTONAME' will be treated manually in address import/export methods
    );

    /**
     * Map for callback request
     * @var array
     */
    protected $_callbackRequestMap = array(
        'SHIPTOCOUNTRY' => 'country_id',
        'SHIPTOSTATE' => 'region',
        'SHIPTOCITY' => 'city',
        'SHIPTOSTREET' => 'street',
        'SHIPTOSTREET2' => 'street2',
        'SHIPTOZIP' => 'postcode'
    );

    /**
     * Payment information response specifically to be collected after some requests
     * @var string[]
     */
    protected $_paymentInformationResponse = array(
        'PAYERID',
        'PAYERSTATUS',
        'CORRELATIONID',
        'ADDRESSID',
        'ADDRESSSTATUS',
        'PAYMENTSTATUS',
        'PENDINGREASON',
        'PROTECTIONELIGIBILITY',
        'EMAIL',
        'SHIPPINGOPTIONNAME',
        'TAXID',
        'TAXIDTYPE'
    );

    /**
     * Line items export mapping settings
     * @var array
     */
    protected $_lineItemTotalExportMap = array(
        Cart::AMOUNT_SUBTOTAL => 'ITEMAMT',
        Cart::AMOUNT_TAX => 'TAXAMT',
        Cart::AMOUNT_SHIPPING => 'SHIPPINGAMT'
    );

    /**
     * Line items export mapping settings
     * @var array
     */
    protected $_lineItemExportItemsFormat = array(
        'id' => 'L_NUMBER%d',
        'name' => 'L_NAME%d',
        'qty' => 'L_QTY%d',
        'amount' => 'L_AMT%d'
    );

    /**
     * Shipping options export to request mapping settings
     * @var array
     */
    protected $_shippingOptionsExportItemsFormat = array(
        'is_default' => 'L_SHIPPINGOPTIONISDEFAULT%d',
        'amount' => 'L_SHIPPINGOPTIONAMOUNT%d',
        'code' => 'L_SHIPPINGOPTIONNAME%d',
        'name' => 'L_SHIPPINGOPTIONLABEL%d',
        'tax_amount' => 'L_TAXAMT%d'
    );

    /**
     * init Billing Agreement request map
     *
     * @var string[]
     */
    protected $_customerBillingAgreementRequest = array('RETURNURL', 'CANCELURL', 'BILLINGTYPE');

    /**
     * init Billing Agreement response map
     *
     * @var string[]
     */
    protected $_customerBillingAgreementResponse = array('TOKEN');

    /**
     * Billing Agreement details request map
     *
     * @var string[]
     */
    protected $_billingAgreementCustomerDetailsRequest = array('TOKEN');

    /**
     * Billing Agreement details response map
     *
     * @var string[]
     */
    protected $_billingAgreementCustomerDetailsResponse = array(
        'EMAIL',
        'PAYERID',
        'PAYERSTATUS',
        'SHIPTOCOUNTRYCODE',
        'PAYERBUSINESS'
    );

    /**
     * Create Billing Agreement request map
     *
     * @var string[]
     */
    protected $_createBillingAgreementRequest = array('TOKEN');

    /**
     * Create Billing Agreement response map
     *
     * @var string[]
     */
    protected $_createBillingAgreementResponse = array('BILLINGAGREEMENTID');

    /**
     * Update Billing Agreement request map
     *
     * @var string[]
     */
    protected $_updateBillingAgreementRequest = array(
        'REFERENCEID',
        'BILLINGAGREEMENTDESCRIPTION',
        'BILLINGAGREEMENTSTATUS',
        'BILLINGAGREEMENTCUSTOM'
    );

    /**
     * Update Billing Agreement response map
     *
     * @var string[]
     */
    protected $_updateBillingAgreementResponse = array(
        'REFERENCEID',
        'BILLINGAGREEMENTDESCRIPTION',
        'BILLINGAGREEMENTSTATUS',
        'BILLINGAGREEMENTCUSTOM'
    );

    /**
     * Do Reference Transaction request map
     *
     * @var string[]
     */
    protected $_doReferenceTransactionRequest = array(
        'REFERENCEID',
        'PAYMENTACTION',
        'AMT',
        'ITEMAMT',
        'SHIPPINGAMT',
        'TAXAMT',
        'INVNUM',
        'NOTIFYURL',
        'CURRENCYCODE'
    );

    /**
     * Do Reference Transaction response map
     *
     * @var string[]
     */
    protected $_doReferenceTransactionResponse = array('BILLINGAGREEMENTID', 'TRANSACTIONID');

    /**
     * Fields that should be replaced in debug with '***'
     *
     * @var string[]
     */
    protected $_debugReplacePrivateDataKeys = array(
        'ACCT',
        'EXPDATE',
        'CVV2',
        'CARDISSUE',
        'CARDSTART',
        'CREDITCARDTYPE',
        'USER',
        'PWD',
        'SIGNATURE'
    );

    /**
     * Map of credit card types supported by this API
     *
     * @var array
     */
    protected $_supportedCcTypes = array(
        'VI' => 'Visa',
        'MC' => 'MasterCard',
        'DI' => 'Discover',
        'AE' => 'Amex',
        'SM' => 'Maestro',
        'SO' => 'Solo'
    );

    /**
     * Required fields in the response
     *
     * @var array
     */
    protected $_requiredResponseParams = array(self::DO_DIRECT_PAYMENT => array('ACK', 'CORRELATIONID', 'AMT'));

    /**
     * Warning codes recollected after each API call
     *
     * @var array
     */
    protected $_callWarnings = array();

    /**
     * Error codes recollected after each API call
     *
     * @var array
     */
    protected $_callErrors = array();

    /**
     * Whether to return raw response information after each call
     *
     * @var bool
     */
    protected $_rawResponseNeeded = false;

    /**
     * @var \Magento\Directory\Model\CountryFactory
     */
    protected $_countryFactory;

    /**
     * @param \Magento\Customer\Helper\Address $customerAddress
     * @param \Magento\Logger $logger
     * @param \Magento\Locale\ResolverInterface $localeResolver
     * @param \Magento\Directory\Model\RegionFactory $regionFactory
     * @param \Magento\Logger\AdapterFactory $logAdapterFactory
     * @param \Magento\Directory\Model\CountryFactory $countryFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Customer\Helper\Address $customerAddress,
        \Magento\Logger $logger,
        \Magento\Locale\ResolverInterface $localeResolver,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \Magento\Logger\AdapterFactory $logAdapterFactory,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        array $data = array()
    ) {
        parent::__construct($customerAddress, $logger, $localeResolver, $regionFactory, $logAdapterFactory, $data);
        $this->_countryFactory = $countryFactory;
    }

    /**
     * API endpoint getter
     *
     * @return string
     */
    public function getApiEndpoint()
    {
        $url = $this->getUseCertAuthentication() ? 'https://api%s.paypal.com/nvp' : 'https://api-3t%s.paypal.com/nvp';
        return sprintf($url, $this->_config->sandboxFlag ? '.sandbox' : '');
    }

    /**
     * Return Paypal Api version
     *
     * @return string
     */
    public function getVersion()
    {
        return '72.0';
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
     *
     * TODO: put together style and giropay settings
     *
     * @return void
     * @link https://cms.paypal.com/us/cgi-bin/?&cmd=_render-content&content_ID=developer/e_howto_api_nvp_r_SetExpressCheckout
     */
    public function callSetExpressCheckout()
    {
        $this->_prepareExpressCheckoutCallRequest($this->_setExpressCheckoutRequest);
        $request = $this->_exportToRequest($this->_setExpressCheckoutRequest);
        $this->_exportLineItems($request);

        // import/suppress shipping address, if any
        $options = $this->getShippingOptions();
        if ($this->getAddress()) {
            $request = $this->_importAddresses($request);
            $request['ADDROVERRIDE'] = 1;
        } elseif ($options && count($options) <= 10) {
            // doesn't support more than 10 shipping options
            $request['CALLBACK'] = $this->getShippingOptionsCallbackUrl();
            $request['CALLBACKTIMEOUT'] = 6;
            // max value
            $request['MAXAMT'] = $request['AMT'] + 999.00;
            // it is impossible to calculate max amount
            $this->_exportShippingOptions($request);
        }

        $response = $this->call(self::SET_EXPRESS_CHECKOUT, $request);
        $this->_importFromResponse($this->_setExpressCheckoutResponse, $response);
    }

    /**
     * GetExpressCheckoutDetails call
     *
     * @return void
     * @link https://cms.paypal.com/us/cgi-bin/?&cmd=_render-content&content_ID=developer/e_howto_api_nvp_r_GetExpressCheckoutDetails
     */
    public function callGetExpressCheckoutDetails()
    {
        $this->_prepareExpressCheckoutCallRequest($this->_getExpressCheckoutDetailsRequest);
        $request = $this->_exportToRequest($this->_getExpressCheckoutDetailsRequest);
        $response = $this->call(self::GET_EXPRESS_CHECKOUT_DETAILS, $request);
        $this->_importFromResponse($this->_paymentInformationResponse, $response);
        $this->_exportAddressses($response);
    }

    /**
     * DoExpressCheckout call
     *
     * @return void
     * @link https://cms.paypal.com/us/cgi-bin/?&cmd=_render-content&content_ID=developer/e_howto_api_nvp_r_DoExpressCheckoutPayment
     */
    public function callDoExpressCheckoutPayment()
    {
        $this->_prepareExpressCheckoutCallRequest($this->_doExpressCheckoutPaymentRequest);
        $request = $this->_exportToRequest($this->_doExpressCheckoutPaymentRequest);
        $this->_exportLineItems($request);

        if ($this->getAddress()) {
            $request = $this->_importAddresses($request);
            $request['ADDROVERRIDE'] = 1;
        }

        $response = $this->call(self::DO_EXPRESS_CHECKOUT_PAYMENT, $request);
        $this->_importFromResponse($this->_paymentInformationResponse, $response);
        $this->_importFromResponse($this->_doExpressCheckoutPaymentResponse, $response);
        $this->_importFromResponse($this->_createBillingAgreementResponse, $response);
    }

    /**
     * Process a credit card payment
     *
     * @return void
     */
    public function callDoDirectPayment()
    {
        $request = $this->_exportToRequest($this->_doDirectPaymentRequest);
        $this->_exportLineItems($request);
        if ($this->getAddress()) {
            $request = $this->_importAddresses($request);
        }
        $response = $this->call(self::DO_DIRECT_PAYMENT, $request);
        $this->_importFromResponse($this->_doDirectPaymentResponse, $response);
    }

    /**
     * Do Reference Transaction call
     *
     * @return void
     * @link https://cms.paypal.com/us/cgi-bin/?&cmd=_render-content&content_ID=developer/e_howto_api_nvp_r_DoReferenceTransaction
     */
    public function callDoReferenceTransaction()
    {
        $request = $this->_exportToRequest($this->_doReferenceTransactionRequest);
        $this->_exportLineItems($request);
        $response = $this->call('DoReferenceTransaction', $request);
        $this->_importFromResponse($this->_doReferenceTransactionResponse, $response);
    }

    /**
     * Check whether the last call was returned with fraud warning
     *
     * @return bool
     */
    public function getIsFraudDetected()
    {
        return in_array(11610, $this->_callWarnings);
    }

    /**
     * Made additional request to paypal to get autharization id
     *
     * @return void
     */
    public function callDoReauthorization()
    {
        $request = $this->_export($this->_doReauthorizationRequest);
        $response = $this->call('DoReauthorization', $request);
        $this->_import($response, $this->_doReauthorizationResponse);
    }

    /**
     * DoCapture call
     *
     * @return void
     * @link https://cms.paypal.com/us/cgi-bin/?&cmd=_render-content&content_ID=developer/e_howto_api_nvp_r_DoCapture
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
     * DoAuthorization call
     *
     * @return $this
     * @link https://cms.paypal.com/us/cgi-bin/?&cmd=_render-content&content_ID=developer/e_howto_api_nvp_r_DoAuthorization
     */
    public function callDoAuthorization()
    {
        $request = $this->_exportToRequest($this->_doAuthorizationRequest);
        $response = $this->call(self::DO_AUTHORIZATION, $request);
        $this->_importFromResponse($this->_paymentInformationResponse, $response);
        $this->_importFromResponse($this->_doAuthorizationResponse, $response);

        return $this;
    }

    /**
     * DoVoid call
     *
     * @return void
     * @link https://cms.paypal.com/us/cgi-bin/?&cmd=_render-content&content_ID=developer/e_howto_api_nvp_r_DoVoid
     */
    public function callDoVoid()
    {
        $request = $this->_exportToRequest($this->_doVoidRequest);
        $this->call(self::DO_VOID, $request);
    }

    /**
     * GetTransactionDetails
     *
     * @return void
     * @link https://cms.paypal.com/us/cgi-bin/?&cmd=_render-content&content_ID=developer/e_howto_api_nvp_r_GetTransactionDetails
     */
    public function callGetTransactionDetails()
    {
        $request = $this->_exportToRequest($this->_getTransactionDetailsRequest);
        $response = $this->call('GetTransactionDetails', $request);
        $this->_importFromResponse($this->_getTransactionDetailsResponse, $response);
    }

    /**
     * RefundTransaction call
     *
     * @return void
     * @link https://cms.paypal.com/us/cgi-bin/?&cmd=_render-content&content_ID=developer/e_howto_api_nvp_r_RefundTransaction
     */
    public function callRefundTransaction()
    {
        $request = $this->_exportToRequest($this->_refundTransactionRequest);
        if ($this->getRefundType() === \Magento\Paypal\Model\Config::REFUND_TYPE_PARTIAL) {
            $request['AMT'] = $this->getAmount();
        }
        $response = $this->call(self::REFUND_TRANSACTION, $request);
        $this->_importFromResponse($this->_refundTransactionResponse, $response);
    }

    /**
     * ManagePendingTransactionStatus
     *
     * @return void
     * @link https://cms.paypal.com/us/cgi-bin/?&cmd=_render-content&content_ID=developer/e_howto_api_nvp_r_ManagePendingTransactionStatus
     */
    public function callManagePendingTransactionStatus()
    {
        $request = $this->_exportToRequest($this->_managePendingTransactionStatusRequest);
        if (isset($request['ACTION'])) {
            $request['ACTION'] = $this->_filterPaymentReviewAction($request['ACTION']);
        }
        $response = $this->call('ManagePendingTransactionStatus', $request);
        $this->_importFromResponse($this->_managePendingTransactionStatusResponse, $response);
    }

    /**
     * GetPalDetails call
     *
     * @return void
     * @link https://www.x.com/docs/DOC-1300
     * @link https://cms.paypal.com/us/cgi-bin/?cmd=_render-content&content_ID=developer/e_howto_api_ECButtonIntegration
     */
    public function callGetPalDetails()
    {
        $response = $this->call('getPalDetails', array());
        $this->_importFromResponse($this->_getPalDetailsResponse, $response);
    }

    /**
     * Set Customer BillingA greement call
     *
     * @return void
     * @link https://cms.paypal.com/us/cgi-bin/?&cmd=_render-content&content_ID=developer/e_howto_api_nvp_r_SetCustomerBillingAgreement
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
     * @return void
     * @link https://cms.paypal.com/us/cgi-bin/?&cmd=_render-content&content_ID=developer/e_howto_api_nvp_r_GetBillingAgreementCustomerDetails
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
     * @return void
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
     * @return void
     */
    public function callUpdateBillingAgreement()
    {
        $request = $this->_exportToRequest($this->_updateBillingAgreementRequest);
        try {
            $response = $this->call('BillAgreementUpdate', $request);
        } catch (\Magento\Core\Exception $e) {
            if (in_array(10201, $this->_callErrors)) {
                $this->setIsBillingAgreementAlreadyCancelled(true);
            }
            throw $e;
        }
        $this->_importFromResponse($this->_updateBillingAgreementResponse, $response);
    }

    /**
     * Import callback request array into $this public data
     *
     * @param array $request
     * @return \Magento\Object
     */
    public function prepareShippingOptionsCallbackAddress(array $request)
    {
        $address = new \Magento\Object();
        \Magento\Object\Mapper::accumulateByMap($request, $address, $this->_callbackRequestMap);
        $address->setExportedKeys(array_values($this->_callbackRequestMap));
        $this->_applyStreetAndRegionWorkarounds($address);
        return $address;
    }

    /**
     * Prepare response for shipping options callback
     *
     * @return string
     */
    public function formatShippingOptionsCallback()
    {
        $response = array();
        if (!$this->_exportShippingOptions($response)) {
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
        $request['METHOD'] = $methodName;
        return $request;
    }

    /**
     * Retrieve headers for request.
     *
     * @return array
     */
    protected function _getHeaderListForRequest()
    {
        return array();
    }

    /**
     * Additional response processing.
     *
     * @param  array $response
     * @return array
     */
    protected function _postProcessResponse($response)
    {
        return $response;
    }

    /**
     * Do the API call
     *
     * @param string $methodName
     * @param array $request
     * @return array
     * @throws \Magento\Core\Exception|\Exception
     */
    public function call($methodName, array $request)
    {
        $request = $this->_addMethodToRequest($methodName, $request);
        $eachCallRequest = $this->_prepareEachCallRequest($methodName);
        if ($this->getUseCertAuthentication()) {
            $key = array_search('SIGNATURE', $eachCallRequest);
            if ($key) {
                unset($eachCallRequest[$key]);
            }
        }
        $request = $this->_exportToRequest($eachCallRequest, $request);
        $debugData = array('url' => $this->getApiEndpoint(), $methodName => $request);

        try {
            $http = new \Magento\HTTP\Adapter\Curl();
            $config = array('timeout' => 60, 'verifypeer' => $this->_config->verifyPeer);
            if ($this->getUseProxy()) {
                $config['proxy'] = $this->getProxyHost() . ':' . $this->getProxyPort();
            }
            if ($this->getUseCertAuthentication()) {
                $config['ssl_cert'] = $this->getApiCertificate();
            }
            $http->setConfig($config);
            $http->write(
                \Zend_Http_Client::POST,
                $this->getApiEndpoint(),
                '1.1',
                $this->_getHeaderListForRequest(),
                $this->_buildQuery($request)
            );
            $response = $http->read();
        } catch (\Exception $e) {
            $debugData['http_error'] = array('error' => $e->getMessage(), 'code' => $e->getCode());
            $this->_debug($debugData);
            throw $e;
        }

        $response = preg_split('/^\r?$/m', $response, 2);
        $response = trim($response[1]);
        $response = $this->_deformatNVP($response);

        $debugData['response'] = $response;
        $this->_debug($debugData);

        $response = $this->_postProcessResponse($response);

        // handle transport error
        if ($http->getErrno()) {
            $this->_logger->logException(
                new \Exception(
                    sprintf('PayPal NVP CURL connection error #%s: %s', $http->getErrno(), $http->getError())
                )
            );
            $http->close();

            throw new \Magento\Core\Exception(__('We can\'t contact the PayPal gateway.'));
        }

        // cUrl resource must be closed after checking it for errors
        $http->close();

        if (!$this->_validateResponse($methodName, $response)) {
            $this->_logger->logException(new \Exception(__("PayPal response hasn't required fields.")));
            throw new \Magento\Core\Exception(__('Something went wrong while processing your order.'));
        }

        $this->_callErrors = array();
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
     * @return $this
     */
    public function setRawResponseNeeded($flag)
    {
        $this->_rawResponseNeeded = $flag;
        return $this;
    }

    /**
     * Handle logical errors
     *
     * @param array $response
     * @return void
     * @throws \Magento\Core\Exception
     */
    protected function _handleCallErrors($response)
    {
        $errors = array();
        for ($i = 0; isset($response["L_ERRORCODE{$i}"]); $i++) {
            $longMessage = isset(
                $response["L_LONGMESSAGE{$i}"]
            ) ? preg_replace(
                '/\.$/',
                '',
                $response["L_LONGMESSAGE{$i}"]
            ) : '';
            $shortMessage = preg_replace('/\.$/', '', $response["L_SHORTMESSAGE{$i}"]);
            $errors[] = $longMessage ? sprintf(
                '%s (#%s: %s).',
                $longMessage,
                $response["L_ERRORCODE{$i}"],
                $shortMessage
            ) : sprintf(
                '#%s: %s.',
                $response["L_ERRORCODE{$i}"],
                $shortMessage
            );
            $this->_callErrors[] = $response["L_ERRORCODE{$i}"];
        }
        if ($errors) {
            $errors = implode(' ', $errors);
            $e = new \Magento\Core\Exception(
                sprintf(
                    'PayPal NVP gateway errors: %s Correlation ID: %s. Version: %s.',
                    $errors,
                    isset($response['CORRELATIONID']) ? $response['CORRELATIONID'] : '',
                    isset($response['VERSION']) ? $response['VERSION'] : ''
                )
            );
            $this->_logger->logException($e);
            $e->setMessage(__('The PayPal gateway has rejected this request. %1', $errors));
            throw $e;
        }
    }

    /**
     * Catch success calls and collect warnings
     *
     * @param array $response
     * @return bool success flag
     */
    protected function _isCallSuccessful($response)
    {
        if (!isset($response['ACK'])) {
            return false;
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
            return true;
        }
        return false;
    }

    /**
     * Validate response array.
     *
     * @param string $method
     * @param array $response
     * @return bool
     */
    protected function _validateResponse($method, $response)
    {
        if (isset($this->_requiredResponseParams[$method])) {
            foreach ($this->_requiredResponseParams[$method] as $param) {
                if (!isset($response[$param])) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Parse an NVP response string into an associative array
     * @param string $nvpstr
     * @return array
     */
    protected function _deformatNVP($nvpstr)
    {
        $intial = 0;
        $nvpArray = array();

        $nvpstr = strpos($nvpstr, "\r\n\r\n") !== false ? substr($nvpstr, strpos($nvpstr, "\r\n\r\n") + 4) : $nvpstr;

        while (strlen($nvpstr)) {
            //postion of Key
            $keypos = strpos($nvpstr, '=');
            //position of value
            $valuepos = strpos($nvpstr, '&') ? strpos($nvpstr, '&') : strlen($nvpstr);

            /*getting the Key and Value values and storing in a Associative Array*/
            $keyval = substr($nvpstr, $intial, $keypos);
            $valval = substr($nvpstr, $keypos + 1, $valuepos - $keypos - 1);
            //decoding the respose
            $nvpArray[urldecode($keyval)] = urldecode($valval);
            $nvpstr = substr($nvpstr, $valuepos + 1, strlen($nvpstr));
        }
        return $nvpArray;
    }

    /**
     * NVP doesn't support passing discount total as a separate amount - add it as a line item
     *
     * @param array $request
     * @param int $i
     * @return true|null
     */
    protected function _exportLineItems(array &$request, $i = 0)
    {
        if (!$this->_cart) {
            return;
        }
        $this->_cart->setTransferDiscountAsItem();
        return parent::_exportLineItems($request, $i);
    }

    /**
     * Create billing and shipping addresses basing on response data
     *
     * @param array $data
     * @return void
     */
    protected function _exportAddressses($data)
    {
        $address = new \Magento\Object();
        \Magento\Object\Mapper::accumulateByMap($data, $address, $this->_billingAddressMap);
        $address->setExportedKeys(array_values($this->_billingAddressMap));
        $this->_applyStreetAndRegionWorkarounds($address);
        $this->setExportedBillingAddress($address);
        // assume there is shipping address if there is at least one field specific to shipping
        if (isset($data['SHIPTONAME'])) {
            $shippingAddress = clone $address;
            \Magento\Object\Mapper::accumulateByMap($data, $shippingAddress, $this->_shippingAddressMap);
            $this->_applyStreetAndRegionWorkarounds($shippingAddress);
            // PayPal doesn't provide detailed shipping name fields, so the name will be overwritten
            $firstName = $data['SHIPTONAME'];
            $lastName = null;
            if (isset($data['FIRSTNAME']) && $data['LASTNAME']) {
                $firstName = $data['FIRSTNAME'];
                $lastName = $data['LASTNAME'];
            }
            $shippingAddress->addData(
                array(
                    'prefix' => null,
                    'firstname' => $firstName,
                    'middlename' => null,
                    'lastname' => $lastName,
                    'suffix' => null
                )
            );
            $this->setExportedShippingAddress($shippingAddress);
        }
    }

    /**
     * Adopt specified address object to be compatible with Magento
     *
     * @param \Magento\Object $address
     * @return void
     */
    protected function _applyStreetAndRegionWorkarounds(\Magento\Object $address)
    {
        // merge street addresses into 1
        if ($address->hasStreet2()) {
            $address->setStreet(implode("\n", array($address->getStreet(), $address->getStreet2())));
            $address->unsStreet2();
        }
        // attempt to fetch region_id from directory
        if ($address->getCountryId() && $address->getRegion()) {
            $regions = $this->_countryFactory->create()->loadByCode(
                $address->getCountryId()
            )->getRegionCollection()->addRegionCodeOrNameFilter(
                $address->getRegion()
            )->setPageSize(
                1
            );
            foreach ($regions as $region) {
                $address->setRegionId($region->getId());
                $address->setExportedKeys(array_merge($address->getExportedKeys(), array('region_id')));
                break;
            }
        }
    }

    /**
     * Adopt specified request array to be compatible with Paypal
     * Puerto Rico should be as state of USA and not as a country
     *
     * @param array &$request
     * @return void
     */
    protected function _applyCountryWorkarounds(&$request)
    {
        if (isset($request['SHIPTOCOUNTRYCODE']) && $request['SHIPTOCOUNTRYCODE'] == 'PR') {
            $request['SHIPTOCOUNTRYCODE'] = 'US';
            $request['SHIPTOSTATE'] = 'PR';
        }
    }

    /**
     * Prepare request data basing on provided addresses
     *
     * @param array $to
     * @return array
     */
    protected function _importAddresses(array $to)
    {
        $billingAddress = $this->getBillingAddress() ? $this->getBillingAddress() : $this->getAddress();
        $shippingAddress = $this->getAddress();

        $to = \Magento\Object\Mapper::accumulateByMap(
            $billingAddress,
            $to,
            array_merge(array_flip($this->_billingAddressMap), $this->_billingAddressMapRequest)
        );
        $regionCode = $this->_lookupRegionCodeFromAddress($billingAddress);
        if ($regionCode) {
            $to['STATE'] = $regionCode;
        }
        if (!$this->getSuppressShipping()) {
            $to = \Magento\Object\Mapper::accumulateByMap(
                $shippingAddress,
                $to,
                array_flip($this->_shippingAddressMap)
            );
            $regionCode = $this->_lookupRegionCodeFromAddress($shippingAddress);
            if ($regionCode) {
                $to['SHIPTOSTATE'] = $regionCode;
            }
            $this->_importStreetFromAddress($shippingAddress, $to, 'SHIPTOSTREET', 'SHIPTOSTREET2');
            $this->_importStreetFromAddress($billingAddress, $to, 'STREET', 'STREET2');
            $to['SHIPTONAME'] = $shippingAddress->getName();
        }
        $this->_applyCountryWorkarounds($to);
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
     * @return bool|mixed
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
            case 'day':
                return 'Day';
            case 'week':
                return 'Week';
            case 'semi_month':
                return 'SemiMonth';
            case 'month':
                return 'Month';
            case 'year':
                return 'Year';
            default:
                break;
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
            case 'canceled':
                return 'Canceled';
            case 'active':
                return 'Active';
            default:
                break;
        }
    }

    /**
     * Convert payment status from NVP format to paypal/info model format
     *
     * @param string $value
     * @return string|null
     */
    protected function _filterPaymentStatusFromNvpToInfo($value)
    {
        switch ($value) {
            case 'None':
                return \Magento\Paypal\Model\Info::PAYMENTSTATUS_NONE;
            case 'Completed':
                return \Magento\Paypal\Model\Info::PAYMENTSTATUS_COMPLETED;
            case 'Denied':
                return \Magento\Paypal\Model\Info::PAYMENTSTATUS_DENIED;
            case 'Expired':
                return \Magento\Paypal\Model\Info::PAYMENTSTATUS_EXPIRED;
            case 'Failed':
                return \Magento\Paypal\Model\Info::PAYMENTSTATUS_FAILED;
            case 'In-Progress':
                return \Magento\Paypal\Model\Info::PAYMENTSTATUS_INPROGRESS;
            case 'Pending':
                return \Magento\Paypal\Model\Info::PAYMENTSTATUS_PENDING;
            case 'Refunded':
                return \Magento\Paypal\Model\Info::PAYMENTSTATUS_REFUNDED;
            case 'Partially-Refunded':
                return \Magento\Paypal\Model\Info::PAYMENTSTATUS_REFUNDEDPART;
            case 'Reversed':
                return \Magento\Paypal\Model\Info::PAYMENTSTATUS_REVERSED;
            case 'Canceled-Reversal':
                return \Magento\Paypal\Model\Info::PAYMENTSTATUS_UNREVERSED;
            case 'Processed':
                return \Magento\Paypal\Model\Info::PAYMENTSTATUS_PROCESSED;
            case 'Voided':
                return \Magento\Paypal\Model\Info::PAYMENTSTATUS_VOIDED;
            default:
                break;
        }
    }

    /**
     * Convert payment review action to NVP-compatible value
     *
     * @param string $value
     * @return string|null
     */
    protected function _filterPaymentReviewAction($value)
    {
        switch ($value) {
            case \Magento\Paypal\Model\Pro::PAYMENT_REVIEW_ACCEPT:
                return 'Accept';
            case \Magento\Paypal\Model\Pro::PAYMENT_REVIEW_DENY:
                return 'Deny';
            default:
                break;
        }
    }

    /**
     * Return capture type
     *
     * @return string
     */
    protected function _getCaptureCompleteType()
    {
        return $this->getIsCaptureComplete() ? $this->_captureTypeComplete : $this->_captureTypeNotcomplete;
    }

    /**
     * Return each call request without unused fields in case of Express Checkout Unilateral payments
     *
     * @param string $methodName Current method name
     * @return array
     */
    protected function _prepareEachCallRequest($methodName)
    {
        $expressCheckoutMethods = array(
            self::SET_EXPRESS_CHECKOUT,
            self::GET_EXPRESS_CHECKOUT_DETAILS,
            self::DO_EXPRESS_CHECKOUT_PAYMENT
        );
        if (!in_array($methodName, $expressCheckoutMethods) || !$this->_config->shouldUseUnilateralPayments()) {
            return $this->_eachCallRequest;
        }
        return array_diff($this->_eachCallRequest, array('USER', 'PWD', 'SIGNATURE'));
    }

    /**
     * Check the EC request against unilateral payments mode and remove the SUBJECT if needed
     *
     * @param &array $requestFields
     * @return void
     */
    protected function _prepareExpressCheckoutCallRequest(&$requestFields)
    {
        if (!$this->_config->shouldUseUnilateralPayments()) {
            $key = array_search('SUBJECT', $requestFields);
            if ($key) {
                unset($requestFields[$key]);
            }
        }
    }
}
