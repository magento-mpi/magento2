<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Service;

use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\Exception;
use Magento\Framework\Service\V1\Data\Filter;
use Magento\Framework\Service\V1\Data\FilterBuilder;
use Magento\Framework\Service\V1\Data\SearchCriteria;
use Magento\Framework\Service\V1\Data\SearchCriteriaBuilder;
use Magento\Framework\Service\V1\Data\SortOrderBuilder;

/**
 * Base for service collections
 */
abstract class AbstractServiceCollection extends \Magento\Framework\Data\Collection
{
    /**
     * Filters on specific fields
     *
     * Each filter has the following structure
     * <pre>
     * [
     *     'field'     => $field,
     *     'condition' => $condition,
     * ]
     * </pre>
     * @see addFieldToFilter() for more information on conditions
     *
     * @var array
     */
    protected $fieldFilters = array();

    /**
     * @var FilterBuilder
     */
    protected $filterBuilder;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var \Magento\Framework\Service\V1\Data\SortOrderBuilder
     */
    protected $sortOrderBuilder;

    /**
     * @param EntityFactoryInterface $entityFactory
     * @param FilterBuilder $filterBuilder
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Framework\Service\V1\Data\SortOrderBuilder $sortOrderBuilder
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        FilterBuilder $filterBuilder,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SortOrderBuilder $sortOrderBuilder
    ) {
        parent::__construct($entityFactory);
        $this->filterBuilder = $filterBuilder;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;
    }

    /**
     * Add field filter to collection
     *
     * If $condition integer or string - exact value will be filtered ('eq' condition)
     *
     * If $condition is array - one of the following structures is expected:
     * <pre>
     * - ["from" => $fromValue, "to" => $toValue]
     * - ["eq" => $equalValue]
     * - ["neq" => $notEqualValue]
     * - ["like" => $likeValue]
     * - ["in" => [$inValues]]
     * - ["nin" => [$notInValues]]
     * - ["notnull" => $valueIsNotNull]
     * - ["null" => $valueIsNull]
     * - ["moreq" => $moreOrEqualValue]
     * - ["gt" => $greaterValue]
     * - ["lt" => $lessValue]
     * - ["gteq" => $greaterOrEqualValue]
     * - ["lteq" => $lessOrEqualValue]
     * - ["finset" => $valueInSet]
     * - ["regexp" => $regularExpression]
     * - ["seq" => $stringValue]
     * - ["sneq" => $stringValue]
     * </pre>
     *
     * If non matched - sequential parallel arrays are expected and OR conditions
     * will be built using above mentioned structure.
     *
     * Example:
     * <pre>
     * $field = ['age', 'name'];
     * $condition = [42, ['like' => 'Mage']];
     * </pre>
     * The above would find where age equal to 42 OR name like %Mage%.
     *
     * @param string|array $field
     * @param string|int|array $condition
     * @throws Exception if some error in the input could be detected.
     * @return $this
     */
    public function addFieldToFilter($field, $condition)
    {
        if (is_array($field) && count($field) != count($condition)) {
            throw new Exception('When passing in a field array there must be a matching condition array.');
        }
        $this->fieldFilters[] = array('field' => $field, 'condition' => $condition);
        return $this;
    }

    /**
     * Creates a search criteria DTO based on the array of field filters.
     *
     * @return SearchCriteria
     */
    protected function getSearchCriteria()
    {
        foreach ($this->fieldFilters as $filter) {
            // array of fields, put filters in array to use 'or' group
            /** @var Filter[] $filterGroup */
            $filterGroup = array();
            if (!is_array($filter['field'])) {
                // just one field
                $filterGroup = [$this->createFilterData($filter['field'], $filter['condition'])];
            } else {
                foreach ($filter['field'] as $index => $field) {
                    $filterGroup[] = $this->createFilterData($field, $filter['condition'][$index]);
                }
            }
            $this->searchCriteriaBuilder->addFilter($filterGroup);
        }
        foreach ($this->_orders as $field => $direction) {
            /**
             * @var \Magento\Framework\Service\V1\Data\SortOrder $sortOrder
             */
            /**@var string $direction */
            $direction = ($direction == 'ASC') ? SearchCriteria::SORT_ASC : SearchCriteria::SORT_DESC;
            $sortOrder = $this->sortOrderBuilder->setField($field)->setDirection($direction)->create();
            $this->searchCriteriaBuilder->addSortOrder($sortOrder);
        }
        $this->searchCriteriaBuilder->setCurrentPage($this->_curPage);
        $this->searchCriteriaBuilder->setPageSize($this->_pageSize);
        return $this->searchCriteriaBuilder->create();
    }

    /**
     * Creates a filter DTO for given field/condition
     *
     * @param string $field Field for new filter
     * @param string|array $condition Condition for new filter.
     * @return Filter
     */
    protected function createFilterData($field, $condition)
    {
        $this->filterBuilder->setField($field);

        if (is_array($condition)) {
            $this->filterBuilder->setValue(reset($condition));
            $this->filterBuilder->setConditionType(key($condition));
        } else {
            // not an array, just use eq as condition type and given value
            $this->filterBuilder->setConditionType('eq');
            $this->filterBuilder->setValue($condition);
        }
        return $this->filterBuilder->create();
    }
}
