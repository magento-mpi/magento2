<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Service\V1;

use Magento\Framework\Service\V1\Data\SearchCriteria;
use Magento\Tax\Model\Calculation\TaxRuleConverter;
use Magento\Tax\Service\V1\Data\TaxRule;
use Magento\Tax\Service\V1\Data\TaxRuleBuilder;
use Magento\Tax\Model\Calculation\TaxRuleRegistry;
use Magento\Framework\Exception\InputException;
use Magento\Tax\Model\Calculation\Rule as TaxRuleModel;

class TaxRuleService implements TaxRuleServiceInterface
{
    /**
     * Builder for TaxRule data objects.
     *
     * @var TaxRuleBuilder
     */
    protected $taxRuleBuilder;

    /*
     * @var TaxRuleConverter
     */
    protected $converter;

    /**
     * @var TaxRuleRegistry
     */
    protected $taxRuleRegistry;

    /**
     * @param TaxRuleBuilder $taxRuleBuilder
     * @param TaxRuleConverter $converter
     * @param TaxRuleRegistry $taxRuleRegistry
     */
    public function __construct(
        TaxRuleBuilder $taxRuleBuilder,
        TaxRuleConverter $converter,
        TaxRuleRegistry $taxRuleRegistry
    ) {
        $this->taxRuleBuilder = $taxRuleBuilder;
        $this->converter = $converter;
        $this->taxRuleRegistry = $taxRuleRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function createTaxRule(TaxRule $taxRule)
    {
        $taxRuleModel = $this->saveTaxRule($taxRule);
        return $this->converter->createTaxRuleDataObjectFromModel($taxRuleModel);
    }

    /**
     * {@inheritdoc}
     */
    public function updateTaxRule(TaxRule $rule)
    {
        // Only update existing tax rules
        $this->taxRuleRegistry->retrieveTaxRule($rule->getId());

        $this->saveTaxRule($rule);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteTaxRule($ruleId)
    {
        // TODO: Implement deleteTaxRule() method.
    }

    /**
     * {@inheritdoc}
     */
    public function getTaxRule($taxRuleId)
    {
        $taxRuleModel = $this->taxRuleRegistry->retrieveTaxRule($taxRuleId);
        return $this->converter->createTaxRuleDataObjectFromModel($taxRuleModel);
    }

    /**
     * {@inheritdoc}
     */
    public function getCalculateOffSubtotalOnly()
    {
        // TODO: Implement getCalculateOffSubtotalOnly() method.
    }

    /**
     * {@inheritdoc}
     */
    public function searchTaxRules(SearchCriteria $searchCriteria)
    {
        // TODO: Implement searchTaxRules() method.
    }

    /**
     * Save Tax Rule
     *
     * @param TaxRule $taxRule
     * @return TaxRuleModel
     * @throws InputException
     * @throws \Magento\Framework\Model\Exception
     */
    protected function saveTaxRule(TaxRule $taxRule)
    {
        $this->validate($taxRule);
        $taxRuleModel = $this->converter->createTaxRuleModel($taxRule);
        $taxRuleModel->save();
        $this->taxRuleRegistry->registerTaxRule($taxRuleModel);
        return $taxRuleModel;
    }

    /**
     * Validate tax rule
     *
     * @param TaxRule $taxRule
     * @return void
     * @throws InputException
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    private function validate(TaxRule $taxRule)
    {
        $exception = new InputException();
        // TODO: validation
        if ($exception->wasErrorAdded()) {
            throw $exception;
        }
    }
}
