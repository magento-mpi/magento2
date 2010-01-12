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
 * @package     Mage_PaypalUk
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * NVP API wrappers model
 */
class Mage_PaypalUk_Model_Api_Nvp extends Mage_Paypal_Model_Api_Nvp
{
    /**
     * Transaction types declaration
     * @var unknown_type
     */
    const TRXTYPE_AUTH_ONLY         = 'A';
    const TRXTYPE_SALE              = 'S';
    const TRXTYPE_CREDIT            = 'C';
    const TRXTYPE_DELAYED_CAPTURE   = 'D';
    const TRXTYPE_DELAYED_VOID      = 'V';

    /**
     * Tender definition
     *
     * @var unknown_type
     */
    const TENDER_CC                 = 'C';

    /**
     * Response codes definition
     *
     * @var unknown_type
     */
    const RESPONSE_CODE_APPROVED = 0;

    /**
     * Capture types (make authorization close or remain open)
     * @var string
     */
    protected $_captureTypeComplete = 'Y';
    protected $_captureTypeNotcomplete = 'N';

    /**
     * Global public interface map
     * @var array
     */
    protected $_globalMap = array(
        // each call
        'PARTNER'       => 'partner',
        'VENDOR'        => 'vendor',
        'USER'          => 'user',
        'PWD'           => 'password',
        'BUTTONSOURCE'  => 'build_notation_code',
        'TENDER'        => 'tender',
        // commands
        'RETURNURL'     => 'return_url',
        'CANCELURL'     => 'cancel_url',
        'INVNUM'        => 'inv_num',
        'TOKEN'         => 'token',
        'CORRELATIONID' => 'correlation_id',
        'SOLUTIONTYPE'  => 'solution_type',
        'GIROPAYCANCELURL'  => 'giropay_cancel_url',
        'GIROPAYSUCCESSURL' => 'giropay_success_url',
        'BANKTXNPENDINGURL' => 'giropay_bank_txn_pending_url',
        'CUSTIP'         => 'ip_address',
        'NOTIFYURL'         => 'notify_url',
        'RETURNFMFDETAILS'  => 'fraud_management_filters_enabled',
        'NOTE'              => 'note',
        'COMMENT1'          => 'note',
        'REFUNDTYPE'        => 'refund_type',
        'ACTION'            => 'action',
        // style settings
        'PAGESTYLE'      => 'page_style',
        'HDRIMG'         => 'hdrimg',
        'HDRBORDERCOLOR' => 'hdrbordercolor',
        'HDRBACKCOLOR'   => 'hdrbackcolor',
        'PAYFLOWCOLOR'   => 'payflowcolor',
        'LOCALECODE'     => 'locale_code',
        'PAL'            => 'pal',

        // transaction info
        'PNREF'   => 'transaction_id',
        'ORIGID'    => 'authorization_id',
        'AUTHORIZATIONID' => 'authorization_id',
        'REFUNDTRANSACTIONID' => 'refund_transaction_id',
        'CAPTURECOMPLETE'    => 'complete_type',
        'AMT' => 'amount',
        'PPREF' => 'payment_id', // possible mistake, check with API reference
        'AVSADDR'       => 'address_verification',
        'AVSZIP'        =>  'postcode_verification',

        // payment/billing info
        'CURRENCY'  => 'currency_code',
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
        // paypal direct credit card information
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
     * Fields that should be replaced in debug with '***'
     *
     * @var array
     */
    protected $_debugReplacePrivateDataKeys = array(
        'ACCT', 'EXPDATE', 'CVV2', 'CARDISSUE', 'CARDSTART',
        'PARTNER', 'USER', 'VENDOR', 'PWD',
    );

    /**
     * DoDirectPayment request/response map
     * @var array
     */
    protected $_doDirectPaymentRequest = array(
        'ACCT', 'EXPDATE', 'CVV2', 'CURRENCY', 'EMAIL', 'TENDER',
        'AMT', 'CUSTIP', 'INVNUM'
    );
    protected $_doDirectPaymentResponse = array(
        'PNREF', 'CORRELATIONID', 'CVV2MATCH'
        //'AMT', 'AVSADDR', 'AVSZIP', 'PPREF'
    );

    /**
     * DoCapture request/response map
     * @var array
     */
    protected $_doCaptureRequest = array('ORIGID', 'CAPTURECOMPLETE', 'AMT', 'TENDER', 'NOTE', 'INVNUM');
    protected $_doCaptureResponse = array('PNREF', 'PPREF');

    /**
     * DoVoid request map
     * @var array
     */
    protected $_doVoidRequest = array('ORIGID', 'NOTE', 'TENDER');

    /**
     * Request map for each API call
     * @var array
     */
    protected $_eachCallRequest = array('PARTNER', 'USER', 'VENDOR', 'PWD', 'BUTTONSOURCE');

    /**
     * RefundTransaction request/response map
     * @var array
     */
    protected $_refundTransactionRequest = array('ORIGID', 'TENDER');
    protected $_refundTransactionResponse = array('PNREF');

    /**
     * Map for shipping address import/export (extends billing address mapper)
     *
     * @var array
     */
    protected $_shippingAddressMap = array(
        'SHIPTOCOUNTRY' => 'country_id',
        'SHIPTOSTATE' => 'region',
        'SHIPTOCITY'    => 'city',
        'SHIPTOSTREET'  => 'street',
        'SHIPTOSTREET2' => 'street2',
        'SHIPTOZIP' => 'postcode',
        'SHIPTOPHONENUM' => 'telephone',
    );

    /**
     * Map for billing address import/export
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
        'STATE'    => 'region',
        'CITY'     => 'city',
        'STREET'   => 'street',
        'STREET2'  => 'street2',
        'ZIP'      => 'postcode',
        'PHONENUM' => 'telephone',
    );

    /**
     * API endpoint getter
     *
     * @return string
     */
    public function getApiEndpoint()
    {
        return sprintf('https://%spayflowpro.verisign.com/transaction', $this->_config->sandboxFlag ? 'pilot-' : '');
    }

    /**
     * Return PaypalUk partner based on config data
     *
     * @return string
     */
    public function getPartner()
    {
        return $this->_getDataOrConfig('partner');
    }

    /**
     * Return PaypalUk user based on config data
     *
     * @return string
     */
    public function getUser()
    {
        return $this->_getDataOrConfig('user');
    }

    /**
     * Return PaypalUk password based on config data
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->_getDataOrConfig('pwd');
    }

    /**
     * Return PaypalUk tender based on config data
     *
     * @return string
     */
    public function getTender()
    {
        return self::TENDER_CC;
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
        $request['TRXTYPE'] = $this->_mapPaypalMethodName($methodName);
        return $request;
    }

    /**
     * Map paypal method names
     *
     * @param string| $methodName
     * @return string
     */
    protected function _mapPaypalMethodName($methodName)
    {
        switch($methodName) {
            case Mage_Paypal_Model_Api_Nvp::DO_DIRECT_PAYMENT:
                return ($this->_config->payment_action == 'authorize') ?
                    self::TRXTYPE_AUTH_ONLY:
                    self::TRXTYPE_SALE;
            case Mage_Paypal_Model_Api_Nvp::DO_CAPTURE:
                return self::TRXTYPE_DELAYED_CAPTURE;
            case Mage_Paypal_Model_Api_Nvp::DO_VOID:
                return self::TRXTYPE_DELAYED_VOID;
            case Mage_Paypal_Model_Api_Nvp::REFUND_TRANSACTION:
                return self::TRXTYPE_CREDIT;
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
        $this->_callWarnings = array();
        if ($response['RESULT'] == self::RESPONSE_CODE_APPROVED) {
            // collect warnings
            if (!empty($response['RESPMSG']) && strtoupper($response['RESPMSG']) != 'APPROVED') {
                $this->_callWarnings[] = $response['RESPMSG'];
            }
            return true;
        }
        return false;
    }

    /**
     * Handle logical errors
     *
     * @param array
     */
    protected function _handleCallErrors($response)
    {
        if ($response['RESULT'] != self::RESPONSE_CODE_APPROVED) {
            $message = $response['RESPMSG'];
            $e = new Exception(sprintf('PayPal gateway errors: %s.', $message));
            Mage::logException($e);
            Mage::throwException(Mage::helper('paypal')->__('PayPal geteway rejected request. %s', $message));
        }
    }

    /**
     * Line items export mapping settings
     * @var array
     */
    protected $_lineItemExportTotals = array();
    protected $_lineItemExportItemsFormat = array(
        'name'   => 'L_NAME%d',
        'qty'    => 'L_QTY%d',
        'amount' => 'L_COST%d',
    );
}
