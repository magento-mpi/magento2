<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Service\V1;

use Magento\Tax\Service\V1\Data\TaxRate as TaxRateDataObject;

interface TaxRateServiceInterface
{
    /**
     * @param \Magento\Tax\Service\V1\Data\TaxRate $taxRate
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Model\Exception
     * @return \Magento\Tax\Service\V1\Data\TaxRate
     */
    public function createTaxRate(TaxRateDataObject $taxRate);

    /**
     * Get tax rates
     *
     * @return \Magento\Tax\Service\V1\Data\TaxRate[]
     */
    public function getTaxRates();

    /**
     * Update given tax rate
     *
     * @param \Magento\Tax\Service\V1\Data\TaxRate $taxRate
     * @return \Magento\Tax\Service\V1\Data\TaxRate
     */
    public function updateTaxRate(TaxRateDataObject $taxRate);

    /**
     * Delete tax rate
     *
     * @param int $rateId
     * @return bool
     */
    public function deleteTaxRate($rateId);
}
