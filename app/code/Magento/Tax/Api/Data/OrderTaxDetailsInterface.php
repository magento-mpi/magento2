<?php

namespace Magento\Tax\Api\Data;

/**
 * @see \Magento\Tax\Service\V1\Data\OrderTaxDetails
 */
interface OrderTaxDetailsInterface
{
    const KEY_APPLIED_TAXES = 'applied_taxes';

    const KEY_ITEMS = 'items';

    /**
     * Get applied taxes at order level
     *
     * @return \Magento\Tax\Api\Data\OrderTaxDetailsAppliedTaxInterface[] | null
     */
    public function getAppliedTaxes();

    /**
     * Get order item tax details
     *
     * @return \Magento\Tax\Api\Data\OrderTaxDetailsItemInterface[] | null
     */
    public function getItems();
}
