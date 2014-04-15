<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Service\V1\Data\Search;

use Magento\Service\Data\AbstractObjectBuilder;
use Magento\Service\V1\Data\FilterBuilder;

/**
 * Builder for FilterGroup Data.
 */
class FilterGroupBuilder extends AbstractObjectBuilder
{
    /**
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
        $this->_data[FilterGroup::FILTERS][] = $filter;
        return $this;
    }

    /**
     * Set filters
     *
     * @param \Magento\Service\V1\Data\Filter[] $filters
     * @return $this
     */
    public function setFilters($filters)
    {
        return $this->_set(FilterGroup::FILTERS, $filters);
    }

    /**
     * {@inheritdoc}
     */
    protected function _setDataValues(array $data)
    {
        if (isset($data[FilterGroup::FILTERS])) {
            $filters = [];
            foreach ($data[FilterGroup::FILTERS] as $filter) {
                $filters[] = $this->_filterBuilder->populateWithArray($filter)->create();
            }
            $data[FilterGroup::FILTERS] = $filters;
        }
        return parent::_setDataValues($data);
    }
}
