<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Api\Search;

use Magento\Framework\Api\ExtensibleObjectBuilder;
use Magento\Framework\Api\AttributeDataBuilder;
use Magento\Framework\Api\MetadataServiceInterface;
use Magento\Framework\Api\FilterBuilder;

/**
 * Builder for FilterGroup Data.
 */
class FilterGroupBuilder extends ExtensibleObjectBuilder
{
    /**
     * @var FilterBuilder
     */
    protected $_filterBuilder;

    /**
     * @param \Magento\Framework\Api\ObjectFactory $objectFactory
     * @param AttributeDataBuilder $valueBuilder
     * @param MetadataServiceInterface $metadataService
     * @param FilterBuilder $filterBuilder
     */
    public function __construct(
        \Magento\Framework\Api\ObjectFactory $objectFactory,
        AttributeDataBuilder $valueBuilder,
        MetadataServiceInterface $metadataService,
        FilterBuilder $filterBuilder
    ) {
        parent::__construct($objectFactory, $valueBuilder, $metadataService);
        $this->_filterBuilder = $filterBuilder;
    }

    /**
     * Add filter
     *
     * @param \Magento\Framework\Api\Filter $filter
     * @return $this
     */
    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        $this->data[FilterGroup::FILTERS][] = $filter;
        return $this;
    }

    /**
     * Set filters
     *
     * @param \Magento\Framework\Api\Filter[] $filters
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
