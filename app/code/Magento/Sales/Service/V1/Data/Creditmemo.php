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
 * Class Creditmemo
 */
class Creditmemo extends DataObject
{
    /**
     * int
     */
    const ENTITY_ID = 'entity_id';

    /**
     * int
     */
    const STORE_ID = 'store_id';

    /**
     * float
     */
    const ADJUSTMENT_POSITIVE = 'adjustment_positive';

    /**
     * float
     */
    const BASE_SHIPPING_TAX_AMOUNT = 'base_shipping_tax_amount';

    /**
     * float
     */
    const STORE_TO_ORDER_RATE = 'store_to_order_rate';

    /**
     * float
     */
    const BASE_DISCOUNT_AMOUNT = 'base_discount_amount';

    /**
     * float
     */
    const BASE_TO_ORDER_RATE = 'base_to_order_rate';

    /**
     * float
     */
    const GRAND_TOTAL = 'grand_total';

    /**
     * float
     */
    const BASE_ADJUSTMENT_NEGATIVE = 'base_adjustment_negative';

    /**
     * float
     */
    const BASE_SUBTOTAL_INCL_TAX = 'base_subtotal_incl_tax';

    /**
     * float
     */
    const SHIPPING_AMOUNT = 'shipping_amount';

    /**
     * float
     */
    const SUBTOTAL_INCL_TAX = 'subtotal_incl_tax';

    /**
     * float
     */
    const ADJUSTMENT_NEGATIVE = 'adjustment_negative';

    /**
     * float
     */
    const BASE_SHIPPING_AMOUNT = 'base_shipping_amount';

    /**
     * float
     */
    const STORE_TO_BASE_RATE = 'store_to_base_rate';

    /**
     * float
     */
    const BASE_TO_GLOBAL_RATE = 'base_to_global_rate';

    /**
     * float
     */
    const BASE_ADJUSTMENT = 'base_adjustment';

    /**
     * float
     */
    const BASE_SUBTOTAL = 'base_subtotal';

    /**
     * float
     */
    const DISCOUNT_AMOUNT = 'discount_amount';

    /**
     * float
     */
    const SUBTOTAL = 'subtotal';

    /**
     * float
     */
    const ADJUSTMENT = 'adjustment';

    /**
     * float
     */
    const BASE_GRAND_TOTAL = 'base_grand_total';

    /**
     * float
     */
    const BASE_ADJUSTMENT_POSITIVE = 'base_adjustment_positive';

    /**
     * float
     */
    const BASE_TAX_AMOUNT = 'base_tax_amount';

    /**
     * float
     */
    const SHIPPING_TAX_AMOUNT = 'shipping_tax_amount';

    /**
     * float
     */
    const TAX_AMOUNT = 'tax_amount';

    /**
     * int
     */
    const ORDER_ID = 'order_id';

    /**
     * int
     */
    const EMAIL_SENT = 'email_sent';

    /**
     * int
     */
    const CREDITMEMO_STATUS = 'creditmemo_status';

    /**
     * int
     */
    const STATE = 'state';

    /**
     * int
     */
    const SHIPPING_ADDRESS_ID = 'shipping_address_id';

    /**
     * int
     */
    const BILLING_ADDRESS_ID = 'billing_address_id';

    /**
     * int
     */
    const INVOICE_ID = 'invoice_id';

    /**
     * string
     */
    const STORE_CURRENCY_CODE = 'store_currency_code';

    /**
     * string
     */
    const ORDER_CURRENCY_CODE = 'order_currency_code';

    /**
     * string
     */
    const BASE_CURRENCY_CODE = 'base_currency_code';

    /**
     * string
     */
    const GLOBAL_CURRENCY_CODE = 'global_currency_code';

    /**
     * string
     */
    const TRANSACTION_ID = 'transaction_id';

    /**
     * string
     */
    const INCREMENT_ID = 'increment_id';

    /**
     * string
     */
    const CREATED_AT = 'created_at';

    /**
     * string
     */
    const UPDATED_AT = 'updated_at';

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
    const SHIPPING_INCL_TAX = 'shipping_incl_tax';

    /**
     * float
     */
    const BASE_SHIPPING_INCL_TAX = 'base_shipping_incl_tax';

    /**
     * string
     */
    const DISCOUNT_DESCRIPTION = 'discount_description';

    /**
     * Returns adjustment
     *
     * @return float
     */
    public function getAdjustment()
    {
        return $this->_get(self::ADJUSTMENT);
    }

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
     * Returns base_adjustment
     *
     * @return float
     */
    public function getBaseAdjustment()
    {
        return $this->_get(self::BASE_ADJUSTMENT);
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
     * Returns base_discount_amount
     *
     * @return float
     */
    public function getBaseDiscountAmount()
    {
        return $this->_get(self::BASE_DISCOUNT_AMOUNT);
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
     * Returns base_shipping_amount
     *
     * @return float
     */
    public function getBaseShippingAmount()
    {
        return $this->_get(self::BASE_SHIPPING_AMOUNT);
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
     * Returns base_shipping_tax_amount
     *
     * @return float
     */
    public function getBaseShippingTaxAmount()
    {
        return $this->_get(self::BASE_SHIPPING_TAX_AMOUNT);
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
     * Returns base_subtotal_incl_tax
     *
     * @return float
     */
    public function getBaseSubtotalInclTax()
    {
        return $this->_get(self::BASE_SUBTOTAL_INCL_TAX);
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
     * Returns created_at
     *
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->_get(self::CREATED_AT);
    }

    /**
     * Returns creditmemo_status
     *
     * @return int
     */
    public function getCreditmemoStatus()
    {
        return $this->_get(self::CREDITMEMO_STATUS);
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
     * Returns discount_description
     *
     * @return string
     */
    public function getDiscountDescription()
    {
        return $this->_get(self::DISCOUNT_DESCRIPTION);
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
     * Returns hidden_tax_amount
     *
     * @return float
     */
    public function getHiddenTaxAmount()
    {
        return $this->_get(self::HIDDEN_TAX_AMOUNT);
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
     * Returns invoice_id
     *
     * @return int
     */
    public function getInvoiceId()
    {
        return $this->_get(self::INVOICE_ID);
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
     * Returns order_id
     *
     * @return int
     */
    public function getOrderId()
    {
        return $this->_get(self::ORDER_ID);
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
     * Returns shipping_tax_amount
     *
     * @return float
     */
    public function getShippingTaxAmount()
    {
        return $this->_get(self::SHIPPING_TAX_AMOUNT);
    }

    /**
     * Returns state
     *
     * @return int
     */
    public function getState()
    {
        return $this->_get(self::STATE);
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
     * Returns subtotal_incl_tax
     *
     * @return float
     */
    public function getSubtotalInclTax()
    {
        return $this->_get(self::SUBTOTAL_INCL_TAX);
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
     * Returns transaction_id
     *
     * @return string
     */
    public function getTransactionId()
    {
        return $this->_get(self::TRANSACTION_ID);
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
}
