<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Api\Data;

interface OrderTaxDetailsInterface extends \Magento\Framework\Api\ExtensibleDataInterface
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
