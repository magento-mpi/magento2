<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Model;

use \Magento\Tax\Api\Data\TaxRuleInterface;
use \Magento\Tax\Api\TaxRuleRepositoryInterface;
use \Magento\Tax\Model\Calculation\TaxRuleConverter;
use \Magento\Tax\Model\Calculation\TaxRuleRegistry;
use \Magento\Tax\Model\Calculation\RuleFactory;
use \Magento\Framework\Api\Search\FilterGroup;
use \Magento\Tax\Model\Resource\Calculation\Rule\Collection;
use \Magento\Tax\Model\Resource\Calculation\Rule\CollectionFactory;
use \Magento\Framework\Api\SortOrder;
use \Magento\Tax\Api\Data\TaxRuleSearchResultsDataBuilder;
use \Magento\Framework\Api\SearchCriteria;

class TaxRuleRepository implements TaxRuleRepositoryInterface
{
    /**
     * @var TaxRuleRegistry
     */
    protected $taxRuleRegistry;

    /**
     * @var TaxRuleSearchResultsDataBuilder
     */
    protected $taxRuleSearchResultsBuilder;

    /**
     * @var RuleFactory
     */
    protected $taxRuleModelFactory;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    public function __construct(
        TaxRuleRegistry $taxRuleRegistry,
        TaxRuleConverter $taxRuleConverter,
        TaxRuleSearchResultsDataBuilder $searchResultsBuilder,
        RuleFactory $ruleFactory,
        CollectionFactory $collectionFactory
    ) {
        $this->taxRuleRegistry = $taxRuleRegistry;
        $this->taxRuleSearchResultsBuilder = $searchResultsBuilder;
        $this->taxRuleModelFactory = $ruleFactory;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function get($ruleId)
    {
        return $this->taxRuleRegistry->retrieveTaxRule($ruleId);
    }

    /**
     * {@inheritdoc}
     */
    public function save(TaxRuleInterface $rule)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function delete(TaxRuleInterface $rule)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function deleteByIdentifier($ruleId)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function search(\Magento\Framework\Api\SearchCriteria $searchCriteria)
    {
        $this->taxRuleSearchResultsBuilder->setSearchCriteria($searchCriteria);

        $fields = [];
        $collection = $this->collectionFactory->create();

        //Add filters from root filter group to the collection
        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $collection);
            foreach ($group->getFilters() as $filter) {
                $fields[] = $this->translateField($filter->getField());
            }
        }
        if ($fields) {
            if (in_array('cd.customer_tax_class_id', $fields) || in_array('cd.product_tax_class_id', $fields)) {
                $collection->joinCalculationData('cd');
            }
        }

        $this->taxRuleSearchResultsBuilder->setTotalCount($collection->getSize());
        $sortOrders = $searchCriteria->getSortOrders();
        /** @var SortOrder $sortOrder */
        if ($sortOrders) {
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder(
                    $this->translateField($sortOrder->getField()),
                    ($sortOrder->getDirection() == SearchCriteria::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        }
        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());

        $taxRules = [];

        /** @var TaxRuleInterface $taxRuleModel */
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
            $field = $this->translateField($filter->getField());
            $fields[] = $field;
            $conditions[] = [$condition => $filter->getValue()];
            switch ($field) {
                case 'rate.tax_calculation_rate_id':
                    $collection->joinCalculationData('rate');
                    break;

                case 'ctc.customer_tax_class_id':
                    $collection->joinCalculationData('ctc');
                    break;

                case 'ptc.product_tax_class_id':
                    $collection->joinCalculationData('ptc');
                    break;
            }
        }
        if ($fields) {
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
            case "id":
                return 'tax_calculation_rule_id';
            case 'tax_rate_ids':
                return 'tax_calculation_rate_id';
            case 'customer_tax_class_ids':
                return 'cd.customer_tax_class_id';
            case 'product_tax_class_ids':
                return 'cd.product_tax_class_id';
            case 'sort_order':
                return 'position';
            default:
                return $field;
        }
    }
}
