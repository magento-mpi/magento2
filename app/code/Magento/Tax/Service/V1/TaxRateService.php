<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Service\V1;

use Magento\Tax\Model\Calculation\Rate\Converter;
use Magento\Tax\Service\V1\Data\TaxRate as TaxRateDataObject;
use Magento\Tax\Model\Calculation\RateFactory as TaxRateModelFactory;
use Magento\Tax\Model\Calculation\Rate as RateModel;

class TaxRateService implements TaxRateServiceInterface
{
    /** @var  TaxRateModelFactory */
    protected $rateModelFactory;

    /** @var  Converter */
    protected $converter;

    /**
     * Constructor
     *
     * @param TaxRateModelFactory $rateModelFactory
     * @param Converter $converter
     */
    public function __construct(
        TaxRateModelFactory $rateModelFactory,
        Converter $converter
    ) {
        $this->rateModelFactory = $rateModelFactory;
        $this->converter = $converter;
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
        $collection = $this->rateModelFactory->create()->getResourceCollection();
        $taxRates = [];
        /** @var RateModel $rateModel */
        foreach ($collection as $rateModel) {
            $taxRates[] = $this->converter->createTaxRateDataObjectFromModel($rateModel);
        }
        return $taxRates;
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
