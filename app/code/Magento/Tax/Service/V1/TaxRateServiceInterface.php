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
     * @param TaxRate $rate
     * @return TaxRate
     */
    public function createTaxRate(TaxRate $rate);

    /**
     * @return TaxRate[]
     */
    public function getTaxRates();

    /**
     * @param TaxRate $rate
     * @return TaxRate
     */
    public function updateTaxRate(TaxRate $rate);

    /**
     * @param int $rateId
     * @return bool
     */
    public function deleteTaxRate($rateId);
}
