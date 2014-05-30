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

class TaxRuleService implements TaxRuleServiceInterface
{
    /**
     * Builder for TaxRule data objects.
     *
     * @var Data\TaxRuleBuilder
     */
    protected $taxRuleBuilder;

    /**
     * @var \Magento\Tax\Model\Calculation\TaxRuleRegistry
     */
    protected $taxRuleRegistry;

    /**
     * @var \Magento\Tax\
     */
    /**
     * @param TaxRuleBuilder $taxRuleBuilder
     * @param TaxRuleConverter $taxRuleConverter
     * @param TaxRuleRegistry $taxRuleRegistry
     */
    public function __construct(
        TaxRuleBuilder $taxRuleBuilder,
        TaxRuleConverter $converter,
        TaxRuleRegistry $taxRuleRegistry
    )
    {
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
        // TODO: Implement updateTaxRule() method.
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
        $taxRuleModel = $this->taxRuleRegistry->retrieve($taxRuleId);
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
     * @throws InputException
     * @throws \Magento\Framework\Model\Exception
     * @return RuleModel
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
     * Validate tax rate
     *
     * @param TaxRule $taxRule
     * @throws InputException
     * @return void
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
