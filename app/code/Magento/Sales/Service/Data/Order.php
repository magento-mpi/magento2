<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\Data;

use Magento\Framework\Service\Data\AbstractObject as DataObject;

/**
 * Class Order
 */
class Order extends DataObject
{
    /**
     * int
     */
    const ENTITY_ID = 'entity_id';

    /**
     * string
     */
    const STATE = 'state';

    /**
     * string
     */
    const STATUS = 'status';

    /**
     * string
     */
    const COUPON_CODE = 'coupon_code';

    /**
     * string
     */
    const PROTECT_CODE = 'protect_code';

    /**
     * string
     */
    const SHIPPING_DESCRIPTION = 'shipping_description';

    /**
     * int
     */
    const IS_VIRTUAL = 'is_virtual';

    /**
     * int
     */
    const STORE_ID = 'store_id';

    /**
     * int
     */
    const CUSTOMER_ID = 'customer_id';

    /**
     * float
     */
    const BASE_DISCOUNT_AMOUNT = 'base_discount_amount';

    /**
     * float
     */
    const BASE_DISCOUNT_CANCELED = 'base_discount_canceled';

    /**
     * float
     */
    const BASE_DISCOUNT_INVOICED = 'base_discount_invoiced';

    /**
     * float
     */
    const BASE_DISCOUNT_REFUNDED = 'base_discount_refunded';

    /**
     * float
     */
    const BASE_GRAND_TOTAL = 'base_grand_total';

    /**
     * float
     */
    const BASE_SHIPPING_AMOUNT = 'base_shipping_amount';

    /**
     * float
     */
    const BASE_SHIPPING_CANCELED = 'base_shipping_canceled';

    /**
     * float
     */
    const BASE_SHIPPING_INVOICED = 'base_shipping_invoiced';

    /**
     * float
     */
    const BASE_SHIPPING_REFUNDED = 'base_shipping_refunded';

    /**
     * float
     */
    const BASE_SHIPPING_TAX_AMOUNT = 'base_shipping_tax_amount';

    /**
     * float
     */
    const BASE_SHIPPING_TAX_REFUNDED = 'base_shipping_tax_refunded';

    /**
     * float
     */
    const BASE_SUBTOTAL = 'base_subtotal';

    /**
     * float
     */
    const BASE_SUBTOTAL_CANCELED = 'base_subtotal_canceled';

    /**
     * float
     */
    const BASE_SUBTOTAL_INVOICED = 'base_subtotal_invoiced';

    /**
     * float
     */
    const BASE_SUBTOTAL_REFUNDED = 'base_subtotal_refunded';

    /**
     * float
     */
    const BASE_TAX_AMOUNT = 'base_tax_amount';

    /**
     * float
     */
    const BASE_TAX_CANCELED = 'base_tax_canceled';

    /**
     * float
     */
    const BASE_TAX_INVOICED = 'base_tax_invoiced';

    /**
     * float
     */
    const BASE_TAX_REFUNDED = 'base_tax_refunded';

    /**
     * float
     */
    const BASE_TO_GLOBAL_RATE = 'base_to_global_rate';

    /**
     * float
     */
    const BASE_TO_ORDER_RATE = 'base_to_order_rate';

    /**
     * float
     */
    const BASE_TOTAL_CANCELED = 'base_total_canceled';

    /**
     * float
     */
    const BASE_TOTAL_INVOICED = 'base_total_invoiced';

    /**
     * float
     */
    const BASE_TOTAL_INVOICED_COST = 'base_total_invoiced_cost';

    /**
     * float
     */
    const BASE_TOTAL_OFFLINE_REFUNDED = 'base_total_offline_refunded';

    /**
     * float
     */
    const BASE_TOTAL_ONLINE_REFUNDED = 'base_total_online_refunded';

    /**
     * float
     */
    const BASE_TOTAL_PAID = 'base_total_paid';

    /**
     * float
     */
    const BASE_TOTAL_QTY_ORDERED = 'base_total_qty_ordered';

    /**
     * float
     */
    const BASE_TOTAL_REFUNDED = 'base_total_refunded';

    /**
     * float
     */
    const DISCOUNT_AMOUNT = 'discount_amount';

    /**
     * float
     */
    const DISCOUNT_CANCELED = 'discount_canceled';

    /**
     * float
     */
    const DISCOUNT_INVOICED = 'discount_invoiced';

    /**
     * float
     */
    const DISCOUNT_REFUNDED = 'discount_refunded';

    /**
     * float
     */
    const GRAND_TOTAL = 'grand_total';

    /**
     * float
     */
    const SHIPPING_AMOUNT = 'shipping_amount';

    /**
     * float
     */
    const SHIPPING_CANCELED = 'shipping_canceled';

    /**
     * float
     */
    const SHIPPING_INVOICED = 'shipping_invoiced';

    /**
     * float
     */
    const SHIPPING_REFUNDED = 'shipping_refunded';

    /**
     * float
     */
    const SHIPPING_TAX_AMOUNT = 'shipping_tax_amount';

    /**
     * float
     */
    const SHIPPING_TAX_REFUNDED = 'shipping_tax_refunded';

    /**
     * float
     */
    const STORE_TO_BASE_RATE = 'store_to_base_rate';

    /**
     * float
     */
    const STORE_TO_ORDER_RATE = 'store_to_order_rate';

    /**
     * float
     */
    const SUBTOTAL = 'subtotal';

    /**
     * float
     */
    const SUBTOTAL_CANCELED = 'subtotal_canceled';

    /**
     * float
     */
    const SUBTOTAL_INVOICED = 'subtotal_invoiced';

    /**
     * float
     */
    const SUBTOTAL_REFUNDED = 'subtotal_refunded';

    /**
     * float
     */
    const TAX_AMOUNT = 'tax_amount';

    /**
     * float
     */
    const TAX_CANCELED = 'tax_canceled';

    /**
     * float
     */
    const TAX_INVOICED = 'tax_invoiced';

    /**
     * float
     */
    const TAX_REFUNDED = 'tax_refunded';

    /**
     * float
     */
    const TOTAL_CANCELED = 'total_canceled';

    /**
     * float
     */
    const TOTAL_INVOICED = 'total_invoiced';

    /**
     * float
     */
    const TOTAL_OFFLINE_REFUNDED = 'total_offline_refunded';

    /**
     * float
     */
    const TOTAL_ONLINE_REFUNDED = 'total_online_refunded';

    /**
     * float
     */
    const TOTAL_PAID = 'total_paid';

    /**
     * float
     */
    const TOTAL_QTY_ORDERED = 'total_qty_ordered';

    /**
     * float
     */
    const TOTAL_REFUNDED = 'total_refunded';

    /**
     * int
     */
    const CAN_SHIP_PARTIALLY = 'can_ship_partially';

    /**
     * int
     */
    const CAN_SHIP_PARTIALLY_ITEM = 'can_ship_partially_item';

    /**
     * int
     */
    const CUSTOMER_IS_GUEST = 'customer_is_guest';

    /**
     * int
     */
    const CUSTOMER_NOTE_NOTIFY = 'customer_note_notify';

    /**
     * int
     */
    const BILLING_ADDRESS_ID = 'billing_address_id';

    /**
     * int
     */
    const CUSTOMER_GROUP_ID = 'customer_group_id';

    /**
     * int
     */
    const EDIT_INCREMENT = 'edit_increment';

    /**
     * int
     */
    const EMAIL_SENT = 'email_sent';

    /**
     * int
     */
    const FORCED_SHIPMENT_WITH_INVOICE = 'forced_shipment_with_invoice';

    /**
     * int
     */
    const PAYMENT_AUTH_EXPIRATION = 'payment_auth_expiration';

    /**
     * int
     */
    const QUOTE_ADDRESS_ID = 'quote_address_id';

    /**
     * int
     */
    const QUOTE_ID = 'quote_id';

    /**
     * int
     */
    const SHIPPING_ADDRESS_ID = 'shipping_address_id';

    /**
     * float
     */
    const ADJUSTMENT_NEGATIVE = 'adjustment_negative';

    /**
     * float
     */
    const ADJUSTMENT_POSITIVE = 'adjustment_positive';

    /**
     * float
     */
    const BASE_ADJUSTMENT_NEGATIVE = 'base_adjustment_negative';

    /**
     * float
     */
    const BASE_ADJUSTMENT_POSITIVE = 'base_adjustment_positive';

    /**
     * float
     */
    const BASE_SHIPPING_DISCOUNT_AMOUNT = 'base_shipping_discount_amount';

    /**
     * float
     */
    const BASE_SUBTOTAL_INCL_TAX = 'base_subtotal_incl_tax';

    /**
     * float
     */
    const BASE_TOTAL_DUE = 'base_total_due';

    /**
     * float
     */
    const PAYMENT_AUTHORIZATION_AMOUNT = 'payment_authorization_amount';

    /**
     * float
     */
    const SHIPPING_DISCOUNT_AMOUNT = 'shipping_discount_amount';

    /**
     * float
     */
    const SUBTOTAL_INCL_TAX = 'subtotal_incl_tax';

    /**
     * float
     */
    const TOTAL_DUE = 'total_due';

    /**
     * float
     */
    const WEIGHT = 'weight';

    /**
     * datetime
     */
    const CUSTOMER_DOB = 'customer_dob';

    /**
     * string
     */
    const INCREMENT_ID = 'increment_id';

    /**
     * string
     */
    const APPLIED_RULE_IDS = 'applied_rule_ids';

    /**
     * string
     */
    const BASE_CURRENCY_CODE = 'base_currency_code';

    /**
     * string
     */
    const CUSTOMER_EMAIL = 'customer_email';

    /**
     * string
     */
    const CUSTOMER_FIRSTNAME = 'customer_firstname';

    /**
     * string
     */
    const CUSTOMER_LASTNAME = 'customer_lastname';

    /**
     * string
     */
    const CUSTOMER_MIDDLENAME = 'customer_middlename';

    /**
     * string
     */
    const CUSTOMER_PREFIX = 'customer_prefix';

    /**
     * string
     */
    const CUSTOMER_SUFFIX = 'customer_suffix';

    /**
     * string
     */
    const CUSTOMER_TAXVAT = 'customer_taxvat';

    /**
     * string
     */
    const DISCOUNT_DESCRIPTION = 'discount_description';

    /**
     * string
     */
    const EXT_CUSTOMER_ID = 'ext_customer_id';

    /**
     * string
     */
    const EXT_ORDER_ID = 'ext_order_id';

    /**
     * string
     */
    const GLOBAL_CURRENCY_CODE = 'global_currency_code';

    /**
     * string
     */
    const HOLD_BEFORE_STATE = 'hold_before_state';

    /**
     * string
     */
    const HOLD_BEFORE_STATUS = 'hold_before_status';

    /**
     * string
     */
    const ORDER_CURRENCY_CODE = 'order_currency_code';

    /**
     * string
     */
    const ORIGINAL_INCREMENT_ID = 'original_increment_id';

    /**
     * string
     */
    const RELATION_CHILD_ID = 'relation_child_id';

    /**
     * string
     */
    const RELATION_CHILD_REAL_ID = 'relation_child_real_id';

    /**
     * string
     */
    const RELATION_PARENT_ID = 'relation_parent_id';

    /**
     * string
     */
    const RELATION_PARENT_REAL_ID = 'relation_parent_real_id';

    /**
     * string
     */
    const REMOTE_IP = 'remote_ip';

    /**
     * string
     */
    const SHIPPING_METHOD = 'shipping_method';

    /**
     * string
     */
    const STORE_CURRENCY_CODE = 'store_currency_code';

    /**
     * string
     */
    const STORE_NAME = 'store_name';

    /**
     * string
     */
    const X_FORWARDED_FOR = 'x_forwarded_for';

    /**
     * string
     */
    const CUSTOMER_NOTE = 'customer_note';

    /**
     * string
     */
    const CREATED_AT = 'created_at';

    /**
     * string
     */
    const UPDATED_AT = 'updated_at';

    /**
     * int
     */
    const TOTAL_ITEM_COUNT = 'total_item_count';

    /**
     * int
     */
    const CUSTOMER_GENDER = 'customer_gender';

    /**
     * float
     */
    const HIDDEN_TAX_AMOUNT = 'hidden_tax_amount';

    /**
     * float
     */
    const BASE_HIDDEN_TAX_AMOUNT = 'base_hidden_tax_amount';

    /**
     * float
     */
    const SHIPPING_HIDDEN_TAX_AMOUNT = 'shipping_hidden_tax_amount';

    /**
     * float
     */
    const BASE_SHIPPING_HIDDEN_TAX_AMNT = 'base_shipping_hidden_tax_amnt';

    /**
     * float
     */
    const HIDDEN_TAX_INVOICED = 'hidden_tax_invoiced';

    /**
     * float
     */
    const BASE_HIDDEN_TAX_INVOICED = 'base_hidden_tax_invoiced';

    /**
     * float
     */
    const HIDDEN_TAX_REFUNDED = 'hidden_tax_refunded';

    /**
     * float
     */
    const BASE_HIDDEN_TAX_REFUNDED = 'base_hidden_tax_refunded';

    /**
     * float
     */
    const SHIPPING_INCL_TAX = 'shipping_incl_tax';

    /**
     * float
     */
    const BASE_SHIPPING_INCL_TAX = 'base_shipping_incl_tax';

    /**
     * string
     */
    const COUPON_RULE_NAME = 'coupon_rule_name';

    /**
     * float
     */
    const BASE_CUSTOMER_BALANCE_AMOUNT = 'base_customer_balance_amount';

    /**
     * float
     */
    const CUSTOMER_BALANCE_AMOUNT = 'customer_balance_amount';

    /**
     * float
     */
    const BASE_CUSTOMER_BALANCE_INVOICED = 'base_customer_balance_invoiced';

    /**
     * float
     */
    const CUSTOMER_BALANCE_INVOICED = 'customer_balance_invoiced';

    /**
     * float
     */
    const BASE_CUSTOMER_BALANCE_REFUNDED = 'base_customer_balance_refunded';

    /**
     * float
     */
    const CUSTOMER_BALANCE_REFUNDED = 'customer_balance_refunded';

    /**
     * float
     */
    const BS_CUSTOMER_BAL_TOTAL_REFUNDED = 'bs_customer_bal_total_refunded';

    /**
     * float
     */
    const CUSTOMER_BAL_TOTAL_REFUNDED = 'customer_bal_total_refunded';

    /**
     * string
     */
    const GIFT_CARDS = 'gift_cards';

    /**
     * float
     */
    const BASE_GIFT_CARDS_AMOUNT = 'base_gift_cards_amount';

    /**
     * float
     */
    const GIFT_CARDS_AMOUNT = 'gift_cards_amount';

    /**
     * float
     */
    const BASE_GIFT_CARDS_INVOICED = 'base_gift_cards_invoiced';

    /**
     * float
     */
    const GIFT_CARDS_INVOICED = 'gift_cards_invoiced';

    /**
     * float
     */
    const BASE_GIFT_CARDS_REFUNDED = 'base_gift_cards_refunded';

    /**
     * float
     */
    const GIFT_CARDS_REFUNDED = 'gift_cards_refunded';

    /**
     * int
     */
    const GIFT_MESSAGE_ID = 'gift_message_id';

    /**
     * int
     */
    const GW_ID = 'gw_id';

    /**
     * int
     */
    const GW_ALLOW_GIFT_RECEIPT = 'gw_allow_gift_receipt';

    /**
     * int
     */
    const GW_ADD_CARD = 'gw_add_card';

    /**
     * float
     */
    const GW_BASE_PRICE = 'gw_base_price';

    /**
     * float
     */
    const GW_PRICE = 'gw_price';

    /**
     * float
     */
    const GW_ITEMS_BASE_PRICE = 'gw_items_base_price';

    /**
     * float
     */
    const GW_ITEMS_PRICE = 'gw_items_price';

    /**
     * float
     */
    const GW_CARD_BASE_PRICE = 'gw_card_base_price';

    /**
     * float
     */
    const GW_CARD_PRICE = 'gw_card_price';

    /**
     * float
     */
    const GW_BASE_TAX_AMOUNT = 'gw_base_tax_amount';

    /**
     * float
     */
    const GW_TAX_AMOUNT = 'gw_tax_amount';

    /**
     * float
     */
    const GW_ITEMS_BASE_TAX_AMOUNT = 'gw_items_base_tax_amount';

    /**
     * float
     */
    const GW_ITEMS_TAX_AMOUNT = 'gw_items_tax_amount';

    /**
     * float
     */
    const GW_CARD_BASE_TAX_AMOUNT = 'gw_card_base_tax_amount';

    /**
     * float
     */
    const GW_CARD_TAX_AMOUNT = 'gw_card_tax_amount';

    /**
     * float
     */
    const GW_BASE_PRICE_INVOICED = 'gw_base_price_invoiced';

    /**
     * float
     */
    const GW_PRICE_INVOICED = 'gw_price_invoiced';

    /**
     * float
     */
    const GW_ITEMS_BASE_PRICE_INVOICED = 'gw_items_base_price_invoiced';

    /**
     * float
     */
    const GW_ITEMS_PRICE_INVOICED = 'gw_items_price_invoiced';

    /**
     * float
     */
    const GW_CARD_BASE_PRICE_INVOICED = 'gw_card_base_price_invoiced';

    /**
     * float
     */
    const GW_CARD_PRICE_INVOICED = 'gw_card_price_invoiced';

    /**
     * float
     */
    const GW_BASE_TAX_AMOUNT_INVOICED = 'gw_base_tax_amount_invoiced';

    /**
     * float
     */
    const GW_TAX_AMOUNT_INVOICED = 'gw_tax_amount_invoiced';

    /**
     * float
     */
    const GW_ITEMS_BASE_TAX_INVOICED = 'gw_items_base_tax_invoiced';

    /**
     * float
     */
    const GW_ITEMS_TAX_INVOICED = 'gw_items_tax_invoiced';

    /**
     * float
     */
    const GW_CARD_BASE_TAX_INVOICED = 'gw_card_base_tax_invoiced';

    /**
     * float
     */
    const GW_CARD_TAX_INVOICED = 'gw_card_tax_invoiced';

    /**
     * float
     */
    const GW_BASE_PRICE_REFUNDED = 'gw_base_price_refunded';

    /**
     * float
     */
    const GW_PRICE_REFUNDED = 'gw_price_refunded';

    /**
     * float
     */
    const GW_ITEMS_BASE_PRICE_REFUNDED = 'gw_items_base_price_refunded';

    /**
     * float
     */
    const GW_ITEMS_PRICE_REFUNDED = 'gw_items_price_refunded';

    /**
     * float
     */
    const GW_CARD_BASE_PRICE_REFUNDED = 'gw_card_base_price_refunded';

    /**
     * float
     */
    const GW_CARD_PRICE_REFUNDED = 'gw_card_price_refunded';

    /**
     * float
     */
    const GW_BASE_TAX_AMOUNT_REFUNDED = 'gw_base_tax_amount_refunded';

    /**
     * float
     */
    const GW_TAX_AMOUNT_REFUNDED = 'gw_tax_amount_refunded';

    /**
     * float
     */
    const GW_ITEMS_BASE_TAX_REFUNDED = 'gw_items_base_tax_refunded';

    /**
     * float
     */
    const GW_ITEMS_TAX_REFUNDED = 'gw_items_tax_refunded';

    /**
     * float
     */
    const GW_CARD_BASE_TAX_REFUNDED = 'gw_card_base_tax_refunded';

    /**
     * float
     */
    const GW_CARD_TAX_REFUNDED = 'gw_card_tax_refunded';

    /**
     * int
     */
    const PAYPAL_IPN_CUSTOMER_NOTIFIED = 'paypal_ipn_customer_notified';

    /**
     * int
     */
    const REWARD_POINTS_BALANCE = 'reward_points_balance';

    /**
     * float
     */
    const BASE_REWARD_CURRENCY_AMOUNT = 'base_reward_currency_amount';

    /**
     * float
     */
    const REWARD_CURRENCY_AMOUNT = 'reward_currency_amount';

    /**
     * float
     */
    const BASE_RWRD_CRRNCY_AMT_INVOICED = 'base_rwrd_crrncy_amt_invoiced';

    /**
     * float
     */
    const RWRD_CURRENCY_AMOUNT_INVOICED = 'rwrd_currency_amount_invoiced';

    /**
     * float
     */
    const BASE_RWRD_CRRNCY_AMNT_REFNDED = 'base_rwrd_crrncy_amnt_refnded';

    /**
     * float
     */
    const RWRD_CRRNCY_AMNT_REFUNDED = 'rwrd_crrncy_amnt_refunded';

    /**
     * int
     */
    const REWARD_POINTS_BALANCE_REFUND = 'reward_points_balance_refund';

    /**
     * int
     */
    const REWARD_POINTS_BALANCE_REFUNDED = 'reward_points_balance_refunded';

    /**
     * int
     */
    const REWARD_SALESRULE_POINTS = 'reward_salesrule_points';

    /**
     * Returns adjustment_negative
     *
     * @return float
     */
    public function getAdjustmentNegative()
    {
        return $this->_get(self::ADJUSTMENT_NEGATIVE);
    }

    /**
     * Returns adjustment_positive
     *
     * @return float
     */
    public function getAdjustmentPositive()
    {
        return $this->_get(self::ADJUSTMENT_POSITIVE);
    }

    /**
     * Returns applied_rule_ids
     *
     * @return string
     */
    public function getAppliedRuleIds()
    {
        return $this->_get(self::APPLIED_RULE_IDS);
    }

    /**
     * Returns base_adjustment_negative
     *
     * @return float
     */
    public function getBaseAdjustmentNegative()
    {
        return $this->_get(self::BASE_ADJUSTMENT_NEGATIVE);
    }

    /**
     * Returns base_adjustment_positive
     *
     * @return float
     */
    public function getBaseAdjustmentPositive()
    {
        return $this->_get(self::BASE_ADJUSTMENT_POSITIVE);
    }

    /**
     * Returns base_currency_code
     *
     * @return string
     */
    public function getBaseCurrencyCode()
    {
        return $this->_get(self::BASE_CURRENCY_CODE);
    }

    /**
     * Returns base_customer_balance_amount
     *
     * @return float
     */
    public function getBaseCustomerBalanceAmount()
    {
        return $this->_get(self::BASE_CUSTOMER_BALANCE_AMOUNT);
    }

    /**
     * Returns base_customer_balance_invoiced
     *
     * @return float
     */
    public function getBaseCustomerBalanceInvoiced()
    {
        return $this->_get(self::BASE_CUSTOMER_BALANCE_INVOICED);
    }

    /**
     * Returns base_customer_balance_refunded
     *
     * @return float
     */
    public function getBaseCustomerBalanceRefunded()
    {
        return $this->_get(self::BASE_CUSTOMER_BALANCE_REFUNDED);
    }

    /**
     * Returns base_discount_amount
     *
     * @return float
     */
    public function getBaseDiscountAmount()
    {
        return $this->_get(self::BASE_DISCOUNT_AMOUNT);
    }

    /**
     * Returns base_discount_canceled
     *
     * @return float
     */
    public function getBaseDiscountCanceled()
    {
        return $this->_get(self::BASE_DISCOUNT_CANCELED);
    }

    /**
     * Returns base_discount_invoiced
     *
     * @return float
     */
    public function getBaseDiscountInvoiced()
    {
        return $this->_get(self::BASE_DISCOUNT_INVOICED);
    }

    /**
     * Returns base_discount_refunded
     *
     * @return float
     */
    public function getBaseDiscountRefunded()
    {
        return $this->_get(self::BASE_DISCOUNT_REFUNDED);
    }

    /**
     * Returns base_gift_cards_amount
     *
     * @return float
     */
    public function getBaseGiftCardsAmount()
    {
        return $this->_get(self::BASE_GIFT_CARDS_AMOUNT);
    }

    /**
     * Returns base_gift_cards_invoiced
     *
     * @return float
     */
    public function getBaseGiftCardsInvoiced()
    {
        return $this->_get(self::BASE_GIFT_CARDS_INVOICED);
    }

    /**
     * Returns base_gift_cards_refunded
     *
     * @return float
     */
    public function getBaseGiftCardsRefunded()
    {
        return $this->_get(self::BASE_GIFT_CARDS_REFUNDED);
    }

    /**
     * Returns base_grand_total
     *
     * @return float
     */
    public function getBaseGrandTotal()
    {
        return $this->_get(self::BASE_GRAND_TOTAL);
    }

    /**
     * Returns base_hidden_tax_amount
     *
     * @return float
     */
    public function getBaseHiddenTaxAmount()
    {
        return $this->_get(self::BASE_HIDDEN_TAX_AMOUNT);
    }

    /**
     * Returns base_hidden_tax_invoiced
     *
     * @return float
     */
    public function getBaseHiddenTaxInvoiced()
    {
        return $this->_get(self::BASE_HIDDEN_TAX_INVOICED);
    }

    /**
     * Returns base_hidden_tax_refunded
     *
     * @return float
     */
    public function getBaseHiddenTaxRefunded()
    {
        return $this->_get(self::BASE_HIDDEN_TAX_REFUNDED);
    }

    /**
     * Returns base_reward_currency_amount
     *
     * @return float
     */
    public function getBaseRewardCurrencyAmount()
    {
        return $this->_get(self::BASE_REWARD_CURRENCY_AMOUNT);
    }

    /**
     * Returns base_rwrd_crrncy_amnt_refnded
     *
     * @return float
     */
    public function getBaseRwrdCrrncyAmntRefnded()
    {
        return $this->_get(self::BASE_RWRD_CRRNCY_AMNT_REFNDED);
    }

    /**
     * Returns base_rwrd_crrncy_amt_invoiced
     *
     * @return float
     */
    public function getBaseRwrdCrrncyAmtInvoiced()
    {
        return $this->_get(self::BASE_RWRD_CRRNCY_AMT_INVOICED);
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
     * Returns base_shipping_canceled
     *
     * @return float
     */
    public function getBaseShippingCanceled()
    {
        return $this->_get(self::BASE_SHIPPING_CANCELED);
    }

    /**
     * Returns base_shipping_discount_amount
     *
     * @return float
     */
    public function getBaseShippingDiscountAmount()
    {
        return $this->_get(self::BASE_SHIPPING_DISCOUNT_AMOUNT);
    }

    /**
     * Returns base_shipping_hidden_tax_amnt
     *
     * @return float
     */
    public function getBaseShippingHiddenTaxAmnt()
    {
        return $this->_get(self::BASE_SHIPPING_HIDDEN_TAX_AMNT);
    }

    /**
     * Returns base_shipping_incl_tax
     *
     * @return float
     */
    public function getBaseShippingInclTax()
    {
        return $this->_get(self::BASE_SHIPPING_INCL_TAX);
    }

    /**
     * Returns base_shipping_invoiced
     *
     * @return float
     */
    public function getBaseShippingInvoiced()
    {
        return $this->_get(self::BASE_SHIPPING_INVOICED);
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
     * Returns base_shipping_tax_amount
     *
     * @return float
     */
    public function getBaseShippingTaxAmount()
    {
        return $this->_get(self::BASE_SHIPPING_TAX_AMOUNT);
    }

    /**
     * Returns base_shipping_tax_refunded
     *
     * @return float
     */
    public function getBaseShippingTaxRefunded()
    {
        return $this->_get(self::BASE_SHIPPING_TAX_REFUNDED);
    }

    /**
     * Returns base_subtotal
     *
     * @return float
     */
    public function getBaseSubtotal()
    {
        return $this->_get(self::BASE_SUBTOTAL);
    }

    /**
     * Returns base_subtotal_canceled
     *
     * @return float
     */
    public function getBaseSubtotalCanceled()
    {
        return $this->_get(self::BASE_SUBTOTAL_CANCELED);
    }

    /**
     * Returns base_subtotal_incl_tax
     *
     * @return float
     */
    public function getBaseSubtotalInclTax()
    {
        return $this->_get(self::BASE_SUBTOTAL_INCL_TAX);
    }

    /**
     * Returns base_subtotal_invoiced
     *
     * @return float
     */
    public function getBaseSubtotalInvoiced()
    {
        return $this->_get(self::BASE_SUBTOTAL_INVOICED);
    }

    /**
     * Returns base_subtotal_refunded
     *
     * @return float
     */
    public function getBaseSubtotalRefunded()
    {
        return $this->_get(self::BASE_SUBTOTAL_REFUNDED);
    }

    /**
     * Returns base_tax_amount
     *
     * @return float
     */
    public function getBaseTaxAmount()
    {
        return $this->_get(self::BASE_TAX_AMOUNT);
    }

    /**
     * Returns base_tax_canceled
     *
     * @return float
     */
    public function getBaseTaxCanceled()
    {
        return $this->_get(self::BASE_TAX_CANCELED);
    }

    /**
     * Returns base_tax_invoiced
     *
     * @return float
     */
    public function getBaseTaxInvoiced()
    {
        return $this->_get(self::BASE_TAX_INVOICED);
    }

    /**
     * Returns base_tax_refunded
     *
     * @return float
     */
    public function getBaseTaxRefunded()
    {
        return $this->_get(self::BASE_TAX_REFUNDED);
    }

    /**
     * Returns base_total_canceled
     *
     * @return float
     */
    public function getBaseTotalCanceled()
    {
        return $this->_get(self::BASE_TOTAL_CANCELED);
    }

    /**
     * Returns base_total_due
     *
     * @return float
     */
    public function getBaseTotalDue()
    {
        return $this->_get(self::BASE_TOTAL_DUE);
    }

    /**
     * Returns base_total_invoiced
     *
     * @return float
     */
    public function getBaseTotalInvoiced()
    {
        return $this->_get(self::BASE_TOTAL_INVOICED);
    }

    /**
     * Returns base_total_invoiced_cost
     *
     * @return float
     */
    public function getBaseTotalInvoicedCost()
    {
        return $this->_get(self::BASE_TOTAL_INVOICED_COST);
    }

    /**
     * Returns base_total_offline_refunded
     *
     * @return float
     */
    public function getBaseTotalOfflineRefunded()
    {
        return $this->_get(self::BASE_TOTAL_OFFLINE_REFUNDED);
    }

    /**
     * Returns base_total_online_refunded
     *
     * @return float
     */
    public function getBaseTotalOnlineRefunded()
    {
        return $this->_get(self::BASE_TOTAL_ONLINE_REFUNDED);
    }

    /**
     * Returns base_total_paid
     *
     * @return float
     */
    public function getBaseTotalPaid()
    {
        return $this->_get(self::BASE_TOTAL_PAID);
    }

    /**
     * Returns base_total_qty_ordered
     *
     * @return float
     */
    public function getBaseTotalQtyOrdered()
    {
        return $this->_get(self::BASE_TOTAL_QTY_ORDERED);
    }

    /**
     * Returns base_total_refunded
     *
     * @return float
     */
    public function getBaseTotalRefunded()
    {
        return $this->_get(self::BASE_TOTAL_REFUNDED);
    }

    /**
     * Returns base_to_global_rate
     *
     * @return float
     */
    public function getBaseToGlobalRate()
    {
        return $this->_get(self::BASE_TO_GLOBAL_RATE);
    }

    /**
     * Returns base_to_order_rate
     *
     * @return float
     */
    public function getBaseToOrderRate()
    {
        return $this->_get(self::BASE_TO_ORDER_RATE);
    }

    /**
     * Returns billing_address_id
     *
     * @return int
     */
    public function getBillingAddressId()
    {
        return $this->_get(self::BILLING_ADDRESS_ID);
    }

    /**
     * Returns bs_customer_bal_total_refunded
     *
     * @return float
     */
    public function getBsCustomerBalTotalRefunded()
    {
        return $this->_get(self::BS_CUSTOMER_BAL_TOTAL_REFUNDED);
    }

    /**
     * Returns can_ship_partially
     *
     * @return int
     */
    public function getCanShipPartially()
    {
        return $this->_get(self::CAN_SHIP_PARTIALLY);
    }

    /**
     * Returns can_ship_partially_item
     *
     * @return int
     */
    public function getCanShipPartiallyItem()
    {
        return $this->_get(self::CAN_SHIP_PARTIALLY_ITEM);
    }

    /**
     * Returns coupon_code
     *
     * @return string
     */
    public function getCouponCode()
    {
        return $this->_get(self::COUPON_CODE);
    }

    /**
     * Returns coupon_rule_name
     *
     * @return string
     */
    public function getCouponRuleName()
    {
        return $this->_get(self::COUPON_RULE_NAME);
    }

    /**
     * Returns created_at
     *
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->_get(self::CREATED_AT);
    }

    /**
     * Returns customer_balance_amount
     *
     * @return float
     */
    public function getCustomerBalanceAmount()
    {
        return $this->_get(self::CUSTOMER_BALANCE_AMOUNT);
    }

    /**
     * Returns customer_balance_invoiced
     *
     * @return float
     */
    public function getCustomerBalanceInvoiced()
    {
        return $this->_get(self::CUSTOMER_BALANCE_INVOICED);
    }

    /**
     * Returns customer_balance_refunded
     *
     * @return float
     */
    public function getCustomerBalanceRefunded()
    {
        return $this->_get(self::CUSTOMER_BALANCE_REFUNDED);
    }

    /**
     * Returns customer_bal_total_refunded
     *
     * @return float
     */
    public function getCustomerBalTotalRefunded()
    {
        return $this->_get(self::CUSTOMER_BAL_TOTAL_REFUNDED);
    }

    /**
     * Returns customer_dob
     *
     * @return string
     */
    public function getCustomerDob()
    {
        return $this->_get(self::CUSTOMER_DOB);
    }

    /**
     * Returns customer_email
     *
     * @return string
     */
    public function getCustomerEmail()
    {
        return $this->_get(self::CUSTOMER_EMAIL);
    }

    /**
     * Returns customer_firstname
     *
     * @return string
     */
    public function getCustomerFirstname()
    {
        return $this->_get(self::CUSTOMER_FIRSTNAME);
    }

    /**
     * Returns customer_gender
     *
     * @return int
     */
    public function getCustomerGender()
    {
        return $this->_get(self::CUSTOMER_GENDER);
    }

    /**
     * Returns customer_group_id
     *
     * @return int
     */
    public function getCustomerGroupId()
    {
        return $this->_get(self::CUSTOMER_GROUP_ID);
    }

    /**
     * Returns customer_id
     *
     * @return int
     */
    public function getCustomerId()
    {
        return $this->_get(self::CUSTOMER_ID);
    }

    /**
     * Returns customer_is_guest
     *
     * @return int
     */
    public function getCustomerIsGuest()
    {
        return $this->_get(self::CUSTOMER_IS_GUEST);
    }

    /**
     * Returns customer_lastname
     *
     * @return string
     */
    public function getCustomerLastname()
    {
        return $this->_get(self::CUSTOMER_LASTNAME);
    }

    /**
     * Returns customer_middlename
     *
     * @return string
     */
    public function getCustomerMiddlename()
    {
        return $this->_get(self::CUSTOMER_MIDDLENAME);
    }

    /**
     * Returns customer_note
     *
     * @return string
     */
    public function getCustomerNote()
    {
        return $this->_get(self::CUSTOMER_NOTE);
    }

    /**
     * Returns customer_note_notify
     *
     * @return int
     */
    public function getCustomerNoteNotify()
    {
        return $this->_get(self::CUSTOMER_NOTE_NOTIFY);
    }

    /**
     * Returns customer_prefix
     *
     * @return string
     */
    public function getCustomerPrefix()
    {
        return $this->_get(self::CUSTOMER_PREFIX);
    }

    /**
     * Returns customer_suffix
     *
     * @return string
     */
    public function getCustomerSuffix()
    {
        return $this->_get(self::CUSTOMER_SUFFIX);
    }

    /**
     * Returns customer_taxvat
     *
     * @return string
     */
    public function getCustomerTaxvat()
    {
        return $this->_get(self::CUSTOMER_TAXVAT);
    }

    /**
     * Returns discount_amount
     *
     * @return float
     */
    public function getDiscountAmount()
    {
        return $this->_get(self::DISCOUNT_AMOUNT);
    }

    /**
     * Returns discount_canceled
     *
     * @return float
     */
    public function getDiscountCanceled()
    {
        return $this->_get(self::DISCOUNT_CANCELED);
    }

    /**
     * Returns discount_description
     *
     * @return string
     */
    public function getDiscountDescription()
    {
        return $this->_get(self::DISCOUNT_DESCRIPTION);
    }

    /**
     * Returns discount_invoiced
     *
     * @return float
     */
    public function getDiscountInvoiced()
    {
        return $this->_get(self::DISCOUNT_INVOICED);
    }

    /**
     * Returns discount_refunded
     *
     * @return float
     */
    public function getDiscountRefunded()
    {
        return $this->_get(self::DISCOUNT_REFUNDED);
    }

    /**
     * Returns edit_increment
     *
     * @return int
     */
    public function getEditIncrement()
    {
        return $this->_get(self::EDIT_INCREMENT);
    }

    /**
     * Returns email_sent
     *
     * @return int
     */
    public function getEmailSent()
    {
        return $this->_get(self::EMAIL_SENT);
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
     * Returns ext_customer_id
     *
     * @return string
     */
    public function getExtCustomerId()
    {
        return $this->_get(self::EXT_CUSTOMER_ID);
    }

    /**
     * Returns ext_order_id
     *
     * @return string
     */
    public function getExtOrderId()
    {
        return $this->_get(self::EXT_ORDER_ID);
    }

    /**
     * Returns forced_shipment_with_invoice
     *
     * @return int
     */
    public function getForcedShipmentWithInvoice()
    {
        return $this->_get(self::FORCED_SHIPMENT_WITH_INVOICE);
    }

    /**
     * Returns gift_cards
     *
     * @return string
     */
    public function getGiftCards()
    {
        return $this->_get(self::GIFT_CARDS);
    }

    /**
     * Returns gift_cards_amount
     *
     * @return float
     */
    public function getGiftCardsAmount()
    {
        return $this->_get(self::GIFT_CARDS_AMOUNT);
    }

    /**
     * Returns gift_cards_invoiced
     *
     * @return float
     */
    public function getGiftCardsInvoiced()
    {
        return $this->_get(self::GIFT_CARDS_INVOICED);
    }

    /**
     * Returns gift_cards_refunded
     *
     * @return float
     */
    public function getGiftCardsRefunded()
    {
        return $this->_get(self::GIFT_CARDS_REFUNDED);
    }

    /**
     * Returns gift_message_id
     *
     * @return int
     */
    public function getGiftMessageId()
    {
        return $this->_get(self::GIFT_MESSAGE_ID);
    }

    /**
     * Returns global_currency_code
     *
     * @return string
     */
    public function getGlobalCurrencyCode()
    {
        return $this->_get(self::GLOBAL_CURRENCY_CODE);
    }

    /**
     * Returns grand_total
     *
     * @return float
     */
    public function getGrandTotal()
    {
        return $this->_get(self::GRAND_TOTAL);
    }

    /**
     * Returns gw_add_card
     *
     * @return int
     */
    public function getGwAddCard()
    {
        return $this->_get(self::GW_ADD_CARD);
    }

    /**
     * Returns gw_allow_gift_receipt
     *
     * @return int
     */
    public function getGwAllowGiftReceipt()
    {
        return $this->_get(self::GW_ALLOW_GIFT_RECEIPT);
    }

    /**
     * Returns gw_base_price
     *
     * @return float
     */
    public function getGwBasePrice()
    {
        return $this->_get(self::GW_BASE_PRICE);
    }

    /**
     * Returns gw_base_price_invoiced
     *
     * @return float
     */
    public function getGwBasePriceInvoiced()
    {
        return $this->_get(self::GW_BASE_PRICE_INVOICED);
    }

    /**
     * Returns gw_base_price_refunded
     *
     * @return float
     */
    public function getGwBasePriceRefunded()
    {
        return $this->_get(self::GW_BASE_PRICE_REFUNDED);
    }

    /**
     * Returns gw_base_tax_amount
     *
     * @return float
     */
    public function getGwBaseTaxAmount()
    {
        return $this->_get(self::GW_BASE_TAX_AMOUNT);
    }

    /**
     * Returns gw_base_tax_amount_invoiced
     *
     * @return float
     */
    public function getGwBaseTaxAmountInvoiced()
    {
        return $this->_get(self::GW_BASE_TAX_AMOUNT_INVOICED);
    }

    /**
     * Returns gw_base_tax_amount_refunded
     *
     * @return float
     */
    public function getGwBaseTaxAmountRefunded()
    {
        return $this->_get(self::GW_BASE_TAX_AMOUNT_REFUNDED);
    }

    /**
     * Returns gw_card_base_price
     *
     * @return float
     */
    public function getGwCardBasePrice()
    {
        return $this->_get(self::GW_CARD_BASE_PRICE);
    }

    /**
     * Returns gw_card_base_price_invoiced
     *
     * @return float
     */
    public function getGwCardBasePriceInvoiced()
    {
        return $this->_get(self::GW_CARD_BASE_PRICE_INVOICED);
    }

    /**
     * Returns gw_card_base_price_refunded
     *
     * @return float
     */
    public function getGwCardBasePriceRefunded()
    {
        return $this->_get(self::GW_CARD_BASE_PRICE_REFUNDED);
    }

    /**
     * Returns gw_card_base_tax_amount
     *
     * @return float
     */
    public function getGwCardBaseTaxAmount()
    {
        return $this->_get(self::GW_CARD_BASE_TAX_AMOUNT);
    }

    /**
     * Returns gw_card_base_tax_invoiced
     *
     * @return float
     */
    public function getGwCardBaseTaxInvoiced()
    {
        return $this->_get(self::GW_CARD_BASE_TAX_INVOICED);
    }

    /**
     * Returns gw_card_base_tax_refunded
     *
     * @return float
     */
    public function getGwCardBaseTaxRefunded()
    {
        return $this->_get(self::GW_CARD_BASE_TAX_REFUNDED);
    }

    /**
     * Returns gw_card_price
     *
     * @return float
     */
    public function getGwCardPrice()
    {
        return $this->_get(self::GW_CARD_PRICE);
    }

    /**
     * Returns gw_card_price_invoiced
     *
     * @return float
     */
    public function getGwCardPriceInvoiced()
    {
        return $this->_get(self::GW_CARD_PRICE_INVOICED);
    }

    /**
     * Returns gw_card_price_refunded
     *
     * @return float
     */
    public function getGwCardPriceRefunded()
    {
        return $this->_get(self::GW_CARD_PRICE_REFUNDED);
    }

    /**
     * Returns gw_card_tax_amount
     *
     * @return float
     */
    public function getGwCardTaxAmount()
    {
        return $this->_get(self::GW_CARD_TAX_AMOUNT);
    }

    /**
     * Returns gw_card_tax_invoiced
     *
     * @return float
     */
    public function getGwCardTaxInvoiced()
    {
        return $this->_get(self::GW_CARD_TAX_INVOICED);
    }

    /**
     * Returns gw_card_tax_refunded
     *
     * @return float
     */
    public function getGwCardTaxRefunded()
    {
        return $this->_get(self::GW_CARD_TAX_REFUNDED);
    }

    /**
     * Returns gw_id
     *
     * @return int
     */
    public function getGwId()
    {
        return $this->_get(self::GW_ID);
    }

    /**
     * Returns gw_items_base_price
     *
     * @return float
     */
    public function getGwItemsBasePrice()
    {
        return $this->_get(self::GW_ITEMS_BASE_PRICE);
    }

    /**
     * Returns gw_items_base_price_invoiced
     *
     * @return float
     */
    public function getGwItemsBasePriceInvoiced()
    {
        return $this->_get(self::GW_ITEMS_BASE_PRICE_INVOICED);
    }

    /**
     * Returns gw_items_base_price_refunded
     *
     * @return float
     */
    public function getGwItemsBasePriceRefunded()
    {
        return $this->_get(self::GW_ITEMS_BASE_PRICE_REFUNDED);
    }

    /**
     * Returns gw_items_base_tax_amount
     *
     * @return float
     */
    public function getGwItemsBaseTaxAmount()
    {
        return $this->_get(self::GW_ITEMS_BASE_TAX_AMOUNT);
    }

    /**
     * Returns gw_items_base_tax_invoiced
     *
     * @return float
     */
    public function getGwItemsBaseTaxInvoiced()
    {
        return $this->_get(self::GW_ITEMS_BASE_TAX_INVOICED);
    }

    /**
     * Returns gw_items_base_tax_refunded
     *
     * @return float
     */
    public function getGwItemsBaseTaxRefunded()
    {
        return $this->_get(self::GW_ITEMS_BASE_TAX_REFUNDED);
    }

    /**
     * Returns gw_items_price
     *
     * @return float
     */
    public function getGwItemsPrice()
    {
        return $this->_get(self::GW_ITEMS_PRICE);
    }

    /**
     * Returns gw_items_price_invoiced
     *
     * @return float
     */
    public function getGwItemsPriceInvoiced()
    {
        return $this->_get(self::GW_ITEMS_PRICE_INVOICED);
    }

    /**
     * Returns gw_items_price_refunded
     *
     * @return float
     */
    public function getGwItemsPriceRefunded()
    {
        return $this->_get(self::GW_ITEMS_PRICE_REFUNDED);
    }

    /**
     * Returns gw_items_tax_amount
     *
     * @return float
     */
    public function getGwItemsTaxAmount()
    {
        return $this->_get(self::GW_ITEMS_TAX_AMOUNT);
    }

    /**
     * Returns gw_items_tax_invoiced
     *
     * @return float
     */
    public function getGwItemsTaxInvoiced()
    {
        return $this->_get(self::GW_ITEMS_TAX_INVOICED);
    }

    /**
     * Returns gw_items_tax_refunded
     *
     * @return float
     */
    public function getGwItemsTaxRefunded()
    {
        return $this->_get(self::GW_ITEMS_TAX_REFUNDED);
    }

    /**
     * Returns gw_price
     *
     * @return float
     */
    public function getGwPrice()
    {
        return $this->_get(self::GW_PRICE);
    }

    /**
     * Returns gw_price_invoiced
     *
     * @return float
     */
    public function getGwPriceInvoiced()
    {
        return $this->_get(self::GW_PRICE_INVOICED);
    }

    /**
     * Returns gw_price_refunded
     *
     * @return float
     */
    public function getGwPriceRefunded()
    {
        return $this->_get(self::GW_PRICE_REFUNDED);
    }

    /**
     * Returns gw_tax_amount
     *
     * @return float
     */
    public function getGwTaxAmount()
    {
        return $this->_get(self::GW_TAX_AMOUNT);
    }

    /**
     * Returns gw_tax_amount_invoiced
     *
     * @return float
     */
    public function getGwTaxAmountInvoiced()
    {
        return $this->_get(self::GW_TAX_AMOUNT_INVOICED);
    }

    /**
     * Returns gw_tax_amount_refunded
     *
     * @return float
     */
    public function getGwTaxAmountRefunded()
    {
        return $this->_get(self::GW_TAX_AMOUNT_REFUNDED);
    }

    /**
     * Returns hidden_tax_amount
     *
     * @return float
     */
    public function getHiddenTaxAmount()
    {
        return $this->_get(self::HIDDEN_TAX_AMOUNT);
    }

    /**
     * Returns hidden_tax_invoiced
     *
     * @return float
     */
    public function getHiddenTaxInvoiced()
    {
        return $this->_get(self::HIDDEN_TAX_INVOICED);
    }

    /**
     * Returns hidden_tax_refunded
     *
     * @return float
     */
    public function getHiddenTaxRefunded()
    {
        return $this->_get(self::HIDDEN_TAX_REFUNDED);
    }

    /**
     * Returns hold_before_state
     *
     * @return string
     */
    public function getHoldBeforeState()
    {
        return $this->_get(self::HOLD_BEFORE_STATE);
    }

    /**
     * Returns hold_before_status
     *
     * @return string
     */
    public function getHoldBeforeStatus()
    {
        return $this->_get(self::HOLD_BEFORE_STATUS);
    }

    /**
     * Returns increment_id
     *
     * @return string
     */
    public function getIncrementId()
    {
        return $this->_get(self::INCREMENT_ID);
    }

    /**
     * Returns is_virtual
     *
     * @return int
     */
    public function getIsVirtual()
    {
        return $this->_get(self::IS_VIRTUAL);
    }

    /**
     * Returns order_currency_code
     *
     * @return string
     */
    public function getOrderCurrencyCode()
    {
        return $this->_get(self::ORDER_CURRENCY_CODE);
    }

    /**
     * Returns original_increment_id
     *
     * @return string
     */
    public function getOriginalIncrementId()
    {
        return $this->_get(self::ORIGINAL_INCREMENT_ID);
    }

    /**
     * Returns payment_authorization_amount
     *
     * @return float
     */
    public function getPaymentAuthorizationAmount()
    {
        return $this->_get(self::PAYMENT_AUTHORIZATION_AMOUNT);
    }

    /**
     * Returns payment_auth_expiration
     *
     * @return int
     */
    public function getPaymentAuthExpiration()
    {
        return $this->_get(self::PAYMENT_AUTH_EXPIRATION);
    }

    /**
     * Returns paypal_ipn_customer_notified
     *
     * @return int
     */
    public function getPaypalIpnCustomerNotified()
    {
        return $this->_get(self::PAYPAL_IPN_CUSTOMER_NOTIFIED);
    }

    /**
     * Returns protect_code
     *
     * @return string
     */
    public function getProtectCode()
    {
        return $this->_get(self::PROTECT_CODE);
    }

    /**
     * Returns quote_address_id
     *
     * @return int
     */
    public function getQuoteAddressId()
    {
        return $this->_get(self::QUOTE_ADDRESS_ID);
    }

    /**
     * Returns quote_id
     *
     * @return int
     */
    public function getQuoteId()
    {
        return $this->_get(self::QUOTE_ID);
    }

    /**
     * Returns relation_child_id
     *
     * @return string
     */
    public function getRelationChildId()
    {
        return $this->_get(self::RELATION_CHILD_ID);
    }

    /**
     * Returns relation_child_real_id
     *
     * @return string
     */
    public function getRelationChildRealId()
    {
        return $this->_get(self::RELATION_CHILD_REAL_ID);
    }

    /**
     * Returns relation_parent_id
     *
     * @return string
     */
    public function getRelationParentId()
    {
        return $this->_get(self::RELATION_PARENT_ID);
    }

    /**
     * Returns relation_parent_real_id
     *
     * @return string
     */
    public function getRelationParentRealId()
    {
        return $this->_get(self::RELATION_PARENT_REAL_ID);
    }

    /**
     * Returns remote_ip
     *
     * @return string
     */
    public function getRemoteIp()
    {
        return $this->_get(self::REMOTE_IP);
    }

    /**
     * Returns reward_currency_amount
     *
     * @return float
     */
    public function getRewardCurrencyAmount()
    {
        return $this->_get(self::REWARD_CURRENCY_AMOUNT);
    }

    /**
     * Returns reward_points_balance
     *
     * @return int
     */
    public function getRewardPointsBalance()
    {
        return $this->_get(self::REWARD_POINTS_BALANCE);
    }

    /**
     * Returns reward_points_balance_refund
     *
     * @return int
     */
    public function getRewardPointsBalanceRefund()
    {
        return $this->_get(self::REWARD_POINTS_BALANCE_REFUND);
    }

    /**
     * Returns reward_points_balance_refunded
     *
     * @return int
     */
    public function getRewardPointsBalanceRefunded()
    {
        return $this->_get(self::REWARD_POINTS_BALANCE_REFUNDED);
    }

    /**
     * Returns reward_salesrule_points
     *
     * @return int
     */
    public function getRewardSalesrulePoints()
    {
        return $this->_get(self::REWARD_SALESRULE_POINTS);
    }

    /**
     * Returns rwrd_crrncy_amnt_refunded
     *
     * @return float
     */
    public function getRwrdCrrncyAmntRefunded()
    {
        return $this->_get(self::RWRD_CRRNCY_AMNT_REFUNDED);
    }

    /**
     * Returns rwrd_currency_amount_invoiced
     *
     * @return float
     */
    public function getRwrdCurrencyAmountInvoiced()
    {
        return $this->_get(self::RWRD_CURRENCY_AMOUNT_INVOICED);
    }

    /**
     * Returns shipping_address_id
     *
     * @return int
     */
    public function getShippingAddressId()
    {
        return $this->_get(self::SHIPPING_ADDRESS_ID);
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
     * Returns shipping_canceled
     *
     * @return float
     */
    public function getShippingCanceled()
    {
        return $this->_get(self::SHIPPING_CANCELED);
    }

    /**
     * Returns shipping_description
     *
     * @return string
     */
    public function getShippingDescription()
    {
        return $this->_get(self::SHIPPING_DESCRIPTION);
    }

    /**
     * Returns shipping_discount_amount
     *
     * @return float
     */
    public function getShippingDiscountAmount()
    {
        return $this->_get(self::SHIPPING_DISCOUNT_AMOUNT);
    }

    /**
     * Returns shipping_hidden_tax_amount
     *
     * @return float
     */
    public function getShippingHiddenTaxAmount()
    {
        return $this->_get(self::SHIPPING_HIDDEN_TAX_AMOUNT);
    }

    /**
     * Returns shipping_incl_tax
     *
     * @return float
     */
    public function getShippingInclTax()
    {
        return $this->_get(self::SHIPPING_INCL_TAX);
    }

    /**
     * Returns shipping_invoiced
     *
     * @return float
     */
    public function getShippingInvoiced()
    {
        return $this->_get(self::SHIPPING_INVOICED);
    }

    /**
     * Returns shipping_method
     *
     * @return string
     */
    public function getShippingMethod()
    {
        return $this->_get(self::SHIPPING_METHOD);
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

    /**
     * Returns shipping_tax_amount
     *
     * @return float
     */
    public function getShippingTaxAmount()
    {
        return $this->_get(self::SHIPPING_TAX_AMOUNT);
    }

    /**
     * Returns shipping_tax_refunded
     *
     * @return float
     */
    public function getShippingTaxRefunded()
    {
        return $this->_get(self::SHIPPING_TAX_REFUNDED);
    }

    /**
     * Returns state
     *
     * @return string
     */
    public function getState()
    {
        return $this->_get(self::STATE);
    }

    /**
     * Returns status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->_get(self::STATUS);
    }

    /**
     * Returns store_currency_code
     *
     * @return string
     */
    public function getStoreCurrencyCode()
    {
        return $this->_get(self::STORE_CURRENCY_CODE);
    }

    /**
     * Returns store_id
     *
     * @return int
     */
    public function getStoreId()
    {
        return $this->_get(self::STORE_ID);
    }

    /**
     * Returns store_name
     *
     * @return string
     */
    public function getStoreName()
    {
        return $this->_get(self::STORE_NAME);
    }

    /**
     * Returns store_to_base_rate
     *
     * @return float
     */
    public function getStoreToBaseRate()
    {
        return $this->_get(self::STORE_TO_BASE_RATE);
    }

    /**
     * Returns store_to_order_rate
     *
     * @return float
     */
    public function getStoreToOrderRate()
    {
        return $this->_get(self::STORE_TO_ORDER_RATE);
    }

    /**
     * Returns subtotal
     *
     * @return float
     */
    public function getSubtotal()
    {
        return $this->_get(self::SUBTOTAL);
    }

    /**
     * Returns subtotal_canceled
     *
     * @return float
     */
    public function getSubtotalCanceled()
    {
        return $this->_get(self::SUBTOTAL_CANCELED);
    }

    /**
     * Returns subtotal_incl_tax
     *
     * @return float
     */
    public function getSubtotalInclTax()
    {
        return $this->_get(self::SUBTOTAL_INCL_TAX);
    }

    /**
     * Returns subtotal_invoiced
     *
     * @return float
     */
    public function getSubtotalInvoiced()
    {
        return $this->_get(self::SUBTOTAL_INVOICED);
    }

    /**
     * Returns subtotal_refunded
     *
     * @return float
     */
    public function getSubtotalRefunded()
    {
        return $this->_get(self::SUBTOTAL_REFUNDED);
    }

    /**
     * Returns tax_amount
     *
     * @return float
     */
    public function getTaxAmount()
    {
        return $this->_get(self::TAX_AMOUNT);
    }

    /**
     * Returns tax_canceled
     *
     * @return float
     */
    public function getTaxCanceled()
    {
        return $this->_get(self::TAX_CANCELED);
    }

    /**
     * Returns tax_invoiced
     *
     * @return float
     */
    public function getTaxInvoiced()
    {
        return $this->_get(self::TAX_INVOICED);
    }

    /**
     * Returns tax_refunded
     *
     * @return float
     */
    public function getTaxRefunded()
    {
        return $this->_get(self::TAX_REFUNDED);
    }

    /**
     * Returns total_canceled
     *
     * @return float
     */
    public function getTotalCanceled()
    {
        return $this->_get(self::TOTAL_CANCELED);
    }

    /**
     * Returns total_due
     *
     * @return float
     */
    public function getTotalDue()
    {
        return $this->_get(self::TOTAL_DUE);
    }

    /**
     * Returns total_invoiced
     *
     * @return float
     */
    public function getTotalInvoiced()
    {
        return $this->_get(self::TOTAL_INVOICED);
    }

    /**
     * Returns total_item_count
     *
     * @return int
     */
    public function getTotalItemCount()
    {
        return $this->_get(self::TOTAL_ITEM_COUNT);
    }

    /**
     * Returns total_offline_refunded
     *
     * @return float
     */
    public function getTotalOfflineRefunded()
    {
        return $this->_get(self::TOTAL_OFFLINE_REFUNDED);
    }

    /**
     * Returns total_online_refunded
     *
     * @return float
     */
    public function getTotalOnlineRefunded()
    {
        return $this->_get(self::TOTAL_ONLINE_REFUNDED);
    }

    /**
     * Returns total_paid
     *
     * @return float
     */
    public function getTotalPaid()
    {
        return $this->_get(self::TOTAL_PAID);
    }

    /**
     * Returns total_qty_ordered
     *
     * @return float
     */
    public function getTotalQtyOrdered()
    {
        return $this->_get(self::TOTAL_QTY_ORDERED);
    }

    /**
     * Returns total_refunded
     *
     * @return float
     */
    public function getTotalRefunded()
    {
        return $this->_get(self::TOTAL_REFUNDED);
    }

    /**
     * Returns updated_at
     *
     * @return string
     */
    public function getUpdatedAt()
    {
        return $this->_get(self::UPDATED_AT);
    }

    /**
     * Returns weight
     *
     * @return float
     */
    public function getWeight()
    {
        return $this->_get(self::WEIGHT);
    }

    /**
     * Returns x_forwarded_for
     *
     * @return string
     */
    public function getXForwardedFor()
    {
        return $this->_get(self::X_FORWARDED_FOR);
    }
}
