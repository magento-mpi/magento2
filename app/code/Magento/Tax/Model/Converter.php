<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Model;

use Magento\Tax\Model\ClassModel as TaxClassModel;
use Magento\Tax\Model\ClassModelFactory as TaxClassFactory;
use Magento\Tax\Service\V1\Data\TaxClass;
use Magento\Tax\Service\V1\Data\TaxClassBuilder;

/**
 * Tax class converter. Allows conversion between tax class model and tax class service data object.
 */
class Converter
{
    /**
     * @var TaxClassBuilder
     */
    protected $taxClassBuilder;

    /**
     * @var TaxClassFactory
     */
    protected $taxClassFactory;

    /**
     * Initialize dependencies.
     *
     * @param TaxClassBuilder $taxClassBuilder
     * @param TaxClassFactory $taxClassFactory
     */
    public function __construct(TaxClassBuilder $taxClassBuilder, TaxClassFactory $taxClassFactory)
    {
        $this->taxClassBuilder = $taxClassBuilder;
        $this->taxClassFactory = $taxClassFactory;
    }

    /**
     * Convert tax class model into tax class service data object.
     *
     * @param TaxClassModel $taxClassModel
     * @return TaxClass
     */
    public function createTaxClassData(TaxClassModel $taxClassModel)
    {
        $this->taxClassBuilder
            ->setId($taxClassModel->getId())
            ->setName($taxClassModel->getClassName())
            ->setType($taxClassModel->getClassType());
        return $this->taxClassBuilder;
    }

    /**
     * Convert tax class service data object into tax class model.
     *
     * @param TaxClass $taxClass
     * @return TaxClassModel
     */
    public function createTaxClassModel(TaxClass $taxClass)
    {
        /** @var TaxClassModel $taxClassModel */
        $taxClassModel = $this->taxClassFactory->create();
        $taxClassModel
            ->setId($taxClass->getId())
            ->setClassName($taxClass->getName())
            ->setClassType($taxClass->getType());
        return $taxClassModel;
    }
}
