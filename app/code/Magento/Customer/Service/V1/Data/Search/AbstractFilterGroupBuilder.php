<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Service\V1\Data\Search;

use Magento\Service\Data\AbstractObjectBuilder;
use Magento\Service\V1\Data\Filter;
use Magento\Service\V1\Data\FilterBuilder;

/**
 * Abstract Builder for AbstractFilterGroup DATA.
 */
abstract class AbstractFilterGroupBuilder extends AbstractObjectBuilder
{
    /**
     * Filer builder
     *
     * @var FilterBuilder
     */
    protected $_filterBuilder;

    /**
     * Constructor
     *
     * @param FilterBuilder $filterBuilder
     */
    public function __construct(FilterBuilder $filterBuilder)
    {
        parent::__construct();
        $this->_filterBuilder = $filterBuilder;
    }

    /**
     * Add filter
     *
     * @param \Magento\Service\V1\Data\Filter $filter
     * @return $this
     */
    public function addFilter(\Magento\Service\V1\Data\Filter $filter)
    {
        return $this->setFilterGroupData(AbstractFilterGroup::FILTERS, $filter);
    }

    /**
     * Set filters
     *
     * @param \Magento\Service\V1\Data\Filter[] $filters
     * @return $this
     */
    public function setFilters($filters)
    {
        return $this->_set(AbstractFilterGroup::FILTERS, $filters);
    }

    /**
     * Add And filter group
     *
     * @param \Magento\Customer\Service\V1\Data\Search\AndGroup $group
     * @return $this
     */
    public function addAndGroup(\Magento\Customer\Service\V1\Data\Search\AndGroup $group)
    {
        return $this->setFilterGroupData(AbstractFilterGroup::AND_GROUPS, $group);

    }

    /**
     * Add Or filter group
     *
     * @param \Magento\Customer\Service\V1\Data\Search\OrGroup $group
     * @return $this
     */
    public function addOrGroup(\Magento\Customer\Service\V1\Data\Search\OrGroup $group)
    {
        return $this->setFilterGroupData(AbstractFilterGroup::OR_GROUPS, $group);
    }

    /**
     * Set filter groups
     *
     * @param \Magento\Customer\Service\V1\Data\Search\AndGroup[] $groups
     * @return $this
     */
    public function setAndGroups($groups)
    {
        return $this->_set(AbstractFilterGroup::AND_GROUPS, $groups);
    }

    /**
     * Set filter groups
     *
     * @param \Magento\Customer\Service\V1\Data\Search\OrGroup[] $groups
     * @return $this
     */
    public function setOrGroups($groups)
    {
        return $this->_set(AbstractFilterGroup::OR_GROUPS, $groups);
    }

    /**
     * Set filter or group data
     *
     * @param string $key
     * @param Filter|AbstractFilterGroup $data
     * @return $this
     */
    private function setFilterGroupData($key, $data)
    {
        if (!isset($this->_data[$key])
            || !is_array($this->_data[$key])
        ) {
            $this->_data[$key] = [];
        }
        $this->_data[$key][] = $data;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function _setDataValues(array $data)
    {
        $newData = [];
        if (isset($data[AbstractFilterGroup::FILTERS])) {
            $filters = [];
            foreach ($data[AbstractFilterGroup::FILTERS] as $filter) {
                $filters[] = $this->_filterBuilder->populateWithArray($filter)->create();
            }
            $newData[AbstractFilterGroup::FILTERS] = $filters;
        }
        if (isset($data[AbstractFilterGroup::AND_GROUPS])) {
            $andGroups = [];
            foreach ($data[AbstractFilterGroup::AND_GROUPS] as $andGroup) {
                $andGroups[] = (new AndGroupBuilder(new FilterBuilder()))->populateWithArray($andGroup)->create();
            }
            $newData[AbstractFilterGroup::AND_GROUPS] = $andGroups;
        }
        if (isset($data[AbstractFilterGroup::OR_GROUPS])) {
            $orGroups = [];
            foreach ($data[AbstractFilterGroup::OR_GROUPS] as $orGroup) {
                $orGroups[] = (new OrGroupBuilder(new FilterBuilder()))->populateWithArray($orGroup)->create();
            }
            $newData[AbstractFilterGroup::OR_GROUPS] = $orGroups;
        }
        return parent::_setDataValues($newData);
    }
}
