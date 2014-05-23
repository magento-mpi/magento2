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
     * @return \Magento\Tax\Service\V1\Data\TaxRate
     */
    public function createTaxRate(TaxRateDataObject $taxRate);

    /**
     * @return \Magento\Tax\Service\V1\Data\TaxRate[]
     */
    public function getTaxRates();

    /**
     * @param \Magento\Tax\Service\V1\Data\TaxRate $taxRate
     * @return \Magento\Tax\Service\V1\Data\TaxRate
     */
    public function updateTaxRate(TaxRateDataObject $taxRate);

    /**
     * @param int $rateId
     * @return bool
     */
    public function deleteTaxRate($rateId);
}
