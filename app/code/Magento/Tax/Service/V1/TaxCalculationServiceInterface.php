<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Service\V1;

interface TaxCalculationServiceInterface
{
    /**
     * Calculate Tax
     *
     * @param \Magento\Tax\Service\V1\Data\QuoteDetails $quoteDetails
     * @param int $storeId
     * @return \Magento\Tax\Service\V1\Data\TaxDetails
     */
    public function calculateTax(\Magento\Tax\Service\V1\Data\QuoteDetails $quoteDetails, $storeId);
}
