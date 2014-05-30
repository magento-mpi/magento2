<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Model\Calculation;

use Magento\Tax\Model\Calculation\Rule as TaxRuleModel;
use Magento\Tax\Model\Calculation\RuleFactory as TaxRuleModelFactory;
use Magento\Tax\Service\V1\Data\TaxRule as TaxRuleDataObject;
use Magento\Tax\Service\V1\Data\TaxRuleBuilder as TaxRuleDataObjectBuilder;

/**
 * Tax Rule Model converter.
 *
 * Converts a Tax Rule Model to a Data Object or vice versa.
 */
class TaxRuleConverter
{
    /**
     * @var TaxRuleDataObjectBuilder
     */
    protected $taxRuleDataObjectBuilder;

    /**
     * @var TaxRuleModelFactory
     */
    protected $taxRuleModelFactory;

    /**
     * @param TaxRuleDataObjectBuilder $taxRuleDataObjectBuilder
     * @param TaxRuleModelFactory $taxRuleModelFactory
     */
    public function __construct(
        TaxRuleDataObjectBuilder $taxRuleDataObjectBuilder,
        TaxRuleModelFactory $taxRuleModelFactory
    ) {
        $this->taxRuleDataObjectBuilder = $taxRuleDataObjectBuilder;
        $this->taxRuleModelFactory = $taxRuleModelFactory;
    }

    /**
     * Convert a rate model to a TaxRate data object
     *
     * @param TaxRuleModel $ruleModel
     * @return TaxRuleDataObject
     */
    public function createTaxRuleDataObjectFromModel(TaxRuleModel $ruleModel)
    {
        $this->taxRuleDataObjectBuilder->populateWithArray([]);
        if ($ruleModel->getId()) {
            $this->taxRuleDataObjectBuilder->setId($ruleModel->getId());
        }
        if ($ruleModel->getCode()) {
            $this->taxRuleDataObjectBuilder->setCode($ruleModel->getCode());
        }
        if ($ruleModel->getTaxCustomerClass()) {
            $this->taxRuleDataObjectBuilder->setCustomerTaxClassIds($ruleModel->getTaxCustomerClass());
        }
        if ($ruleModel->getTaxProductClass()) {
            $this->taxRuleDataObjectBuilder->setProductTaxClassIds($ruleModel->getTaxProductClass());
        }
        if ($ruleModel->getTaxRate()) {
            $this->taxRuleDataObjectBuilder->setTaxRateIds($ruleModel->getTaxRate());
        }
        if ($ruleModel->getPriority()) {
            $this->taxRuleDataObjectBuilder->setPrioritiy($ruleModel->getPriority());
        }
        if ($ruleModel->getPosition()) {
            $this->taxRuleDataObjectBuilder->setSortOrder($ruleModel->getPosition());
        }
        return $this->taxRuleDataObjectBuilder->create();
    }

    /**
     * Convert a tax rule data object to tax rule model
     *
     * @param TaxRuleDataObject $taxRule
     * @return TaxRuleModel
     */
    public function createTaxRuleModel(TaxRuleDataObject $taxRuleDataObject)
    {
        $taxRuleModel = $this->taxRuleModelFactory->create();
        $ruleId = $taxRuleDataObject->getId();
        if ($ruleId) {
            $taxRuleModel->setId($ruleId);
        }
        $taxRuleModel->setTaxCustomerClass($taxRuleDataObject->getCustomerTaxClassIds());
        $taxRuleModel->setTaxProductClass($taxRuleDataObject->getProductTaxClassIds());
        $taxRuleModel->setTaxRate($taxRuleDataObject->getTaxRateIds());
        $taxRuleModel->setCode($taxRuleDataObject->getCode());
        $taxRuleModel->setPrioritiy($taxRuleDataObject->getPriority());
        $taxRuleModel->setPosition($taxRuleDataObject->getSortOrder());
        return $taxRuleModel;
    }
}
