<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Service\V1;
/**
 * @deprecated
 * @see \Magento\Tax\Api\TaxCalculationInterface
 */
interface TaxCalculationServiceInterface
{
    /**#@+
     * Type of calculation used
     */
    const CALC_UNIT_BASE = 'UNIT_BASE_CALCULATION';
    const CALC_ROW_BASE = 'ROW_BASE_CALCULATION';
    const CALC_TOTAL_BASE = 'TOTAL_BASE_CALCULATION';
    /**#@-*/

    /**
     * Calculate Tax
     *
     * @param \Magento\Tax\Service\V1\Data\QuoteDetails $quoteDetails
     * @param null|int $storeId
     * @return \Magento\Tax\Service\V1\Data\TaxDetails
     */
    public function calculateTax(\Magento\Tax\Service\V1\Data\QuoteDetails $quoteDetails, $storeId = null);
}
