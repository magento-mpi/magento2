<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Service\V1;

use Magento\Framework\Service\V1\Data\SearchCriteria;
use Magento\Framework\Service\V1\Data\Search\FilterGroup;
use Magento\Tax\Model\Calculation\TaxRuleConverter;
use Magento\Tax\Service\V1\Data\TaxRule;
use Magento\Tax\Service\V1\Data\TaxRuleBuilder;
use Magento\Tax\Model\Calculation\TaxRuleRegistry;
use Magento\Framework\Exception\InputException;
use Magento\Tax\Model\Calculation\Rule as TaxRuleModel;
use Magento\Tax\Model\Calculation\RuleFactory as TaxRuleModelFactory;
use Magento\Tax\Model\Resource\Calculation\Rule\Collection;

/**
 * TaxRuleService implementation.
 */
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
     * @var Data\TaxRuleSearchResultsBuilder
     */
    protected $taxRuleSearchResultsBuilder;

    /**
     * @var TaxRuleModelFactory
     */
    protected $taxRuleModelFactory;

    /**
     * @param TaxRuleBuilder $taxRuleBuilder
     * @param TaxRuleConverter $converter
     * @param TaxRuleRegistry $taxRuleRegistry
     * @param Data\TaxRuleSearchResultsBuilder $taxRuleSearchResultsBuilder
     * @param TaxRuleModelFactory $taxRuleModelFactory
     */
    public function __construct(
        TaxRuleBuilder $taxRuleBuilder,
        TaxRuleConverter $converter,
        TaxRuleRegistry $taxRuleRegistry,
        Data\TaxRuleSearchResultsBuilder $taxRuleSearchResultsBuilder,
        TaxRuleModelFactory $taxRuleModelFactory
    ) {
        $this->taxRuleBuilder = $taxRuleBuilder;
        $this->converter = $converter;
        $this->taxRuleRegistry = $taxRuleRegistry;
        $this->taxRuleSearchResultsBuilder = $taxRuleSearchResultsBuilder;
        $this->taxRuleModelFactory = $taxRuleModelFactory;
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
        $ruleModel = $this->taxRuleRegistry->retrieveTaxRule($ruleId);
        $ruleModel->delete();
        $this->taxRuleRegistry->removeTaxRule($ruleId);
        return true;
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
    public function searchTaxRules(SearchCriteria $searchCriteria)
    {
        $this->taxRuleSearchResultsBuilder->setSearchCriteria($searchCriteria);
        $collection = $this->taxRuleModelFactory->create()->getCollection();

        //Add filters from root filter group to the collection
        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $collection);
        }
        $this->taxRuleSearchResultsBuilder->setTotalCount($collection->getSize());
        $sortOrders = $searchCriteria->getSortOrders();
        if ($sortOrders) {
            foreach ($sortOrders as $field => $direction) {
                $field = $this->translateField($field);
                $collection->addOrder($field, $direction == SearchCriteria::SORT_ASC ? 'ASC' : 'DESC');
            }
        }
        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());

        $taxRules = [];

        /** @var TaxRuleModel $taxRuleModel */
        foreach ($collection as $taxRuleModel) {
            $taxRule = $this->converter->createTaxRuleDataObjectFromModel($taxRuleModel);
            $taxRules[] = $taxRule;
        }
        $this->taxRuleSearchResultsBuilder->setItems($taxRules);
        return $this->taxRuleSearchResultsBuilder->create();
    }

    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * @param FilterGroup $filterGroup
     * @param Collection $collection
     * @return void
     * @throws \Magento\Framework\Exception\InputException
     */
    protected function addFilterGroupToCollection(FilterGroup $filterGroup, Collection $collection)
    {
        $fields = [];
        $conditions = [];
        foreach ($filterGroup->getFilters() as $filter) {
            $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
            $fields[] = $this->translateField($filter->getField());
            $conditions[] = [$condition => $filter->getValue()];
        }
        if ($fields) {
            if (in_array('cd.customer_tax_class_id', $fields) || in_array('cd.product_tax_class_id', $fields) ) {
                $collection->joinCalculationData('cd');
            }
            $collection->addFieldToFilter($fields, $conditions);
        }
    }

    /**
     * Translates a field name to a DB column name for use in collection queries.
     *
     * @param string $field a field name that should be translated to a DB column name.
     * @return string
     */
    protected function translateField($field)
    {
        switch ($field) {
            case TaxRule::ID:
                return 'tax_calculation_rule_id';
            case TaxRule::TAX_RATE_IDS:
                return 'tax_calculation_rate_id';
            case TaxRule::CUSTOMER_TAX_CLASS_IDS:
                return 'cd.customer_tax_class_id';
            case TaxRule::PRODUCT_TAX_CLASS_IDS:
                return 'cd.product_tax_class_id';
            case TaxRule::SORT_ORDER:
                return 'position';
            default:
                return $field;
        }
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

        // SortOrder is required and must be 0 or greater
        if (!\Zend_Validate::is(trim($taxRule->getSortOrder()), 'NotEmpty')) {
            $exception->addError(InputException::REQUIRED_FIELD, ['fieldName' => TaxRule::SORT_ORDER]);
        }
        if (!\Zend_Validate::is(trim($taxRule->getSortOrder()), 'GreaterThan', [-1])) {
            $exception->addError(
                InputException::INVALID_FIELD_MIN_VALUE,
                ['fieldName' => TaxRule::SORT_ORDER, 'value' => $taxRule->getSortOrder(), 'minValue' => 0]
            );
        }

        // Priority is required and must be 0 or greater
        if (!\Zend_Validate::is(trim($taxRule->getPriority()), 'NotEmpty')) {
            $exception->addError(InputException::REQUIRED_FIELD, ['fieldName' => TaxRule::PRIORITY]);
        }
        if (!\Zend_Validate::is(trim($taxRule->getPriority()), 'GreaterThan', [-1])) {
            $exception->addError(
                InputException::INVALID_FIELD_MIN_VALUE,
                ['fieldName' => TaxRule::PRIORITY, 'value' => $taxRule->getPriority(), 'minValue' => 0]
            );
        }

        // Code is required
        if (!\Zend_Validate::is(trim($taxRule->getCode()), 'NotEmpty')) {
            $exception->addError(InputException::REQUIRED_FIELD, ['fieldName' => TaxRule::CODE]);
        }
        // customer tax class ids is required
        if (($taxRule->getCustomerTaxClassIds() === null) || !$taxRule->getCustomerTaxClassIds()) {
            $exception->addError(InputException::REQUIRED_FIELD, ['fieldName' => TaxRule::CUSTOMER_TAX_CLASS_IDS]);
        }
        // product tax class ids is required
        if (($taxRule->getProductTaxClassIds() === null) || !$taxRule->getProductTaxClassIds()) {
            $exception->addError(InputException::REQUIRED_FIELD, ['fieldName' => TaxRule::PRODUCT_TAX_CLASS_IDS]);
        }
        // tax rate ids is required
        if (($taxRule->getTaxRateIds() === null) || !$taxRule->getTaxRateIds()) {
            $exception->addError(InputException::REQUIRED_FIELD, ['fieldName' => TaxRule::TAX_RATE_IDS]);
        }

        // throw exception if errors were found
        if ($exception->wasErrorAdded()) {
            throw $exception;
        }
    }
}
