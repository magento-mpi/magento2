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

    /**
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
    public function createTaxRule(TaxRule $rule)
    {
        $taxRuleModel = $this->saveTaxRule($rule);
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
        $ruleModel = $this->taxRuleRegistry->retrieveTaxRule($ruleId);
        $ruleModel->delete();
        $this->taxRuleRegistry->removeTaxRule($ruleId);
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getTaxRule($ruleId)
    {
        $taxRuleModel = $this->taxRuleRegistry->retrieveTaxRule($ruleId);
        return $this->converter->createTaxRuleDataObjectFromModel($taxRuleModel);
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
     * @param TaxRule $rule
     * @return TaxRuleModel
     * @throws InputException
     * @throws \Magento\Framework\Model\Exception
     */
    protected function saveTaxRule(TaxRule $rule)
    {
        $this->validate($rule);
        $taxRuleModel = $this->converter->createTaxRuleModel($rule);
        $taxRuleModel->save();
        $this->taxRuleRegistry->registerTaxRule($taxRuleModel);
        return $taxRuleModel;
    }

    /**
     * Validate tax rule
     *
     * @param TaxRule $rule
     * @return void
     * @throws InputException
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    private function validate(TaxRule $rule)
    {
        $exception = new InputException();

        // SortOrder is required and must be 0 or greater
        if (!\Zend_Validate::is(trim($rule->getSortOrder()), 'NotEmpty')) {
            $exception->addError(InputException::REQUIRED_FIELD, ['fieldName' => TaxRule::SORT_ORDER]);
        }
        if (!\Zend_Validate::is(trim($rule->getSortOrder()), 'GreaterThan', [-1])) {
            $exception->addError(
                InputException::INVALID_FIELD_MIN_VALUE,
                ['fieldName' => TaxRule::SORT_ORDER, 'value' => $rule->getSortOrder(), 'minValue' => 0]
            );
        }

        // Priority is required and must be 0 or greater
        if (!\Zend_Validate::is(trim($rule->getPriority()), 'NotEmpty')) {
            $exception->addError(InputException::REQUIRED_FIELD, ['fieldName' => TaxRule::PRIORITY]);
        }
        if (!\Zend_Validate::is(trim($rule->getPriority()), 'GreaterThan', [-1])) {
            $exception->addError(
                InputException::INVALID_FIELD_MIN_VALUE,
                ['fieldName' => TaxRule::PRIORITY, 'value' => $rule->getPriority(), 'minValue' => 0]
            );
        }

        // Code is required
        if (!\Zend_Validate::is(trim($rule->getCode()), 'NotEmpty')) {
            $exception->addError(InputException::REQUIRED_FIELD, ['fieldName' => TaxRule::CODE]);
        }
        // customer tax class ids is required
        if (($rule->getCustomerTaxClassIds() === null) || !$rule->getCustomerTaxClassIds()) {
            $exception->addError(InputException::REQUIRED_FIELD, ['fieldName' => TaxRule::CUSTOMER_TAX_CLASS_IDS]);
        }
        // product tax class ids is required
        if (($rule->getProductTaxClassIds() === null) || !$rule->getProductTaxClassIds()) {
            $exception->addError(InputException::REQUIRED_FIELD, ['fieldName' => TaxRule::PRODUCT_TAX_CLASS_IDS]);
        }
        // tax rate ids is required
        if (($rule->getTaxRateIds() === null) || !$rule->getTaxRateIds()) {
            $exception->addError(InputException::REQUIRED_FIELD, ['fieldName' => TaxRule::TAX_RATE_IDS]);
        }

        // throw exception if errors were found
        if ($exception->wasErrorAdded()) {
            throw $exception;
        }
    }
}
