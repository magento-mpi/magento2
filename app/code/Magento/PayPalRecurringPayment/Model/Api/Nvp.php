<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\PayPalRecurringPayment\Model\Api;

/**
 * Recurring payments implementation via PayPal Name-Value Pair API
 */
class Nvp extends \Magento\Paypal\Model\Api\Nvp
{
    /**
     * CreateRecurringPayment request map
     *
     * @var array
     */
    protected $_createRecurringPaymentRequest = array(
        'TOKEN', 'SUBSCRIBERNAME', 'PROFILESTARTDATE', 'PROFILEREFERENCE', 'DESC', 'MAXFAILEDPAYMENTS', 'AUTOBILLAMT',
        'BILLINGPERIOD', 'BILLINGFREQUENCY', 'TOTALBILLINGCYCLES', 'AMT', 'TRIALBILLINGPERIOD', 'TRIALBILLINGFREQUENCY',
        'TRIALTOTALBILLINGCYCLES', 'TRIALAMT', 'CURRENCYCODE', 'SHIPPINGAMT', 'TAXAMT', 'INITAMT', 'FAILEDINITAMTACTION'
    );

    /**
     * CreateRecurringPayment response map
     *
     * @var array
     */
    protected $_createRecurringPaymentResponse = array(
        'PROFILEID', 'PROFILESTATUS'
    );

    /**
     * Request/response for ManageRecurringPaymentStatus map
     *
     * @var array
     */
    protected $_manageRecurringPaymentStatusRequest = array('PROFILEID', 'ACTION');

    /**
     * Request for GetRecurringPaymentDetails
     *
     * @var array
     */
    protected $_getRecurringPaymentDetailsRequest = array('PROFILEID');

    /**
     * Response for GetRecurringPaymentDetails
     *
     * @var array
     */
    protected $_getRecurringPaymentDetailsResponse = array('STATUS', /* TODO: lot of other stuff */);

    /**
     * @var \Magento\RecurringPayment\Model\QuoteImporter
     */
    protected $_quoteImporter;

    /**
     * @param \Magento\Customer\Helper\Address $customerAddress
     * @param \Magento\Logger $logger
     * @param \Magento\Locale\ResolverInterface $localeResolver
     * @param \Magento\Directory\Model\RegionFactory $regionFactory
     * @param \Magento\Logger\AdapterFactory $logAdapterFactory
     * @param \Magento\Directory\Model\CountryFactory $countryFactory
     * @param \Magento\RecurringPayment\Model\QuoteImporter $quoteImporter
     * @param array $data
     */
    public function __construct(
        \Magento\Customer\Helper\Address $customerAddress,
        \Magento\Logger $logger,
        \Magento\Locale\ResolverInterface $localeResolver,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \Magento\Logger\AdapterFactory $logAdapterFactory,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\RecurringPayment\Model\QuoteImporter $quoteImporter,
        array $data = array()
    ) {
        parent::__construct(
            $customerAddress,
            $logger,
            $localeResolver,
            $regionFactory,
            $logAdapterFactory,
            $countryFactory,
            $data
        );
        $this->_quoteImporter = $quoteImporter;
    }

    /**
     * SetExpressCheckout call
     * @link https://cms.paypal.com/us/cgi-bin/?&cmd=_render-content&content_ID=developer/e_howto_api_nvp_r_SetExpressCheckout
     * TODO: put together style and giropay settings
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
        } elseif ($options && (count($options) <= 10)) { // doesn't support more than 10 shipping options
            $request['CALLBACK'] = $this->getShippingOptionsCallbackUrl();
            $request['CALLBACKTIMEOUT'] = 6; // max value
            $request['MAXAMT'] = $request['AMT'] + 999.00; // it is impossible to calculate max amount
            $this->_exportShippingOptions($request);
        }

        $payments = $this->_quoteImporter->import($this->getQuote());
        if ($payments) {
            $i = 0;
            foreach ($payments as $payment) {
                $payment->setMethodCode(\Magento\Paypal\Model\Config::METHOD_WPP_EXPRESS);
                if (!$payment->isValid()) {
                    throw new \Magento\Core\Exception($payment->getValidationErrors());
                }
                $request["L_BILLINGTYPE{$i}"] = 'RecurringPayments';
                $request["L_BILLINGAGREEMENTDESCRIPTION{$i}"] = $payment->getScheduleDescription();
                $i++;
            }
        }

        $response = $this->call(self::SET_EXPRESS_CHECKOUT, $request);
        $this->_importFromResponse($this->_setExpressCheckoutResponse, $response);
    }

    /**
     * GetExpressCheckoutDetails call
     * @link https://cms.paypal.com/us/cgi-bin/?&cmd=_render-content&content_ID=developer/e_howto_api_nvp_r_GetExpressCheckoutDetails
     */
    function callGetExpressCheckoutDetails()
    {
        $this->_prepareExpressCheckoutCallRequest($this->_getExpressCheckoutDetailsRequest);
        $request = $this->_exportToRequest($this->_getExpressCheckoutDetailsRequest);
        $response = $this->call(self::GET_EXPRESS_CHECKOUT_DETAILS, $request);
        $this->_importFromResponse($this->_paymentInformationResponse, $response);
        $this->_exportAddressses($response);
    }

    /**
     * DoExpressCheckout call
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
     * CreateRecurringPayment call
     */
    public function callCreateRecurringPayment()
    {
        $request = $this->_exportToRequest($this->_createRecurringPaymentRequest);
        $response = $this->call('CreateRecurringPaymentsProfile', $request);
        $this->_importFromResponse($this->_createRecurringPaymentResponse, $response);
        $this->_analyzeRecurringPaymentStatus($this->getRecurringPaymentStatus(), $this);
    }

    /**
     * ManageRecurringPaymentStatus call
     *
     * @throws \Magento\Core\Exception
     */
    public function callManageRecurringPaymentStatus()
    {
        $request = $this->_exportToRequest($this->_manageRecurringPaymentStatusRequest);
        if (isset($request['ACTION'])) {
            $request['ACTION'] = $this->_filterRecurringPaymentActionToNvp($request['ACTION']);
        }
        try {
            $this->call('ManageRecurringPaymentsProfileStatus', $request);
        } catch (\Magento\Core\Exception $e) {
            if ((in_array(11556, $this->_callErrors) && 'Cancel' === $request['ACTION'])
                || (in_array(11557, $this->_callErrors) && 'Suspend' === $request['ACTION'])
                || (in_array(11558, $this->_callErrors) && 'Reactivate' === $request['ACTION'])
            ) {
                throw new \Magento\Core\Exception(
                    __('We can\'t change the status because the current status doesn\'t match the real status.')
                );
            }
            throw $e;
        }
    }

    /**
     * GetRecurringPaymentDetails call
     */
    public function callGetRecurringPaymentDetails(\Magento\Object $result)
    {
        $request = $this->_exportToRequest($this->_getRecurringPaymentDetailsRequest);
        $response = $this->call('GetRecurringPaymentsProfileDetails', $request);
        $this->_importFromResponse($this->_getRecurringPaymentDetailsResponse, $response);
        $this->_analyzeRecurringPaymentStatus($this->getStatus(), $result);
    }

    /**
     * Convert RP management action to NVP format
     *
     * @param string $value
     * @return string|null
     */
    protected function _filterRecurringPaymentActionToNvp($value)
    {
        switch ($value) {
            case 'cancel':
                return 'Cancel';
            case 'suspend':
                return 'Suspend';
            case 'activate':
                return 'Reactivate';
            default:
                break;
        }
    }

    /**
     * Check the obtained RP status in NVP format and specify the payment state
     *
     * @param string $value
     * @param \Magento\Object $result
     */
    protected function _analyzeRecurringPaymentStatus($value, \Magento\Object $result)
    {
        switch ($value) {
            case 'ActiveProfile':
            case 'Active':
                $result->setIsProfileActive(true);
                break;
            case 'PendingProfile':
                $result->setIsProfilePending(true);
                break;
            case 'CancelledProfile':
            case 'Cancelled':
                $result->setIsProfileCanceled(true);
                break;
            case 'SuspendedProfile':
            case 'Suspended':
                $result->setIsProfileSuspended(true);
                break;
            case 'ExpiredProfile':
            case 'Expired': // ??
                $result->setIsProfileExpired(true);
                break;
            default:
                break;
        }
    }
}
