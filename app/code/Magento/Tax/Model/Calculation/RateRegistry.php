<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Model\Calculation;

use Magento\Tax\Model\Calculation\RateFactory as TaxRateModelFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Tax\Model\Calculation\Rate as TaxRateModel;

class RateRegistry
{
    /**
     * Tax rate model factory
     *
     * @var  TaxRateModelFactory
     */
    private $taxRateModelFactory;

    /**
     * Tax rate models
     *
     * @var TaxRateModel[]
     */
    private $taxRateRegistryById = [];

    /**
     * Constructor
     *
     * @param TaxRateModelFactory $taxModelRateFactory
     */
    public function __construct(
        TaxRateModelFactory $taxModelRateFactory
    ) {
        $this->taxRateModelFactory = $taxModelRateFactory;
    }

    /**
     * Register TaxRate Model to registry
     *
     * @param TaxRateModel $taxRateModel
     * @return void
     */
    public function registerTaxRate(TaxRateModel $taxRateModel)
    {
        $this->taxRateRegistryById[$taxRateModel->getId()] = $taxRateModel;
    }

    /**
     * Retrieve TaxRate Model from registry given an id
     *
     * @param int $taxRateId
     * @return TaxRateModel
     * @throws NoSuchEntityException
     */
    public function retrieveTaxRate($taxRateId)
    {
        if (isset($this->taxRateRegistryById[$taxRateId])) {
            return $this->taxRateRegistryById[$taxRateId];
        }
        /** @var TaxRateModel $taxRateModel */
        $taxRateModel = $this->taxRateModelFactory->create()->load($taxRateId);
        if (!$taxRateModel->getId()) {
            // tax rate does not exist
            throw NoSuchEntityException::singleField('taxRateId', $taxRateId);
        }
        $this->taxRateRegistryById[$taxRateModel->getId()] = $taxRateModel;
        return $taxRateModel;
    }
}
