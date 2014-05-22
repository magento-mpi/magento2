<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Service\V1;

use Magento\Tax\Service\V1\Data\TaxRate as TaxRateDataObject;

class TaxRateService implements TaxRateServiceInterface
{

    public function __construct()
    {

    }

    /**
     * @inheritdoc
     */
    public function createTaxRate(TaxRateDataObject $taxRate)
    {
        // TODO: Implement createTaxRate() method.
    }

    /**
     * @inheritdoc
     */
    public function getTaxRates()
    {
        // TODO: Implement getTaxRates() method.
    }

    /**
     * @inheritdoc
     */
    public function updateTaxRate(TaxRateDataObject $taxRate)
    {
        // TODO: Implement updateTaxRate() method.
    }

    /**
     * @inheritdoc
     */
    public function deleteTaxRate($rateId)
    {
        // TODO: Implement deleteTaxRate() method.
    }
}
