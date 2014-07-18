<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Service\V1\Data;

use Magento\Sales\Model\Order;

class OrderMapper
{
    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $orderFactory;

    /**
     * @var OrderBuilder
     */
    protected $orderBuilder;

    /**
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param OrderBuilder $orderBuilder
     */
    public function __construct(
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Sales\Service\V1\Data\OrderBuilder $orderBuilder
    ) {
        $this->orderFactory = $orderFactory;
        $this->orderBuilder = $orderBuilder;
    }

    /**
     * @param Order $order
     * @return \Magento\Framework\Service\Data\AbstractObject
     */
    public function extractDto(Order $order)
    {
        $this->orderBuilder->setAdjustmentNegative($order->getAdjustmentNegative());
        $this->orderBuilder->setAdjustmentPositive($order->getAdjustmentPositive());
        $this->orderBuilder->setAppliedRuleIds($order->getAppliedRuleIds());
        $this->orderBuilder->setBaseAdjustmentNegative($order->getBaseAdjustmentNegative());
        $this->orderBuilder->setBaseAdjustmentPositive($order->getBaseAdjustmentPositive());
        $this->orderBuilder->setBaseCurrencyCode($order->getBaseCurrencyCode());
        $this->orderBuilder->setBaseDiscountAmount($order->getBaseDiscountAmount());
        $this->orderBuilder->setBaseDiscountCanceled($order->getBaseDiscountCanceled());
        $this->orderBuilder->setBaseDiscountInvoiced($order->getBaseDiscountInvoiced());
        $this->orderBuilder->setBaseDiscountRefunded($order->getBaseDiscountRefunded());
        $this->orderBuilder->setBaseGrandTotal($order->getBaseGrandTotal());
        $this->orderBuilder->setBaseHiddenTaxAmount($order->getBaseHiddenTaxAmount());
        $this->orderBuilder->setBaseHiddenTaxInvoiced($order->getBaseHiddenTaxInvoiced());
        $this->orderBuilder->setBaseHiddenTaxRefunded($order->getBaseHiddenTaxRefunded());
        $this->orderBuilder->setBaseShippingAmount($order->getBaseShippingAmount());
        $this->orderBuilder->setBaseShippingCanceled($order->getBaseShippingCanceled());
        $this->orderBuilder->setBaseShippingDiscountAmount($order->getBaseShippingDiscountAmount());
        $this->orderBuilder->setBaseShippingHiddenTaxAmnt($order->getBaseShippingHiddenTaxAmnt());
        $this->orderBuilder->setBaseShippingInclTax($order->getBaseShippingInclTax());
        $this->orderBuilder->setBaseShippingInvoiced($order->getBaseShippingInvoiced());
        $this->orderBuilder->setBaseShippingRefunded($order->getBaseShippingRefunded());
        $this->orderBuilder->setBaseShippingTaxAmount($order->getBaseShippingTaxAmount());
        $this->orderBuilder->setBaseShippingTaxRefunded($order->getBaseShippingTaxRefunded());
        $this->orderBuilder->setBaseSubtotal($order->getBaseSubtotal());
        $this->orderBuilder->setBaseSubtotalCanceled($order->getBaseSubtotalCanceled());
        $this->orderBuilder->setBaseSubtotalInclTax($order->getBaseSubtotalInclTax());
        $this->orderBuilder->setBaseSubtotalInvoiced($order->getBaseSubtotalInvoiced());
        $this->orderBuilder->setBaseSubtotalRefunded($order->getBaseSubtotalRefunded());
        $this->orderBuilder->setBaseTaxAmount($order->getBaseTaxAmount());
        $this->orderBuilder->setBaseTaxCanceled($order->getBaseTaxCanceled());
        $this->orderBuilder->setBaseTaxInvoiced($order->getBaseTaxInvoiced());
        $this->orderBuilder->setBaseTaxRefunded($order->getBaseTaxRefunded());
        $this->orderBuilder->setBaseTotalCanceled($order->getBaseTotalCanceled());
        $this->orderBuilder->setBaseTotalDue($order->getBaseTotalDue());
        $this->orderBuilder->setBaseTotalInvoiced($order->getBaseTotalInvoiced());
        $this->orderBuilder->setBaseTotalInvoicedCost($order->getBaseTotalInvoicedCost());
        $this->orderBuilder->setBaseTotalOfflineRefunded($order->getBaseTotalOfflineRefunded());
        $this->orderBuilder->setBaseTotalOnlineRefunded($order->getBaseTotalOnlineRefunded());
        $this->orderBuilder->setBaseTotalPaid($order->getBaseTotalPaid());
        $this->orderBuilder->setBaseTotalQtyOrdered($order->getBaseTotalQtyOrdered());
        $this->orderBuilder->setBaseTotalRefunded($order->getBaseTotalRefunded());
        $this->orderBuilder->setBaseToGlobalRate($order->getBaseToGlobalRate());
        $this->orderBuilder->setBaseToOrderRate($order->getBaseToOrderRate());
        $this->orderBuilder->setBillingAddressId($order->getBillingAddressId());
        $this->orderBuilder->setCanShipPartially($order->getCanShipPartially());
        $this->orderBuilder->setCanShipPartiallyItem($order->getCanShipPartiallyItem());
        $this->orderBuilder->setCouponCode($order->getCouponCode());
        $this->orderBuilder->setCreatedAt($order->getCreatedAt());
        $this->orderBuilder->setCustomerDob($order->getCustomerDob());
        $this->orderBuilder->setCustomerEmail($order->getCustomerEmail());
        $this->orderBuilder->setCustomerFirstname($order->getCustomerFirstname());
        $this->orderBuilder->setCustomerGender($order->getCustomerGender());
        $this->orderBuilder->setCustomerGroupId($order->getCustomerGroupId());
        $this->orderBuilder->setCustomerId($order->getCustomerId());
        $this->orderBuilder->setCustomerIsGuest($order->getCustomerIsGuest());
        $this->orderBuilder->setCustomerLastname($order->getCustomerLastname());
        $this->orderBuilder->setCustomerMiddlename($order->getCustomerMiddlename());
        $this->orderBuilder->setCustomerNote($order->getCustomerNote());
        $this->orderBuilder->setCustomerNoteNotify($order->getCustomerNoteNotify());
        $this->orderBuilder->setCustomerPrefix($order->getCustomerPrefix());
        $this->orderBuilder->setCustomerSuffix($order->getCustomerSuffix());
        $this->orderBuilder->setCustomerTaxvat($order->getCustomerTaxvat());
        $this->orderBuilder->setDiscountAmount($order->getDiscountAmount());
        $this->orderBuilder->setDiscountCanceled($order->getDiscountCanceled());
        $this->orderBuilder->setDiscountDescription($order->getDiscountDescription());
        $this->orderBuilder->setDiscountInvoiced($order->getDiscountInvoiced());
        $this->orderBuilder->setDiscountRefunded($order->getDiscountRefunded());
        $this->orderBuilder->setEditIncrement($order->getEditIncrement());
        $this->orderBuilder->setEmailSent($order->getEmailSent());
        $this->orderBuilder->setEntityId($order->getEntityId());
        $this->orderBuilder->setExtCustomerId($order->getExtCustomerId());
        $this->orderBuilder->setExtOrderId($order->getExtOrderId());
        $this->orderBuilder->setForcedShipmentWithInvoice($order->getForcedShipmentWithInvoice());
        $this->orderBuilder->setGlobalCurrencyCode($order->getGlobalCurrencyCode());
        $this->orderBuilder->setGrandTotal($order->getGrandTotal());
        $this->orderBuilder->setHiddenTaxAmount($order->getHiddenTaxAmount());
        $this->orderBuilder->setHiddenTaxInvoiced($order->getHiddenTaxInvoiced());
        $this->orderBuilder->setHiddenTaxRefunded($order->getHiddenTaxRefunded());
        $this->orderBuilder->setHoldBeforeState($order->getHoldBeforeState());
        $this->orderBuilder->setHoldBeforeStatus($order->getHoldBeforeStatus());
        $this->orderBuilder->setIncrementId($order->getIncrementId());
        $this->orderBuilder->setIsVirtual($order->getIsVirtual());
        $this->orderBuilder->setOrderCurrencyCode($order->getOrderCurrencyCode());
        $this->orderBuilder->setOriginalIncrementId($order->getOriginalIncrementId());
        $this->orderBuilder->setPaymentAuthorizationAmount($order->getPaymentAuthorizationAmount());
        $this->orderBuilder->setPaymentAuthExpiration($order->getPaymentAuthExpiration());
        $this->orderBuilder->setProtectCode($order->getProtectCode());
        $this->orderBuilder->setQuoteAddressId($order->getQuoteAddressId());
        $this->orderBuilder->setQuoteId($order->getQuoteId());
        $this->orderBuilder->setRelationChildId($order->getRelationChildId());
        $this->orderBuilder->setRelationChildRealId($order->getRelationChildRealId());
        $this->orderBuilder->setRelationParentId($order->getRelationParentId());
        $this->orderBuilder->setRelationParentRealId($order->getRelationParentRealId());
        $this->orderBuilder->setRemoteIp($order->getRemoteIp());
        $this->orderBuilder->setShippingAddressId($order->getShippingAddressId());
        $this->orderBuilder->setShippingAmount($order->getShippingAmount());
        $this->orderBuilder->setShippingCanceled($order->getShippingCanceled());
        $this->orderBuilder->setShippingDescription($order->getShippingDescription());
        $this->orderBuilder->setShippingDiscountAmount($order->getShippingDiscountAmount());
        $this->orderBuilder->setShippingHiddenTaxAmount($order->getShippingHiddenTaxAmount());
        $this->orderBuilder->setShippingInclTax($order->getShippingInclTax());
        $this->orderBuilder->setShippingInvoiced($order->getShippingInvoiced());
        $this->orderBuilder->setShippingMethod($order->getShippingMethod());
        $this->orderBuilder->setShippingRefunded($order->getShippingRefunded());
        $this->orderBuilder->setShippingTaxAmount($order->getShippingTaxAmount());
        $this->orderBuilder->setShippingTaxRefunded($order->getShippingTaxRefunded());
        $this->orderBuilder->setState($order->getState());
        $this->orderBuilder->setStatus($order->getStatus());
        $this->orderBuilder->setStoreCurrencyCode($order->getStoreCurrencyCode());
        $this->orderBuilder->setStoreId($order->getStoreId());
        $this->orderBuilder->setStoreName($order->getStoreName());
        $this->orderBuilder->setStoreToBaseRate($order->getStoreToBaseRate());
        $this->orderBuilder->setStoreToOrderRate($order->getStoreToOrderRate());
        $this->orderBuilder->setSubtotal($order->getSubtotal());
        $this->orderBuilder->setSubtotalCanceled($order->getSubtotalCanceled());
        $this->orderBuilder->setSubtotalInclTax($order->getSubtotalInclTax());
        $this->orderBuilder->setSubtotalInvoiced($order->getSubtotalInvoiced());
        $this->orderBuilder->setSubtotalRefunded($order->getSubtotalRefunded());
        $this->orderBuilder->setTaxAmount($order->getTaxAmount());
        $this->orderBuilder->setTaxCanceled($order->getTaxCanceled());
        $this->orderBuilder->setTaxInvoiced($order->getTaxInvoiced());
        $this->orderBuilder->setTaxRefunded($order->getTaxRefunded());
        $this->orderBuilder->setTotalCanceled($order->getTotalCanceled());
        $this->orderBuilder->setTotalDue($order->getTotalDue());
        $this->orderBuilder->setTotalInvoiced($order->getTotalInvoiced());
        $this->orderBuilder->setTotalItemCount($order->getTotalItemCount());
        $this->orderBuilder->setTotalOfflineRefunded($order->getTotalOfflineRefunded());
        $this->orderBuilder->setTotalOnlineRefunded($order->getTotalOnlineRefunded());
        $this->orderBuilder->setTotalPaid($order->getTotalPaid());
        $this->orderBuilder->setTotalQtyOrdered($order->getTotalQtyOrdered());
        $this->orderBuilder->setTotalRefunded($order->getTotalRefunded());
        $this->orderBuilder->setUpdatedAt($order->getUpdatedAt());
        $this->orderBuilder->setWeight($order->getWeight());
        $this->orderBuilder->setXForwardedFor($order->getXForwardedFor());
        return $this->orderBuilder->create();
    }

    public function populateModel($dataObject)
    {

    }
}
