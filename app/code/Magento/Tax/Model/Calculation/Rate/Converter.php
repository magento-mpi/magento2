<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Model\Calculation\Rate;

use Magento\Tax\Model\Calculation\Rate as TaxRateModel;
use Magento\Tax\Model\Calculation\RateFactory as TaxRateModelFactory;
use Magento\Tax\Service\V1\Data\TaxRate as TaxRateDataObject;
use Magento\Tax\Service\V1\Data\TaxRateBuilder as TaxRateDataObjectBuilder;
use Magento\Tax\Service\V1\Data\ZipRangeBuilder as ZipRangeDataObjectBuilder;


/**
 * Tax Rate Model converter.
 *
 * Converts a Tax Rate Model to a Data Object or vice versa.
 */
class Converter
{
    /**
     * @var TaxRateDataObjectBuilder
     */
    protected $taxRateDataObjectBuilder;

    /**
     * @var TaxRateModelFactory
     */
    protected $taxRateModelFactory;

    /**
     * @var ZipRangeDataObjectBuilder
     */
    protected $zipRangeDataObjectBuilder;

    /**
     * @param TaxRateDataObjectBuilder $taxRateDataObjectBuilder
     * @param TaxRateModelFactory $taxRateModelFactory
     * @param ZipRangeDataObjectBuilder $zipRangeBuilder
     */
    public function __construct(
        TaxRateDataObjectBuilder $taxRateDataObjectBuilder,
        TaxRateModelFactory $taxRateModelFactory,
        ZipRangeDataObjectBuilder $zipRangeDataObjectBuilder
    ) {
        $this->taxRateDataObjectBuilder = $taxRateDataObjectBuilder;
        $this->taxRateModelFactory = $taxRateModelFactory;
        $this->zipRangeDataObjectBuilder = $zipRangeDataObjectBuilder;
    }

    /**
     * Convert a rate model to a TaxRate data object
     *
     * @param TaxRateModel $rateModel
     * @return TaxRateDataObject
     */
    public function createTaxRateDataObjectFromModel(TaxRateModel $rateModel)
    {
        $this->taxRateDataObjectBuilder->populateWithArray([]);
        if ($rateModel->getId()) {
            $this->taxRateDataObjectBuilder->setId($rateModel->getId());
        }
        if ($rateModel->getCountryId()) {
            $this->taxRateDataObjectBuilder->setCountryId($rateModel->getCountryId());
        }
        if ($rateModel->getRegionId()) {
            $this->taxRateDataObjectBuilder->setRegionId($rateModel->getRegionId());
        }
        if ($rateModel->getTaxPostcode()) {
            $this->taxRateDataObjectBuilder->setZip($rateModel->getTaxPostcode());
        }
        if ($rateModel->getCode()) {
            $this->taxRateDataObjectBuilder->setCode($rateModel->getCode());
        }
        if ($rateModel->getRate()) {
            $this->taxRateDataObjectBuilder->setPercentageRate($rateModel->getRate());
        }
        if ($rateModel->getZipIsRange()) {
            $zipRange = $this->zipRangeDataObjectBuilder->populateWithArray([])
                ->setFrom($rateModel->getZipFrom())
                ->setTo($rateModel->getZipTo())
                ->create();
            $this->taxRateDataObjectBuilder->setZipRange($zipRange);
        }
        return $this->taxRateDataObjectBuilder->create();
    }
}
