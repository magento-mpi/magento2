<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Model;

use Magento\Tax\Model\ClassModelFactory as TaxClassModelFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Tax\Model\ClassModel as TaxClassModel;
use Magento\Tax\Service\V1\Data\TaxClass;

class ClassModelRegistry
{
    /**
     * Tax class model factory
     *
     * @var  TaxClassModelFactory
     */
    private $taxClassModelFactory;

    /**
     * Tax class models
     *
     * @var TaxClassModel[]
     */
    private $taxClassRegistryById = [];

    /**
     * @param TaxClassModelFactory $taxClassModelFactory
     */
    public function __construct(
        TaxClassModelFactory $taxClassModelFactory
    ) {
        $this->taxClassModelFactory = $taxClassModelFactory;
    }

    /**
     * Register TaxClassModel Model to registry
     *
     * @param TaxClassModel $taxClassModel
     * @return void
     */
    public function registerTaxClass(TaxClassModel $taxClassModel)
    {
        $this->taxClassRegistryById[$taxClassModel->getId()] = $taxClassModel;
    }

    /**
     * Retrieve ClassModel Model from registry given an id
     *
     * @param int $taxClassId
     * @return TaxClassModel
     * @throws NoSuchEntityException
     */
    public function retrieve($taxClassId)
    {
        if (isset($this->taxClassRegistryById[$taxClassId])) {
            return $this->taxClassRegistryById[$taxClassId];
        }
        /** @var TaxClassModel $taxClassModel */
        $taxClassModel = $this->taxClassModelFactory->create()->load($taxClassId);
        if (!$taxClassModel->getId()) {
            // tax class does not exist
            throw NoSuchEntityException::singleField(TaxClass::KEY_ID, $taxClassId);
        }
        $this->taxClassRegistryById[$taxClassModel->getId()] = $taxClassModel;
        return $taxClassModel;
    }

    /**
     * Remove an instance of the TaxClass Model from the registry
     *
     * @param int $taxClassId
     * @return void
     */
    public function remove($taxClassId)
    {
        unset($this->taxClassRegistryById[$taxClassId]);
    }
}
