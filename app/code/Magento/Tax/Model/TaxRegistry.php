<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Model;

use Magento\Tax\Model\Calculation\RateFactory as TaxRateModelFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Tax\Model\Calculation\Rate as TaxRateModel;

class TaxRegistry
{
    /** @var  TaxRateModelFactory */
    private $taxModelRateFactory;
    /**
     * @var array
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
        $this->taxModelRateFactory = $taxModelRateFactory;
    }

    /**
     * Retrieve Customer Model from registry given an id
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
        $taxRateModel = $this->taxModelRateFactory->create()->load($taxRateId);
        if (!$taxRateModel->getId()) {
            // tax rate does not exist
            throw NoSuchEntityException::singleField('taxRateId', $taxRateId);
        }
        return $taxRateModel;
    }
}
