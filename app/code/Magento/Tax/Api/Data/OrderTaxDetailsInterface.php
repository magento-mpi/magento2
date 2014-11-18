<?php

namespace Magento\Tax\Api\Data;

/**
 * @see
 */
interface OrderTaxDetailsInterface
{
    const KEY_APPLIED_TAXES = 'applied_taxes';

    const KEY_ITEMS = 'items';

    /**
     * Get applied taxes at order level
     *
     * @return \Magento\Tax\Service\V1\Data\OrderTaxDetails\AppliedTax[] | null
     */
    public function getAppliedTaxes();

    /**
     * Get order item tax details
     *
     * @return \Magento\Tax\Service\V1\Data\OrderTaxDetails\Item[] | null
     */
    public function getItems();

}
