<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1\Data;

use Magento\Framework\Service\Data\AbstractExtensibleObject as DataObject;

/**
 * Class OrderPayment
 */
class OrderPayment extends DataObject
{
    /**
     * int
     */
    const ENTITY_ID = 'entity_id';

    /**
     * int
     */
    const PARENT_ID = 'parent_id';

    /**
     * float
     */
    const BASE_SHIPPING_CAPTURED = 'base_shipping_captured';

    /**
     * float
     */
    const SHIPPING_CAPTURED = 'shipping_captured';

    /**
     * float
     */
    const AMOUNT_REFUNDED = 'amount_refunded';

    /**
     * float
     */
    const BASE_AMOUNT_PAID = 'base_amount_paid';

    /**
     * float
     */
    const AMOUNT_CANCELED = 'amount_canceled';

    /**
     * float
     */
    const BASE_AMOUNT_AUTHORIZED = 'base_amount_authorized';

    /**
     * float
     */
    const BASE_AMOUNT_PAID_ONLINE = 'base_amount_paid_online';

    /**
     * float
     */
    const BASE_AMOUNT_REFUNDED_ONLINE = 'base_amount_refunded_online';

    /**
     * float
     */
    const BASE_SHIPPING_AMOUNT = 'base_shipping_amount';

    /**
     * float
     */
    const SHIPPING_AMOUNT = 'shipping_amount';

    /**
     * float
     */
    const AMOUNT_PAID = 'amount_paid';

    /**
     * float
     */
    const AMOUNT_AUTHORIZED = 'amount_authorized';

    /**
     * float
     */
    const BASE_AMOUNT_ORDERED = 'base_amount_ordered';

    /**
     * float
     */
    const BASE_SHIPPING_REFUNDED = 'base_shipping_refunded';

    /**
     * float
     */
    const SHIPPING_REFUNDED = 'shipping_refunded';

    /**
     * float
     */
    const BASE_AMOUNT_REFUNDED = 'base_amount_refunded';

    /**
     * float
     */
    const AMOUNT_ORDERED = 'amount_ordered';

    /**
     * float
     */
    const BASE_AMOUNT_CANCELED = 'base_amount_canceled';

    /**
     * int
     */
    const QUOTE_PAYMENT_ID = 'quote_payment_id';

    /**
     * string
     */
    const ADDITIONAL_DATA = 'additional_data';

    /**
     * string
     */
    const CC_EXP_MONTH = 'cc_exp_month';

    /**
     * string
     */
    const CC_SS_START_YEAR = 'cc_ss_start_year';

    /**
     * string
     */
    const ECHECK_BANK_NAME = 'echeck_bank_name';

    /**
     * string
     */
    const METHOD = 'method';

    /**
     * string
     */
    const CC_DEBUG_REQUEST_BODY = 'cc_debug_request_body';

    /**
     * string
     */
    const CC_SECURE_VERIFY = 'cc_secure_verify';

    /**
     * string
     */
    const PROTECTION_ELIGIBILITY = 'protection_eligibility';

    /**
     * string
     */
    const CC_APPROVAL = 'cc_approval';

    /**
     * string
     */
    const CC_LAST4 = 'cc_last4';

    /**
     * string
     */
    const CC_STATUS_DESCRIPTION = 'cc_status_description';

    /**
     * string
     */
    const ECHECK_TYPE = 'echeck_type';

    /**
     * string
     */
    const CC_DEBUG_RESPONSE_SERIALIZED = 'cc_debug_response_serialized';

    /**
     * string
     */
    const CC_SS_START_MONTH = 'cc_ss_start_month';

    /**
     * string
     */
    const ECHECK_ACCOUNT_TYPE = 'echeck_account_type';

    /**
     * string
     */
    const LAST_TRANS_ID = 'last_trans_id';

    /**
     * string
     */
    const CC_CID_STATUS = 'cc_cid_status';

    /**
     * string
     */
    const CC_OWNER = 'cc_owner';

    /**
     * string
     */
    const CC_TYPE = 'cc_type';

    /**
     * string
     */
    const PO_NUMBER = 'po_number';

    /**
     * string
     */
    const CC_EXP_YEAR = 'cc_exp_year';

    /**
     * string
     */
    const CC_STATUS = 'cc_status';

    /**
     * string
     */
    const ECHECK_ROUTING_NUMBER = 'echeck_routing_number';

    /**
     * string
     */
    const ACCOUNT_STATUS = 'account_status';

    /**
     * string
     */
    const ANET_TRANS_METHOD = 'anet_trans_method';

    /**
     * string
     */
    const CC_DEBUG_RESPONSE_BODY = 'cc_debug_response_body';

    /**
     * string
     */
    const CC_SS_ISSUE = 'cc_ss_issue';

    /**
     * string
     */
    const ECHECK_ACCOUNT_NAME = 'echeck_account_name';

    /**
     * string
     */
    const CC_AVS_STATUS = 'cc_avs_status';

    /**
     * string
     */
    const CC_NUMBER_ENC = 'cc_number_enc';

    /**
     * string
     */
    const CC_TRANS_ID = 'cc_trans_id';

    /**
     * string
     */
    const ADDRESS_STATUS = 'address_status';

    /**
     * string
     */
    const ADDITIONAL_INFORMATION = 'additional_information';

    /**
     * Returns account_status
     *
     * @return string
     */
    public function getAccountStatus()
    {
        return $this->_get(self::ACCOUNT_STATUS);
    }

    /**
     * Returns additional_data
     *
     * @return string
     */
    public function getAdditionalData()
    {
        return $this->_get(self::ADDITIONAL_DATA);
    }

    /**
     * Returns additional_information
     *
     * @return string
     */
    public function getAdditionalInformation()
    {
        return $this->_get(self::ADDITIONAL_INFORMATION);
    }

    /**
     * Returns address_status
     *
     * @return string
     */
    public function getAddressStatus()
    {
        return $this->_get(self::ADDRESS_STATUS);
    }

    /**
     * Returns amount_authorized
     *
     * @return float
     */
    public function getAmountAuthorized()
    {
        return $this->_get(self::AMOUNT_AUTHORIZED);
    }

    /**
     * Returns amount_canceled
     *
     * @return float
     */
    public function getAmountCanceled()
    {
        return $this->_get(self::AMOUNT_CANCELED);
    }

    /**
     * Returns amount_ordered
     *
     * @return float
     */
    public function getAmountOrdered()
    {
        return $this->_get(self::AMOUNT_ORDERED);
    }

    /**
     * Returns amount_paid
     *
     * @return float
     */
    public function getAmountPaid()
    {
        return $this->_get(self::AMOUNT_PAID);
    }

    /**
     * Returns amount_refunded
     *
     * @return float
     */
    public function getAmountRefunded()
    {
        return $this->_get(self::AMOUNT_REFUNDED);
    }

    /**
     * Returns anet_trans_method
     *
     * @return string
     */
    public function getAnetTransMethod()
    {
        return $this->_get(self::ANET_TRANS_METHOD);
    }

    /**
     * Returns base_amount_authorized
     *
     * @return float
     */
    public function getBaseAmountAuthorized()
    {
        return $this->_get(self::BASE_AMOUNT_AUTHORIZED);
    }

    /**
     * Returns base_amount_canceled
     *
     * @return float
     */
    public function getBaseAmountCanceled()
    {
        return $this->_get(self::BASE_AMOUNT_CANCELED);
    }

    /**
     * Returns base_amount_ordered
     *
     * @return float
     */
    public function getBaseAmountOrdered()
    {
        return $this->_get(self::BASE_AMOUNT_ORDERED);
    }

    /**
     * Returns base_amount_paid
     *
     * @return float
     */
    public function getBaseAmountPaid()
    {
        return $this->_get(self::BASE_AMOUNT_PAID);
    }

    /**
     * Returns base_amount_paid_online
     *
     * @return float
     */
    public function getBaseAmountPaidOnline()
    {
        return $this->_get(self::BASE_AMOUNT_PAID_ONLINE);
    }

    /**
     * Returns base_amount_refunded
     *
     * @return float
     */
    public function getBaseAmountRefunded()
    {
        return $this->_get(self::BASE_AMOUNT_REFUNDED);
    }

    /**
     * Returns base_amount_refunded_online
     *
     * @return float
     */
    public function getBaseAmountRefundedOnline()
    {
        return $this->_get(self::BASE_AMOUNT_REFUNDED_ONLINE);
    }

    /**
     * Returns base_shipping_amount
     *
     * @return float
     */
    public function getBaseShippingAmount()
    {
        return $this->_get(self::BASE_SHIPPING_AMOUNT);
    }

    /**
     * Returns base_shipping_captured
     *
     * @return float
     */
    public function getBaseShippingCaptured()
    {
        return $this->_get(self::BASE_SHIPPING_CAPTURED);
    }

    /**
     * Returns base_shipping_refunded
     *
     * @return float
     */
    public function getBaseShippingRefunded()
    {
        return $this->_get(self::BASE_SHIPPING_REFUNDED);
    }

    /**
     * Returns cc_approval
     *
     * @return string
     */
    public function getCcApproval()
    {
        return $this->_get(self::CC_APPROVAL);
    }

    /**
     * Returns cc_avs_status
     *
     * @return string
     */
    public function getCcAvsStatus()
    {
        return $this->_get(self::CC_AVS_STATUS);
    }

    /**
     * Returns cc_cid_status
     *
     * @return string
     */
    public function getCcCidStatus()
    {
        return $this->_get(self::CC_CID_STATUS);
    }

    /**
     * Returns cc_debug_request_body
     *
     * @return string
     */
    public function getCcDebugRequestBody()
    {
        return $this->_get(self::CC_DEBUG_REQUEST_BODY);
    }

    /**
     * Returns cc_debug_response_body
     *
     * @return string
     */
    public function getCcDebugResponseBody()
    {
        return $this->_get(self::CC_DEBUG_RESPONSE_BODY);
    }

    /**
     * Returns cc_debug_response_serialized
     *
     * @return string
     */
    public function getCcDebugResponseSerialized()
    {
        return $this->_get(self::CC_DEBUG_RESPONSE_SERIALIZED);
    }

    /**
     * Returns cc_exp_month
     *
     * @return string
     */
    public function getCcExpMonth()
    {
        return $this->_get(self::CC_EXP_MONTH);
    }

    /**
     * Returns cc_exp_year
     *
     * @return string
     */
    public function getCcExpYear()
    {
        return $this->_get(self::CC_EXP_YEAR);
    }

    /**
     * Returns cc_last4
     *
     * @return string
     */
    public function getCcLast4()
    {
        return $this->_get(self::CC_LAST4);
    }

    /**
     * Returns cc_number_enc
     *
     * @return string
     */
    public function getCcNumberEnc()
    {
        return $this->_get(self::CC_NUMBER_ENC);
    }

    /**
     * Returns cc_owner
     *
     * @return string
     */
    public function getCcOwner()
    {
        return $this->_get(self::CC_OWNER);
    }

    /**
     * Returns cc_secure_verify
     *
     * @return string
     */
    public function getCcSecureVerify()
    {
        return $this->_get(self::CC_SECURE_VERIFY);
    }

    /**
     * Returns cc_ss_issue
     *
     * @return string
     */
    public function getCcSsIssue()
    {
        return $this->_get(self::CC_SS_ISSUE);
    }

    /**
     * Returns cc_ss_start_month
     *
     * @return string
     */
    public function getCcSsStartMonth()
    {
        return $this->_get(self::CC_SS_START_MONTH);
    }

    /**
     * Returns cc_ss_start_year
     *
     * @return string
     */
    public function getCcSsStartYear()
    {
        return $this->_get(self::CC_SS_START_YEAR);
    }

    /**
     * Returns cc_status
     *
     * @return string
     */
    public function getCcStatus()
    {
        return $this->_get(self::CC_STATUS);
    }

    /**
     * Returns cc_status_description
     *
     * @return string
     */
    public function getCcStatusDescription()
    {
        return $this->_get(self::CC_STATUS_DESCRIPTION);
    }

    /**
     * Returns cc_trans_id
     *
     * @return string
     */
    public function getCcTransId()
    {
        return $this->_get(self::CC_TRANS_ID);
    }

    /**
     * Returns cc_type
     *
     * @return string
     */
    public function getCcType()
    {
        return $this->_get(self::CC_TYPE);
    }

    /**
     * Returns echeck_account_name
     *
     * @return string
     */
    public function getEcheckAccountName()
    {
        return $this->_get(self::ECHECK_ACCOUNT_NAME);
    }

    /**
     * Returns echeck_account_type
     *
     * @return string
     */
    public function getEcheckAccountType()
    {
        return $this->_get(self::ECHECK_ACCOUNT_TYPE);
    }

    /**
     * Returns echeck_bank_name
     *
     * @return string
     */
    public function getEcheckBankName()
    {
        return $this->_get(self::ECHECK_BANK_NAME);
    }

    /**
     * Returns echeck_routing_number
     *
     * @return string
     */
    public function getEcheckRoutingNumber()
    {
        return $this->_get(self::ECHECK_ROUTING_NUMBER);
    }

    /**
     * Returns echeck_type
     *
     * @return string
     */
    public function getEcheckType()
    {
        return $this->_get(self::ECHECK_TYPE);
    }

    /**
     * Returns entity_id
     *
     * @return int
     */
    public function getEntityId()
    {
        return $this->_get(self::ENTITY_ID);
    }

    /**
     * Returns last_trans_id
     *
     * @return string
     */
    public function getLastTransId()
    {
        return $this->_get(self::LAST_TRANS_ID);
    }

    /**
     * Returns method
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->_get(self::METHOD);
    }

    /**
     * Returns parent_id
     *
     * @return int
     */
    public function getParentId()
    {
        return $this->_get(self::PARENT_ID);
    }

    /**
     * Returns po_number
     *
     * @return string
     */
    public function getPoNumber()
    {
        return $this->_get(self::PO_NUMBER);
    }

    /**
     * Returns protection_eligibility
     *
     * @return string
     */
    public function getProtectionEligibility()
    {
        return $this->_get(self::PROTECTION_ELIGIBILITY);
    }

    /**
     * Returns quote_payment_id
     *
     * @return int
     */
    public function getQuotePaymentId()
    {
        return $this->_get(self::QUOTE_PAYMENT_ID);
    }

    /**
     * Returns shipping_amount
     *
     * @return float
     */
    public function getShippingAmount()
    {
        return $this->_get(self::SHIPPING_AMOUNT);
    }

    /**
     * Returns shipping_captured
     *
     * @return float
     */
    public function getShippingCaptured()
    {
        return $this->_get(self::SHIPPING_CAPTURED);
    }

    /**
     * Returns shipping_refunded
     *
     * @return float
     */
    public function getShippingRefunded()
    {
        return $this->_get(self::SHIPPING_REFUNDED);
    }
}
