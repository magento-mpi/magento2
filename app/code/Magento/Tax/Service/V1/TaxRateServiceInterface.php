<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Service\V1;

use Magento\Tax\Service\V1\Data\TaxRate;

interface TaxRateServiceInterface
{
    /**
     * @param TaxRate $taxRate
     * @return TaxRate
     */
    public function createTaxRate(TaxRate $taxRate);

    /**
     * @return TaxRate[]
     */
    public function getTaxRates();

    /**
     * @param TaxRate $taxRate
     * @return TaxRate
     */
    public function updateTaxRate(TaxRate $taxRate);

    /**
     * @param int $rateId
     * @return bool
     */
    public function deleteTaxRate($rateId);
}
