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
    /**#@+
     * Type of calcuation used
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

    /**
     * Get default rate request
     *
     * @param int $productTaxClassID
     * @param int $customerId
     * @param string $storeId
     * @return float
     */
    public function getDefaultCalculatedRate($productTaxClassID, $customerId = null, $storeId = null);

    /**
     * Get rate request
     *
     * @param int $productTaxClassID
     * @param int $customerId
     * @param string $storeId
     * @return float
     */
    public function getCalculatedRate($productTaxClassID, $customerId = null, $storeId = null);
}
