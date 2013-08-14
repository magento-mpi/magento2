<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Mock object for order payment model
 *
 * @category   Saas
 * @package    Saas_PrintedTemplate
 * @subpackage Models
 */
class Saas_PrintedTemplate_Model_Converter_Preview_Mock_Order_Payment extends Magento_Sales_Model_Order_Payment
{
    /**
     * Initialize order payment with mock data
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setData($this->_getMockData());
    }

    /**
     * Returns data for the order payment
     *
     * @return array
     */
    protected function _getMockData()
    {
        return array (
            'parent_id' => '-1',
            'base_shipping_captured' => '30.0000',
            'shipping_captured' => '60.0000',
            'amount_refunded' => '2488.1300',
            'base_amount_paid' => '2069.0600',
            'amount_canceled' => NULL,
            'base_amount_authorized' => NULL,
            'base_amount_paid_online' => NULL,
            'base_amount_refunded_online' => NULL,
            'base_shipping_amount' => '30.0000',
            'shipping_amount' => '60.0000',
            'amount_paid' => '4138.1300',
            'amount_authorized' => NULL,
            'base_amount_ordered' => '2069.0600',
            'base_shipping_refunded' => '30.0000',
            'shipping_refunded' => '60.0000',
            'base_amount_refunded' => '1244.0600',
            'amount_ordered' => '4138.1300',
            'base_amount_canceled' => NULL,
            'ideal_transaction_checked' => NULL,
            'quote_payment_id' => NULL,
            'additional_data' => NULL,
            'cc_exp_month' => '0',
            'cc_ss_start_year' => '0',
            'echeck_bank_name' => NULL,
            'method' => 'purchaseorder',
            'cc_debug_request_body' => NULL,
            'cc_secure_verify' => NULL,
            'cybersource_token' => NULL,
            'ideal_issuer_title' => NULL,
            'protection_eligibility' => NULL,
            'cc_approval' => NULL,
            'cc_last4' => '',
            'cc_status_description' => NULL,
            'echeck_type' => NULL,
            'paybox_question_number' => NULL,
            'cc_debug_response_serialized' => NULL,
            'cc_ss_start_month' => '0',
            'echeck_account_type' => NULL,
            'last_trans_id' => NULL,
            'cc_cid_status' => NULL,
            'cc_owner' => '',
            'cc_type' => '',
            'ideal_issuer_id' => NULL,
            'po_number' => '1111111111111111111',
            'cc_exp_year' => '0',
            'cc_status' => NULL,
            'echeck_routing_number' => NULL,
            'account_status' => NULL,
            'anet_trans_method' => NULL,
            'cc_debug_response_body' => NULL,
            'cc_ss_issue' => NULL,
            'echeck_account_name' => NULL,
            'cc_avs_status' => NULL,
            'cc_number_enc' => '',
            'cc_trans_id' => NULL,
            'flo2cash_account_id' => NULL,
            'paybox_request_number' => NULL,
            'address_status' => NULL,
            'cc_raw_request' => NULL,
            'cc_raw_response' => NULL,
            'customer_payment_id' => NULL,
            'amount' => NULL,
            'additional_information' =>
            array (
            ),
        );
    }
}
