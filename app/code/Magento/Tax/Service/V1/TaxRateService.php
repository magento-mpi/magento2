<?php
/**
 * Created by PhpStorm.
 * User: bimathew
 * Date: 5/21/14
 * Time: 11:53 AM
 */

namespace Magento\Tax\Service\V1;


use Magento\Tax\Service\V1\Data\TaxRate;

class TaxRateService implements TaxRateServiceInterface
{

    public function __construct()
    {

    }

    /**
     * @inheritdoc
     */
    public function createTaxRate(TaxRate $rate)
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
    public function updateTaxRate(TaxRate $rate)
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
